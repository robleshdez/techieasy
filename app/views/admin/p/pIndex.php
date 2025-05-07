<?php   
if ($_SESSION['userRole']!="admin" && $_SESSION['userRole']!="gestor") {
 $this->errorControl('Permissions', site_url.'admin/p/');
} 

$response = $this->getDatas('getProjectsForUser') ;
   //var_dump($response);
 ?>
<div class="pagetitle ">
	<div class="d-flex align-items-start">
		<div class="d-inline-block me-2">
			<h1 class="me-3">Proyectos</h1>
		</div>

		<?php if ($_SESSION['userRole']=="admin"): ?>
			<div class="d-inline-block ms-md-4">
	<a id="" href="<?php echo site_url.'admin/p/add' ?>" 		class="btn btn-primary mt-3 mt-md-0" >Añadir Proyecto</a>
</div>
		<?php endif ?>
	</div>
	  
</div><!-- End Page Title -->

<form id="tokens">
	<input type="hidden" class="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
	<input type="hidden" class="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
</form>


<div class="container mt-3">
 <?php echo $response['html']; ?>
</div>
	

 



	 
	