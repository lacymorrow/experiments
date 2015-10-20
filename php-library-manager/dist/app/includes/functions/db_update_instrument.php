<?php
require_once('flintstone.class.php');
function db_update_instrument($id, $cid='', $type='', $lid=''){
    try {
        // Set options
        $options = array('dir' => '../db');

        // Load the databases
        $instruments = Flintstone::load('instruments', $options);
        $ins = $instruments->get($id);
        $lid = ($lid=='') ? $ins['lid'] : $lid;
        $cid = (!is_numeric($cid) || $cid < 0) ? '' : $cid;
        $type = ($type=='') ? $ins['type'] : $type;
        $instruments->delete($id);
        $instruments->set($id, array('id' => $id, 'cid' => $cid, 'lid' => $lid, 'type' => $type));
        return true;
    }
    catch (FlintstoneException $e) {
        return 'An error occured:'.$e->getMessage();
    }
}
?>