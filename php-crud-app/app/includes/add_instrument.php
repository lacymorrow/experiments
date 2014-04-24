<?php
session_start();
require_once('functions.php');
if(isset($_POST['i-type']) && isset($_POST['i-location'])){
    $ins_added = db_add_instrument($_POST['i-type'],$_POST['i-location']);
    if($ins_added === true){
        $_SESSION['alert'] = "Instrument added successfully.";
        header('Location: ../../?p=browse');
    } else {
        $_SESSION['alert'] = $ins_added;
        header('Location: ../../?p=browse');
    }
} else {
    $_SESSION['alert'] = "Please select a type and location. Nothing changed.";
    header('Location: ../../?p=browse');
}
?>