<?php
    session_start();
    require_once('functions.php');
    $default_level = 'user';
    if(isset($_POST['manage']) && isset($_POST['user-email']) && isset($_POST['user-pass']) && strlen($_POST['user-pass']) >= 8){
        //adding from users
        $level = (isset($_POST['user-level'])) ? 'admin' : $default_level;
        $user_added = db_add_user($_POST['user-email'], $_POST['user-pass'], $level);
        if(is_numeric($user_added)){
            $_SESSION['alert'] = "New user ".$_POST['user-email']." created.";
            header('Location: ../../?p=users');
        } else {
            $_SESSION['alert'] = $user_added;
            header('Location: ../../?p=users');
        }
    } else if(isset($_POST['manage']) && strlen($_POST['user-pass']) < 8){
        $_SESSION['alert'] = "Password too short. Please enter a password of at least 8 characters.";
        header('Location: ../../?p=users');
    } else if(isset($_POST['email']) && isset($_POST['password']) && strlen($_POST['password']) >= 8 ){
        //adding from register.php
        $level = $default_level;
        $user_added = db_add_user($_POST['email'], $_POST['password'], $level);
        if(is_numeric($user_added)){
            $_SESSION['alert'] = "New user ".$_POST['email']." created. You are now logged in.";
            $_SESSION['user'] = $_POST['email'];
            $_SESSION['id'] = $user_added;
            $_SESSION['level'] = $level;
            header('Location: ../../?p=home');
        } else {
            $_SESSION['alert'] = $user_added;
            header('Location: ../../?p=login');
        }
    } else if(isset($_POST['password']) && strlen($_POST['password']) < 8 ) {
        $_SESSION['alert'] = "Password too short. Please enter a password of at least 8 characters.";
        header('Location: ../../?p=register');
    } else {
        $_SESSION['alert'] = "Invalid registration. Please enter a valid email and password.";
        header('Location: ../../?p=register');
    }
?>