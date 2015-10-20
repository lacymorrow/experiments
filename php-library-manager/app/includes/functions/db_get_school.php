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