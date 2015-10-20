<?php

if($_SESSION['level'] == 'admin' && $auth == true){
    if(isset($_GET['s'])){
        // Delete School
        $school_deleted = db_delete_school($_GET['s']);
        if($school_deleted === true){
            $_SESSION['alert'] = 'School deleted successfully.';
            header('Location: ../../?p=schools');
        } else {
            $_SESSION['alert'] = $school_deleted;
            header('Location: ../../?p=schools');
        }
    } else if(isset($_GET['u'])){
        // Delete User
        $user_deleted = db_delete_user($_GET['u']);
        if($user_deleted === true){
            $_SESSION['alert'] = 'User deleted successfully.';
            header('Location: ../../?p=users');
        } else {
            $_SESSION['alert'] = $user_deleted;
            header('Location: ../../?p=users');
        }
    } else if(isset($_GET['i'])){
        // Delete Instrument
        $ins_deleted = db_delete_instrument($_GET['i']);
        if($ins_deleted === true){
            $_SESSION['alert'] = 'Instrument deleted successfully.';
            header('Location: ../../?p=browse');
        } else {
            $_SESSION['alert'] = $ins_deleted;
            header('Location: ../../?p=browse');
        }
    }
} else {
    $_SESSION['alert'] = "Whoa there, buddy! You are not allowed back there!";
    header('Location: ../../?p=home');
}
?>