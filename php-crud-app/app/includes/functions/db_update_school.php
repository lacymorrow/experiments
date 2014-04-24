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