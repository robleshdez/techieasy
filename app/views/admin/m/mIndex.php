<?php 
    $spaceUsed = $this->getDatas('getTotalSpace',['business_id'=>$routeParams['businessID']]) ;
    $spacePlan = $this->getDatas('disk_capacity',['business_id'=>$routeParams['businessID']]);

    if (isset($spaceUsed['message']) && $spaceUsed['message']=="noItemID") {
      $this->errorControl('404', site_url.'admin/b/');
    }
    if (isset($spacePlan['message']) && $spacePlan['message']=="noItemID") {
       $this->errorControl('404', site_url.'admin/b/');
    }

//var_dump($spaceUsed);
    ?>
<div class="pagetitle ">
	<h1 class="me-3">Biblioteca de imágenes</h1>
	<a href="<?php  echo site_url.'admin/b/'.$routeParams['businessID'].'/m/add/' ?>" class="btn btn-primary mt-3 mt-md-0" >Añadir imagen</a>  
	

	<div>
	<span id="spaceUsed" class="me-2"><?php echo $spaceUsed['totalSizeInMB']?>MB usados de <?php echo $spacePlan['disk_capacity'].'MB'?></span><a href="#">Mejorar mi plan</a></div>
</div><!-- End Page Title -->



<form id="tokens">
	<input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
	<input type="hidden" id="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
	<input type="hidden" id="businessID" name="businessID" value="<?php echo $routeParams['businessID']; ?>">
</form>
<?php 
$response = $this->getDatas('getMediasList',['business_id'=>$routeParams['businessID']]) ;
//print_r($response);
?>
<div class="container mt-5">
	<div id="all_items" class="row mediasList">
		<?php 
			// Verificar si 'rows' está presente en el array
		if (isset($response['rows']) && is_array($response['rows'])) {
			foreach ($response['rows'] as $row) {
        // Acceder a los valores dentro de cada fila
				$mediaId = $row['media_id'];
				$mediaURL = $row['media_url'];
				$smallURL= $this->getDatas('getThumbnailUrl',['mediaURL'=>$mediaURL,'size'=>'small']);    
				?>
				<div class="col-4 col-md-2 col-lg-2 col-sm-2">
				<a id="<?php echo 'mID_' . $mediaId; ?>" class="m-list-card" href="#">
					<div class="card mb-3">
						<img class="card-img-top" src="<?php echo site_url. $smallURL ?>">
					</div>
				</a>
			</div>
<?php 
			}
		} else {
			echo '<span id="noM">No tienes imágenes agregadas a este negocio.</span>';
		}
		?>		
	</div>

	<?php
	 if (isset($response['total_pages'])&&$response['total_pages']>1) { 
	 	pagination($response['total_pages'],$response['page']);
	 }

function pagination($total_pages, $page) {  	
	echo '<div class="row">
		<nav  aria-label="all_items_pagination">
				<ul id="all_items_pagination" class="pagination justify-content-end pagination-sm">';
					  
					 // Primer item
    echo '<li class="page-item ' . (($page == 1) ? 'disabled' : '') . '">';
    echo ($page == 1) ? '<span class="page-link">Anterior</span>' : '<a class="page-link prev" href="#">Anterior</a>';
    echo '</li>';
    // Iterar sobre las páginas
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<li class="page-item ' . (($page == $i) ? 'active' : '') . '">';
        echo ($page == $i) ? '<span class="page-link">' . $i . '</span>' : '<a class="page-link linkeable" href="#">' . $i . '</a>';
        echo '</li>';
    }
    // Último item
    echo '<li class="page-item ' . (($page == $total_pages) ? 'disabled' : '') . '">';
    echo ($page == $total_pages) ? '<span class="page-link">Siguiente</span>' : '<a class="page-link next" href="#">Siguiente</a>';
    echo '</li>';
  
			 echo '</ul>
			</nav>
	</div>';

}

?>

   <!-- Modal detalles de medias  -->
<div class="modal fade" id="editMediaModal" tabindex="-1" aria-labelledby="editMediaModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content ">
            <div class="modal-header">
        <h5 class="modal-title">Detalles de la imagen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div  class="modal-body ">
      <div class="row">
        <div class="col-md-5 col-12">
          <img id="img_details" class="img-fluid" src="" alt="">
        </div>
        <div class="col-md-7 col-12">
          <form id="editMedia" class="needs-validation " novalidate>
    <div class="row">
      <input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
	<input type="hidden" id="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
	<input type="hidden" id="businessID" name="businessID" value="<?php echo $routeParams['businessID']; ?>">
	<input type="hidden" id="media_id" name="media_id" value="1">
 <!-- incluir el id del negocio -->

  <div class="mb-0 col-12 lh-1">
  <label class="form-label" for="alt_text">Subido el: </label>
  <span id="upload_date">fecha</span>
  </div>
   <div class="mb-0 col-12 lh-1">
  <label class="form-label" for="alt_text">Tipo: </label>
  <span id="file_type">jpg</span>
  </div>
  <div class="mb-0 col-12 lh-1">
  <label class="form-label" for="alt_text">Tamaño del archivo: </label>
  <span id="file_weight">0</span><span> KB</span>
  </div>
  <div class="mb-2 col-12 lh-1">
  <label class="form-label" for="alt_text">Dimensiones: </label>
  <span id="file_size">píxeles</span>
  </div>

 
       <!-- <div class="mb-2 col-12   ">
            <label class="form-label" for="alt_text">Texto alternativo <i class="gicon-help tips" data-bs-toggle="tooltip" data-bs-title="El texto que aparecerá antes de que la imagen sea cargada"></i></label>
    <input class="form-control" type="text" name="alt_text"   id="alt_text" value="" autocomplete="off "> -->

       </div>
        <div class="mb-2 col-12   ">
            <label class="form-label" for="img_mame">Título descriptivo <i class="gicon-help tips" data-bs-toggle="tooltip" data-bs-title="El texto que aparecerá antes de que la imagen cargue y con el que identificarás la imagen. Ayuda a mejorar el posicionamiento."></i></label>
    <input class="form-control" type="text" name="img_name"   id="img_name" value="" autocomplete="off ">
 
       </div>

        <div class="mb-3 col-12   d-flex align-items-baseline">
            <label class="form-label d-block me-3" for="img_url">URL: </label>
        <a id="img_url" href="#" target="_blank">http</a>
 
       </div>
       <div class="mb-3 col-12   ">
          <a id="delmedia" class="text-danger" href="#">Borrar permanentemente</a> 
 
       </div>
    </div>
                    
      </form>
        <div id="" class="pt-5 d-flex flex-row-reverse ">
        <a id="editMedia_guardar" class="btn btn-primary btn-modal ms-2 disabled">Guardar</a>
        <a class="btn btn-secondary btn-modal" data-bs-dismiss="modal">Cancelar</a>
        </div>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>
	 
	