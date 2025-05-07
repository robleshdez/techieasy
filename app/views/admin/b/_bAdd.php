<div class="pagetitle ">
  <h1 class="me-3">Añadir Bot</h1>
  <?php $response = $this->getDatas('getBusinessTvsC') ;
  //print_r($response); ?>
  <span class="d-block">Bots creados: <?php echo $response['total_bots'] ?> de <?php echo $response['can_have']?>
  <?php if ($response['total_bots']>=$response['can_have']) {
    echo '<a href="#">Mejorar mi plan</a>';
  } ?> 
</span>
</div><!-- End Page Title -->

<div class="container p-4 card my-3">
  <div class="row">
    <div class="col-12">
      <form id="addBusiness" class="needs-validation " novalidate>
        <div class="row">
          <input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
          <input type="hidden" id="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
          <div class="mb-3 col-12  ">
            <label class="form-label" for="business_name">Nombre del Bot<i class="gicon-help tips" data-bs-toggle="tooltip" data-bs-title="El nombre con que se identificará el negocio"></i></label>
            <input class="form-control" type="text" name="business_name"   id="business_name" value=""  autofocus required placeholder="Tienda de Caramelos" pattern="^.{3,50}$">
            <div class="invalid-feedback validation_business_name"></div> 
          </div> 
          
          <div class="mb-3 col-12 ">
            <label class="form-label" for="business_category">Categoría <i class="gicon-help tips" data-bs-toggle="tooltip" data-bs-title="Escoja la categoría que mejor identifique a su negocio. Así podrán encontraro fácilmente"></i></label>
            <?php $response = $this->getDatas('getBusinessCategoryList') ;
            //var_dump($response); ?>
            <select class="form-select" id="business_category" required="">
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
            <input type="hidden" id="business_category_val" name="business_category_val">
            <div class="invalid-feedback validation_business_category"></div> 
          </div>
        </div>
      </form>
      <button id="submit_add_business" class="btn btn-primary  ms-auto d-block my-3" >Añadir Bot</button>
    </div>
  </div>
</div>