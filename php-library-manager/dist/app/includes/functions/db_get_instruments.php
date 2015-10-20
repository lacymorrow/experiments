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