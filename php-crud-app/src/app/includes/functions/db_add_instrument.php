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