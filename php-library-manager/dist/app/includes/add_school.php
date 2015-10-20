<?php
session_start();
require_once('functions.php');
if(isset($_POST['school-name']) && strlen($_POST['school-name']) > 1){
    $school_added = db_add_school($_POST['school-name']);
    if($school_added === true){
        $_SESSION['alert'] = $_POST['school-name']." added successfully.";
        header('Location: ../../?p=schools');
    } else {
        $_SESSION['alert'] = $school_added;
        header('Location: ../../?p=schools');
    }
} else {
    $_SESSION['alert'] = "Please enter a name to add a school.";
    header('Location: ../../?p=schools');
}
?>