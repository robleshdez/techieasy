<?php 
	$spaceUsed = $this->getDatas('getTotalSpace',['business_id'=>$routeParams['businessID']]) ;
	$spacePlan = $this->getDatas('disk_capacity',['business_id'=>$routeParams['businessID']]);

	 if (isset($spaceUsed['message']) && $spaceUsed['message']=="noItemID") {
      $this->errorControl('404', site_url.'admin/b/');
    }
    if (isset($spacePlan['message']) && $spacePlan['message']=="noItemID") {
       $this->errorControl('404', site_url.'admin/b/');
    }

	?>
<div class="pagetitle ">
	<h1 class="me-3 ">Añadir imagen</h1>

	<div><span id="spaceUsed" class="me-2"><?php echo $spaceUsed['totalSizeInMB']?>MB usados de <?php echo $spacePlan['disk_capacity'].'MB'?></span><a href="#">Mejorar mi plan</a></div>
</div><!-- End Page Title -->

<div class="container p-4 card my-3 ">
	<div class="row">
		<div class="col-12">

			<div id="drop-zone" class="drop-zone ">
				<h2 class="px-4 pt-4 px-0">Arrastra aquí las imágenes para subirlas (máx. 1 MB) <br><br>o</h2>
				<input type="file" id="file-input" accept=".jpg, .jpeg, .png" class="d-none" multiple>
				<form id="tokens">
				<input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
				<input type="hidden" id="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
				<input type="hidden" id="businessID" name="businessID" value="<?php echo $routeParams['businessID'] ?>">
				</form>
				<div class="pb-4">
					<button id="upload_media" class="btn btn-primary  m-auto d-block" >Selecciona la imagen</button>
				</div>

			</div>

		</div>
	</div>
</div>	
