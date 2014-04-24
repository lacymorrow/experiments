<?php 
if(!$auth){ 
	$_SESSION['alert'] = 'Please log in to access the requested page.';
	header('Location: ./?p=home'); 
}
$usersArr = db_get_users();
include_once('header.php');
if(isset($_SESSION['alert'])){ ?>
	<h4 class="bg-info lead text-center"><?php echo $_SESSION['alert']; ?></h4>
<?php unset($_SESSION['alert']);
} ?>

<h1>Manage Users</h1>

<div class="row">

<?php if($_SESSION['level'] == 'admin'){ ?>
	<div class="col-sm-6">
		<h3>Add a User</h3>
		<form role="form" method="post" action="app/includes/add_user.php">
		  <div class="form-group">
		    <label class="sr-only" for="user-email">Email</label>
		    <input type="email" class="form-control" name="user-email" id="user-email" placeholder="Email" required>
		  </div>
		  <div class="form-group">
		    <label class="sr-only" for="user-pass">Password</label>
		    <input type="text" class="form-control" name="user-pass" id="user-pass" placeholder="Password" required>
		    <!-- <p class="help-block">Users will be emailed their password.</p> -->
		  </div>
		  <div class="checkbox">
		    <label>
		      <input type="checkbox" name="user-level"> Give this user administrative rights
		    </label>
		  </div>
		  <input type="hidden" value="1" name="manage">
		  <button type="submit" class="btn btn-default">Add User</button>
		</form>
	</div>
	<?php if(isset($_GET['u'])){ $usr = db_get_user($_GET['u']);?>
		<div class="col-sm-6">
			<h3>Update User #<?php echo $usr['id'].':  '.$usr['email']; ?></h3>
			<form role="form" method="post" action="app/includes/update_user.php">
			  <div class="form-group">
			    <label class="sr-only" for="user-email">New Email</label>
			    <input type="email" class="form-control" name="user-email" id="user-email" placeholder="New Email">
			  </div>
			  <div class="form-group">
			    <label class="sr-only" for="user-pass">New Password</label>
			    <input type="text" class="form-control" name="user-pass" id="user-pass" placeholder="New Password">
			    <p class="help-block">You do not need to enter both email and password, only the information you wish to change.</p>
			  </div>
	  		  <div class="checkbox">
			    <label>
			      <input type="checkbox" name="user-level" <?php if($usr['level'] == 'admin'){ echo 'checked'; } ?>> Give this user administrative rights
			    </label>
			  </div>
			  <input type="hidden" name="id" value="<?php echo $usr['id']; ?>">
			  <button type="submit" class="btn btn-info">Update User</button>
			</form>
		</div>
	<?php } ?>
<?php } ?>
</div>

<h3>Browse Users</h3>
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th>#</th>
			<th>Email</th>
			<th>User Type</th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($usersArr as $user) { ?>
			<tr>
				<td><?php $id = $user['id']; echo $id; ?></td>
				<td><?php echo $user['email']; ?></td>
				<td><?php echo $user['level']; ?></td>
				<td>
					<?php if($_SESSION['level'] == 'admin'){ ?>
						<a href="?p=users&u=<?php echo $id; ?>"><button type="button" class="btn btn-info ">View</button></a>
					<?php } ?>
				</td>
				<td>
					<?php if($_SESSION['level'] == 'admin'){ ?>
						<a href="?p=delete&u=<?php echo $id; ?>"><button type="button" class="btn btn-danger ">Delete</button></a>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
<?php include_once('footer.php'); ?>