<?php
require_once('flintstone.class.php');
function db_delete_instrument($id){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $instruments = Flintstone::load('instruments', $options);
        $instruments->delete($id);
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>