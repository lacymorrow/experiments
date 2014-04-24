<?php
    session_start();
    require_once('functions.php');
    if(isset($_POST['id']) && (isset($_POST['user-email']) || isset($_POST['user-pass']) || isset($_POST['user-level']))){
        $email = (isset($_POST['user-email'])) ? $_POST['user-email'] : '';
        $pass = (isset($_POST['user-pass'])) ? $_POST['user-pass'] : '';
        $level = (isset($_POST['user-level'])) ? 'admin' : 'user';
        $user_update = db_update_user($_POST['id'],$email,$pass,$level);
        if($user_update === true){
            $_SESSION['alert'] = $_POST['user-email']." updated successfully.";
            header('Location: ../../?p=users&u='.$_POST['id']);
        } else {
            $_SESSION['alert'] = $user_update;
            header('Location: ../../?p=users');
        }
    } else {
        $_SESSION['alert'] = "Nothing changed.";
        header('Location: ../../?p=users');
    }
?>