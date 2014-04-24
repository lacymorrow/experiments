<?php
session_start();
require_once('functions.php');
if(isset($_POST['school-name']) && strlen($_POST['school-name']) > 1){
    $school_update = db_update_school($_POST['id'],$_POST['school-name']);
    if($school_update === true){
        $_SESSION['alert'] = $_POST['school-name']." updated successfully.";
        header('Location: ../../?p=schools');
    } else {
        $_SESSION['alert'] = $school_update;
        header('Location: ../../?p=schools');
    }
} else {
    $_SESSION['alert'] = "Nothing changed.";
    header('Location: ../../?p=schools');
}
?>