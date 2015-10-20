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