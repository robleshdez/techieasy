<div class="pagetitle ">
  <h1 class="me-3">Añadir Trabajador</h1>

</div><!-- End Page Title -->

<div class="container p-4 card my-3">
  <div class="row">
    <div class="col-12">
      <form id="addWorker" class="needs-validation " novalidate>
        <div class="row">
          <input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
          <input type="hidden" id="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
          <div class="mb-3 col-12 col-md-6  ">
            <label class="form-label" for="worker_name">Nombre y Apellidos</label>
            <input class="form-control" type="text" name="worker_name"   id="worker_name" value=""  autofocus required placeholder="Juank de Gorvet" pattern="^.{3,50}$">
            <div class="invalid-feedback validation_worker_name"></div> 
          </div> 
          <div class="mb-3 col-12 col-md-6  ">
            <label class="form-label" for="worker_name">Nombre y Apellidos</label>
            <input class="form-control" type="text" name="worker_name"   id="worker_name" value=""  autofocus required placeholder="Juank de Gorvet" pattern="^.{3,50}$">
            <div class="invalid-feedback validation_worker_name"></div> 
          </div> 
          
          
          <div class="mb-3 col-12 ">
            <label class="form-label" for="worker_category">Categoría <i class="gicon-help tips" data-bs-toggle="tooltip" data-bs-title="Escoja la categoría que mejor identifique a su negocio. Así podrán encontraro fácilmente"></i></label>
            <?php $response = $this->getDatas('getworkerCategoryList') ;
            //var_dump($response); ?>
            <select class="form-select" id="worker_category" required="">
              <option value="">Seleccionar...</option>
              <?php 
              if (isset($response['listBcategory']) && is_array($response['listBcategory'])) {
                foreach ($response['listBcategory'] as $category) : ?>
                  <option value="<?php echo $category['category_id']; ?>">
                    <?php echo $category['name']; ?>
                  </option>
              <?php
                endforeach;
              }?>
            </select>
            <ul id="category_description">
              <?php 
              if (isset($response['listBcategory']) && is_array($response['listBcategory'])) {
                foreach ($response['listBcategory'] as $category) : ?>
                  <li id="<?php echo $category['category_id']; ?>" class="d-none">
                    <span>Ideal para negocios de: </span>
                    <?php echo $category['description']; ?>
                  </li>
              <?php
                endforeach;
              } ?>
            </ul>
            <input type="hidden" id="worker_category_val" name="worker_category_val">
            <div class="invalid-feedback validation_worker_category"></div> 
          </div>
        </div>
      </form>
      <button id="submit_add_worker" class="btn btn-primary  ms-auto d-block my-3" >Añadir Bot</button>
    </div>
  </div>
</div>