<?php 
if(isset($_SESSION['user'])){ 
	session_destroy();
	header('Location: ./?p=login');
}
include_once('header.php'); 
if(isset($_SESSION['alert'])){ ?>
	<h4 class="bg-info lead text-center"><?php echo $_SESSION['alert']; ?></h4>
<?php unset($_SESSION['alert']);
} ?>

  <form class="form-signin" role="form" action="app/includes/add_user.php" method="post">
    <h2 class="form-signin-heading">Enter your email and password to register</h2>
    <input name="email" type="email" class="form-control" placeholder="Email address" required autofocus>
    <input name="password" type="password" class="form-control" placeholder="Password" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
  </form>
 <p class="lead text-center">Already Registered? <a href="?p=register">Click here to sign in &raquo;</a></p>
 <?php include_once('footer.php'); ?>