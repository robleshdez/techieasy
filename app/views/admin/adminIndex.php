<form id="tokens" class="needs-validation " novalidate>
	<input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
	<input type="hidden" id="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
</form>


<div class="container">
	<div class="col">index o dashboard</div>

	<?php 
	if ($_SESSION['userRole']==='admin') {
		//include 'app/views/admin/parts/adashboard.php'; 
	}
	else {
		//include 'app/views/admin/parts/udashboard.php'; 
	}

	 ?>

	
 
	<!-- cantidad de medias
	cantidad por tipos de mdias
	espacio ocupado -->
	
</div>

