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