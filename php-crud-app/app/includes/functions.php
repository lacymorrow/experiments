<?php
#
# Portable PHP password hashing framework.
#
# Version 0.3 / genuine.
#
# Written by Solar Designer <solar at openwall.com> in 2004-2006 and placed in
# the public domain.  Revised in subsequent years, still public domain.
#
# There's absolutely no warranty.
#
# The homepage URL for this framework is:
#
#	http://www.openwall.com/phpass/
#
# Please be sure to update the Version line if you edit this file in any way.
# It is suggested that you leave the main version number intact, but indicate
# your project name (after the slash) and add your own revision information.
#
# Please do not change the "private" password hashing method implemented in
# here, thereby making your hashes incompatible.  However, if you must, please
# change the hash type identifier (the "$P$") to something different.
#
# Obviously, since this code is in the public domain, the above are not
# requirements (there can be none), but merely suggestions.
#
class PasswordHash {
	var $itoa64;
	var $iteration_count_log2;
	var $portable_hashes;
	var $random_state;

	function PasswordHash($iteration_count_log2, $portable_hashes)
	{
		$this->itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31)
			$iteration_count_log2 = 8;
		$this->iteration_count_log2 = $iteration_count_log2;

		$this->portable_hashes = $portable_hashes;

		$this->random_state = microtime();
		if (function_exists('getmypid'))
			$this->random_state .= getmypid();
	}

	function get_random_bytes($count)
	{
		$output = '';
		if (is_readable('/dev/urandom') &&
		    ($fh = @fopen('/dev/urandom', 'rb'))) {
			$output = fread($fh, $count);
			fclose($fh);
		}

		if (strlen($output) < $count) {
			$output = '';
			for ($i = 0; $i < $count; $i += 16) {
				$this->random_state =
				    md5(microtime() . $this->random_state);
				$output .=
				    pack('H*', md5($this->random_state));
			}
			$output = substr($output, 0, $count);
		}

		return $output;
	}

	function encode64($input, $count)
	{
		$output = '';
		$i = 0;
		do {
			$value = ord($input[$i++]);
			$output .= $this->itoa64[$value & 0x3f];
			if ($i < $count)
				$value |= ord($input[$i]) << 8;
			$output .= $this->itoa64[($value >> 6) & 0x3f];
			if ($i++ >= $count)
				break;
			if ($i < $count)
				$value |= ord($input[$i]) << 16;
			$output .= $this->itoa64[($value >> 12) & 0x3f];
			if ($i++ >= $count)
				break;
			$output .= $this->itoa64[($value >> 18) & 0x3f];
		} while ($i < $count);

		return $output;
	}

	function gensalt_private($input)
	{
		$output = '$P$';
		$output .= $this->itoa64[min($this->iteration_count_log2 +
			((PHP_VERSION >= '5') ? 5 : 3), 30)];
		$output .= $this->encode64($input, 6);

		return $output;
	}

	function crypt_private($password, $setting)
	{
		$output = '*0';
		if (substr($setting, 0, 2) == $output)
			$output = '*1';

		$id = substr($setting, 0, 3);
		# We use "$P$", phpBB3 uses "$H$" for the same thing
		if ($id != '$P$' && $id != '$H$')
			return $output;

		$count_log2 = strpos($this->itoa64, $setting[3]);
		if ($count_log2 < 7 || $count_log2 > 30)
			return $output;

		$count = 1 << $count_log2;

		$salt = substr($setting, 4, 8);
		if (strlen($salt) != 8)
			return $output;

		# We're kind of forced to use MD5 here since it's the only
		# cryptographic primitive available in all versions of PHP
		# currently in use.  To implement our own low-level crypto
		# in PHP would result in much worse performance and
		# consequently in lower iteration counts and hashes that are
		# quicker to crack (by non-PHP code).
		if (PHP_VERSION >= '5') {
			$hash = md5($salt . $password, TRUE);
			do {
				$hash = md5($hash . $password, TRUE);
			} while (--$count);
		} else {
			$hash = pack('H*', md5($salt . $password));
			do {
				$hash = pack('H*', md5($hash . $password));
			} while (--$count);
		}

		$output = substr($setting, 0, 12);
		$output .= $this->encode64($hash, 16);

		return $output;
	}

	function gensalt_extended($input)
	{
		$count_log2 = min($this->iteration_count_log2 + 8, 24);
		# This should be odd to not reveal weak DES keys, and the
		# maximum valid value is (2**24 - 1) which is odd anyway.
		$count = (1 << $count_log2) - 1;

		$output = '_';
		$output .= $this->itoa64[$count & 0x3f];
		$output .= $this->itoa64[($count >> 6) & 0x3f];
		$output .= $this->itoa64[($count >> 12) & 0x3f];
		$output .= $this->itoa64[($count >> 18) & 0x3f];

		$output .= $this->encode64($input, 3);

		return $output;
	}

	function gensalt_blowfish($input)
	{
		# This one needs to use a different order of characters and a
		# different encoding scheme from the one in encode64() above.
		# We care because the last character in our encoded string will
		# only represent 2 bits.  While two known implementations of
		# bcrypt will happily accept and correct a salt string which
		# has the 4 unused bits set to non-zero, we do not want to take
		# chances and we also do not want to waste an additional byte
		# of entropy.
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$output = '$2a$';
		$output .= chr(ord('0') + $this->iteration_count_log2 / 10);
		$output .= chr(ord('0') + $this->iteration_count_log2 % 10);
		$output .= '$';

		$i = 0;
		do {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16) {
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);

		return $output;
	}

	function HashPassword($password)
	{
		$random = '';

		if (CRYPT_BLOWFISH == 1 && !$this->portable_hashes) {
			$random = $this->get_random_bytes(16);
			$hash =
			    crypt($password, $this->gensalt_blowfish($random));
			if (strlen($hash) == 60)
				return $hash;
		}

		if (CRYPT_EXT_DES == 1 && !$this->portable_hashes) {
			if (strlen($random) < 3)
				$random = $this->get_random_bytes(3);
			$hash =
			    crypt($password, $this->gensalt_extended($random));
			if (strlen($hash) == 20)
				return $hash;
		}

		if (strlen($random) < 6)
			$random = $this->get_random_bytes(6);
		$hash =
		    $this->crypt_private($password,
		    $this->gensalt_private($random));
		if (strlen($hash) == 34)
			return $hash;

		# Returning '*' on error is safe here, but would _not_ be safe
		# in a crypt(3)-like function used _both_ for generating new
		# hashes and for validating passwords against existing hashes.
		return '*';
	}

	function CheckPassword($password, $stored_hash)
	{
		$hash = $this->crypt_private($password, $stored_hash);
		if ($hash[0] == '*')
			$hash = crypt($password, $stored_hash);

		return $hash == $stored_hash;
	}
}

?>
<?php
require_once('flintstone.class.php');
function db_add_instrument($type, $location){
    try {
        // Set options
        $options = array('dir' => '../db');
        // Load the databases
        $instruments = Flintstone::load('instruments', $options);
        
        $keys = $instruments->getKeys(); // returns array('bob', 'joe', ...)
        $id = count($keys)+1;
        // Insert User
        $instruments->set($id, array('type' => $type, 'id' => $id, 'lid' => $location, 'cid' => ''));
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured:'.$e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_add_school($school){
    try {
        // Set options
        $options = array('dir' => '../db');
        // Load the databases
        $schools = Flintstone::load('schools', $options);
        
        $keys = $schools->getKeys(); // returns array('bob', 'joe', ...)
        $id = count($keys)+1;
        foreach ($keys as $key) {
            $tmp = $schools->get($key);
            if($tmp['name'] == $school){
                return 'This school is already registered.';
            }
        }
        // Insert User
        $schools->set($id, array('name' => $school, 'id' => $id));
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured:'.$e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_add_user($user, $password, $level='user'){
    try {
        // Set options
        $t_hasher = new PasswordHash(8, FALSE);
        $options = array('dir' => '../db');
        // Load the databases
        $users = Flintstone::load('users', $options);
        // New incremental id
        $id = count($users->getKeys())+1;
        $key = str_replace(array('@', '.', ' '), '', $user);
        if($users->get($key)){
            return 'This email is already registered.';
        } else {
            // Insert User
            $users->set($key, array('id' => $id, 'level' => $level, 'email' => $user, 'password' => $t_hasher->HashPassword($password)));
            return $id;
        }
    }
    catch (FlintstoneException $e) {
        return 'An error occured:'.$e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_check_instrument($id, $cid){
    try {
        // Set options
        $options = array('dir' => 'app/db');

        // Load the databases
        $instruments = Flintstone::load('instruments', $options);
        $ins = $instruments->get($id);
        $instruments->delete($id);
        if($ins['cid'] == $cid){
            $instruments->set($id, array('id' => $id, 'cid' => '', 'lid' => $ins['lid'], 'type' => $ins['type']));
            return 1;
        } else if(is_numeric($cid) && $ins['cid'] == ''){
            $instruments->set($id, array('id' => $id, 'cid' => $cid, 'lid' => $ins['lid'], 'type' => $ins['type']));
            return -1;
        }
        return 'An error occured.';
    }
    catch (FlintstoneException $e) {
        return 'An error occured:'.$e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_delete_instrument($id){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $instruments = Flintstone::load('instruments', $options);
        $instruments->delete($id);
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_delete_school($id){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $schools = Flintstone::load('schools', $options);
        $inst = db_get_instruments();
        foreach ($inst as $i) {
            if($i['lid'] == $id){
                return 'There are instruments still attached to this school. Please delete the instruments before deleting the school.';
            }
        }
        $schools->delete($id);
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_delete_user($id){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $users = Flintstone::load('users', $options);
        $instruments = db_get_instruments();
        $keys = $users->getKeys();
        foreach ($keys as $key) {
            $tmp = $users->get($key);
            if($tmp['id'] == $id){
                foreach ($instruments as $ins) {
                    if($ins['cid'] == $id){
                        db_update_instrument($id, -1);
                    }
                }
                return $users->delete($key);
            }
        }
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_get_instrument($key){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $instruments = Flintstone::load('instruments', $options);
        return $instruments->get($key);
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_get_instruments(){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $instruments = Flintstone::load('instruments', $options);
        $keys = $instruments->getKeys();
        $instrumentArr = array();
        foreach ($keys as $key) {
            $instrumentArr[] = $instruments->get($key);
        }
        return $instrumentArr;
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_get_school($key){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $schools = Flintstone::load('schools', $options);
        return $schools->get($key);
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_get_schools(){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $schools = Flintstone::load('schools', $options);
        $keys = $schools->getKeys();
        $schoolsArr = array();
        foreach ($keys as $key) {
            $schoolsArr[] = $schools->get($key);
        }
        return $schoolsArr;
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_get_user($id){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $users = Flintstone::load('users', $options);
        $keys = $users->getKeys();
        foreach ($keys as $key) {
            $tmp = $users->get($key);
            if($tmp['id'] == $id){
                return $users->get($key);
            }
        }
        return 'Could not find user.';
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_get_users(){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $users = Flintstone::load('users', $options);
        $keys = $users->getKeys();
        $userArr = array();
        foreach ($keys as $key) {
            $userArr[] = $users->get($key);
        }
        return $userArr;
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_update_instrument($id, $cid='', $type='', $lid=''){
    try {
        // Set options
        $options = array('dir' => '../db');

        // Load the databases
        $instruments = Flintstone::load('instruments', $options);
        $ins = $instruments->get($id);
        $lid = ($lid=='') ? $ins['lid'] : $lid;
        $cid = (!is_numeric($cid) || $cid < 0) ? '' : $cid;
        $type = ($type=='') ? $ins['type'] : $type;
        $instruments->delete($id);
        $instruments->set($id, array('id' => $id, 'cid' => $cid, 'lid' => $lid, 'type' => $type));
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured:'.$e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_update_school($id, $school){
    try {
        // Set options
        $options = array('dir' => '../db');

        // Load the databases
        $schools = Flintstone::load('schools', $options);

        // Insert User
        $schools->set($id, array('name' => $school, 'id' => $id));
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured:'.$e->getMessage();
    }
}
?>
<?php
require_once('flintstone.class.php');
function db_update_user($id, $email='', $pass='', $level=''){
    try {
        // Set options
        $t_hasher = new PasswordHash(8, FALSE);
        $options = array('dir' => '../db');

        // Load the databases
        $users = Flintstone::load('users', $options);
        $keys = $users->getKeys();
        foreach ($keys as $key) {
            $tmp = $users->get($key);
            if($tmp['id'] == $id){
                $user = $users->get($key);
                $k = $key;
            }
        }
        $users->delete($k);
        $email = ($email == '') ? $user['email'] : $email;
        $key = str_replace(array('@', '.', ' '), '', $email);
        $password = ($pass=='') ? $user['password'] : $t_hasher->HashPassword($pass);
        $level = ($level != 'admin') ? 'user' : $level;
        $users->set($key, array('id' => $id, 'email' => $email, 'password' => $password, 'level' => $level));
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured:'.$e->getMessage();
    }
}
?>