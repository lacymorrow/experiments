<?php
    session_start();
    require_once('functions.php');
    if(isset($_POST['id']) && (isset($_POST['cid']) || isset($_POST['i-type']) || isset($_POST['i-location']))){
        $cid = (isset($_POST['cid'])) ? $_POST['cid'] : '';
        $type = (isset($_POST['i-type'])) ? $_POST['i-type'] : '';
        $lid = (isset($_POST['i-location'])) ? $_POST['i-location'] : '';
        $ins_update = db_update_instrument($_POST['id'],$cid,$type,$lid);
        if($ins_update === true){
            $_SESSION['alert'] = "Instrument updated successfully.";
            header('Location: ../../?p=browse&i='.$_POST['id']);
        } else {
            $_SESSION['alert'] = $ins_update;
            header('Location: ../../?p=browse');
        }
    } else {
        $_SESSION['alert'] = "Nothing changed.";
        header('Location: ../../?p=browse');
    }
?>