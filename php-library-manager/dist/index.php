<?php
  session_start();
  $theme = 'app';
  require_once($theme.'/includes/functions.php');
  if(isset($_SESSION['id']) && is_array($user = db_get_user($_SESSION['id']))){
      $auth = true;
      $user = db_get_user($_SESSION['id']);
      $id = $user['id'];
      $_SESSION['level'] = $user['level'];
  } else { $auth = false; unset($id); $_SESSION['level'] = 'public'; session_destroy(); }
  // Allow the public to view instrument database
  $public_browse = true;
  $page = (isset($_GET['p'])) ? $_GET['p'] : 'home';
  $valid_page = include_once($theme.'/views/'.$page.'.php');

  // 404 ERROR
  if(!$valid_page){ require_once($theme.'/views/404.php'); } 
?>