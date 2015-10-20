<?php
require_once('flintstone.class.php');
function db_delete_user($id){
    try {
        // Set options
        $options = array('dir' => 'app/db');
        // Load the databases
        $users = Flintstone::load('users', $options);
        $instruments = db_get_instruments();
        $keys = $users->getKeys();
        foreach ($keys as $key) {
            $tmp = $users->get($key);
            if($tmp['id'] == $id){
                foreach ($instruments as $ins) {
                    if($ins['cid'] == $id){
                        db_update_instrument($id, -1);
                    }
                }
                return $users->delete($key);
            }
        }
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured: ' . $e->getMessage();
    }
}
?>