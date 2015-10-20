<?php 
if(!$auth && !$public_browse){ 
	$_SESSION['alert'] = 'Please log in to access the requested page.';
	header('Location: ./?p=home'); 
}
$instArr = db_get_instruments();
include_once('header.php');
if(isset($_SESSION['alert'])){ ?>
	<h4 class="bg-info lead text-center"><?php echo $_SESSION['alert']; ?></h4>
<?php unset($_SESSION['alert']);
} ?>

<div class="row"> 
<?php if($_SESSION['level'] == 'admin'){ ?>
	<div class="col-sm-6">
		<h3>Add an Instrument</h3>
		<form role="form" method="post" action="app/includes/add_instrument.php">
		  <div class="form-group">
		    <label for="i-type">Type of instrument</label>
		    <input type="text" class="form-control" name="i-type" id="i-type" placeholder="Type of instrument" required>
		  </div>
		  <div class="form-group">
		    <label for="i-location">Location</label>
		    <select class="form-control" name="i-location">
		      <?php $schools = db_get_schools();
		      foreach ($schools as $school) {
		      	echo '<option value='.$school['id'].'>'.$school['name'].'</option>';
		      } ?>
			</select>
		  </div>
		  <button type="submit" class="btn btn-default">Add Instrument</button>
		</form>
	</div>
	<?php if(isset($_GET['i'])){ $ins = db_get_instrument($_GET['i']);?>
		<div class="col-sm-6">
			<h3>Checkout Instrument #<?php echo $ins['id'].':  '.$ins['type']; ?></h3>
			<form role="form" method="post" action="app/includes/update_instrument.php">
	  		  <div class="form-group">
			    <label for="i-type">Type of instrument</label>
			    <input type="text" class="form-control" name="i-type" id="i-type" placeholder="Type of instrument">
			  </div>
			  <div class="form-group">
			    <label for="i-location">Location</label>
			    <select class="form-control" name="i-location">
			      <?php $schools = db_get_schools();
			      foreach ($schools as $school) {
			      	echo '<option value='.$school['id'].'>'.$school['name'].'</option>';
			      } ?>
				</select>
			  </div>
			  <div class="form-group">
			    <label for="i-type">Checked out to</label>
			    <select class="form-control" name="cid">
			      <option value=""> - Not checked out - </option>
			      <?php $users = db_get_users();
			      foreach ($users as $user) {
			      	$chk = ($ins['cid']===$user['id']) ? 'selected' : '';
			      	echo '<option value="'.$user['id'].'" '.$chk.'>'.$user['email'].'</option>';
			      } ?>
				</select>
			  </div>
			  <input type="hidden" name="id" value="<?php echo $ins['id']; ?>">
			  <button type="submit" class="btn btn-info">Update User</button>
			</form>
		</div>
	<?php } ?>
<?php } ?>
</div>


<h1>Browse Instruments</h1>
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th>#</th>
			<th>Type</th>
			<th>Location</th>
			<th>Checked out to</th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($instArr as $inst) { ?>
			<tr <?php if($inst['cid'] != ''){ echo 'class="info"'; } ?>>
				<td><?php $iid = $inst['id']; echo $iid; ?></td>
				<td><?php echo $inst['type']; ?></td>
				<td><?php $lid = db_get_school($inst['lid']);
				echo $lid['name']; ?></td>
				<td>
					<?php if($inst['cid'] != ''){  ?>
						<a href="?p=users&u=<?php echo $inst['cid']; ?>">
						<?php $cid = db_get_user($inst['cid']);
						echo $cid['email']; ?></a>
					<?php } ?>
				</td>
				<td>
					<?php if($inst['cid'] != '' && $inst['cid'] == $id){ ?>
						<a href="?p=check&i=<?php echo $id; ?>"><button type="button" class="btn btn-info">Return</button></a>
					<?php } else if($inst['cid'] == ''){ ?>
						<a href="?p=check&i=<?php echo $id; ?>"><button type="button" class="btn btn-info">Checkout</button></a>
					<?php } ?>
				</td>
				<?php if($_SESSION['level'] == 'admin'){ ?>
					<td>
						<a href="?p=browse&i=<?php echo $iid; ?>"><button type="button" class="btn btn-info">Update</button></a>
					</td>
					<td>
						<a href="?p=delete&i=<?php echo $iid; ?>"><button type="button" class="btn btn-danger">Delete</button></a>
					</td>					
				<?php } ?>
			</tr>
		<?php } ?>
	</tbody>
</table>
<h4 class="bg-info lead text-center">A blue background indicates that the instrument is currently checked out.</h4>
<?php include_once('footer.php'); ?>