<?php
require_once('flintstone.class.php');
function db_delete_school($id){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $schools = Flintstone::load('schools', $options);
        $inst = db_get_instruments();
        foreach ($inst as $i) {
            if($i['lid'] == $id){
                return 'There are instruments still attached to this school. Please delete the instruments before deleting the school.';
            }
        }
        $schools->delete($id);
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>