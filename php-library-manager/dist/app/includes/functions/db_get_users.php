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