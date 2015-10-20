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