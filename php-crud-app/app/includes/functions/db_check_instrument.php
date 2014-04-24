<?php
require_once('flintstone.class.php');
function db_check_instrument($id, $cid){
    try {
        // Set options
        $options = array('dir' => 'app/db');

        // Load the databases
        $instruments = Flintstone::load('instruments', $options);
        $ins = $instruments->get($id);
        $instruments->delete($id);
        if($ins['cid'] == $cid){
            $instruments->set($id, array('id' => $id, 'cid' => '', 'lid' => $ins['lid'], 'type' => $ins['type']));
            return 1;
        } else if(is_numeric($cid) && $ins['cid'] == ''){
            $instruments->set($id, array('id' => $id, 'cid' => $cid, 'lid' => $ins['lid'], 'type' => $ins['type']));
            return -1;
        }
        return 'An error occured.';
    }
    catch (FlintstoneException $e) {
        return 'An error occured:'.$e->getMessage();
    }
}
?>