<div class="container-fluid">
  <div class="row">
    <aside id="sidebar" class="sidebar col">
      <?php include 'app/views/admin/parts/'.$routeParams['moduleName'].'side.php';?>
      <?php //echo $adminSidebar; ?>
      <?php //echo $commonSidebar; ?>
    </aside>

    <main id="main" class="main col admin">
     <?php  include 'app/views/admin/parts/navbar.php';?>

     
     <?php //print_r($routeParams); ?>
     <section class="section dashboard pt-3">
      <div class="row">
        <?php echo $content; ?>
      </div>
    </section>
     

  </main><!-- End #main -->
</div>




</div>
<!-- Modal de select media -->
<div class="modal fade" id="selectMediaModal" tabindex="-1" aria-labelledby="selectMediaModal" aria-hidden="true" >
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title">Seleccionar imagen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div  class="modal-body ">
        <input type="hidden" name="mediaSelectedID"   id="mediaSelectedID" value="" target="" media_url="">
        <div id="all_items" class="row">
          <div>
            <p>No tienes imágenes agregadas a este negocio.</p>
            <a href="<?php echo site_url . 'admin/b/'. $routeParams['businessID']. '/m/add/' ?>" class="btn btn-primary mt-3 mt-md-0">Añadir imágenes</a>
          </div>
        </div>
        <div class="row mt-3">
          <nav  aria-label="all_items_pagination">
            <ul id="all_items_pagination" class="pagination justify-content-end pagination-sm"></ul>
          </nav>
        </div>
        
        <div id="" class="pt-5 d-flex flex-row-reverse ">
          <a id="selectMedia_guardar" class="btn btn-primary btn-modal ms-2 disabled">Guardar</a>
          <a class="btn btn-secondary btn-modal" data-bs-dismiss="modal">Cancelar</a>
        </div>

      </div>
    </div>
  </div>
</div>