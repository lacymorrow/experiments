<?php include_once('header.php');
if(isset($_SESSION['alert'])){ ?>
	<h4 class="bg-info lead text-center"><?php echo $_SESSION['alert']; ?></h4>
<?php unset($_SESSION['alert']);
} ?>

<h1>Instrument Manager</h1>
<p class="lead">Welcome to the instrument mangager. Lorem ipsum.</p>

<?php include_once('footer.php'); ?>