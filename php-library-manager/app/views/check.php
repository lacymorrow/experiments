<?php

if($auth == true && isset($_GET['i']) && $id){
    $ins_check = db_check_instrument($_GET['i'], $id);
    if(is_numeric($ins_check) && $ins_check > 0){
        $_SESSION['alert'] = 'You have returned this instrument.';
        header('Location: ../../?p=browse');
    } else if(is_numeric($ins_check) && $ins_check < 0){
        $_SESSION['alert'] = 'You have checked out this instrument.';
        header('Location: ../../?p=browse');
    } else {
        $_SESSION['alert'] = $ins_check;
        header('Location: ../../?p=browse');
    }
} else {
    $_SESSION['alert'] = "Whoa there, buddy! You are not allowed back there!";
    header('Location: ../../?p=home');
}
?>