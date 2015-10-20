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