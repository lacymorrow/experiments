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