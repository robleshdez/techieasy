 <?php $response = $this->getDatas('getBusinessDetails', ['itemID'=>$routeParams['businessID']] ) ;

 //var_dump($response);
if (isset($response['message']) && $response['message']=='404') {
 $this->errorControl('404', site_url.'admin/b/');
}elseif (isset($response['message']) && $response['message']=='notPermission') {
  $this->errorControl('Permissions', site_url.'admin/b/', '');
}

$businessData=$response['row'][0];
//var_dump($businessData);

 ?>
  <div class="pagetitle d-flex align-items-center">
    <h1 id="pageH1">Editar negocio</h1>
  </div>
 
  <div class="container p-4 card my-3">
    <div class="row">
      <div class="col-12">
         <form id="editBusiness" class="needs-validation " novalidate>

          <input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
          <input type="hidden" id="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
          <input type="hidden" id="businessID" name="businessID" value="<?php echo $routeParams['businessID']; ?>">
          

        <div class="businessBaner">
          <div class="b1"></div>
          <div id="bannerImg" class="bannerImg" style="background-image: url('<?php   echo $response['banerurl'] ?>');">
            <span id="changeBaner"><i class="gicon-photo me-md-2 p-2"></i><span class="d-none d-md-inline">Cambiar portada</span></span>
            <input type="hidden" id="bannerImgID" name="bannerImgID" value="<?php   echo $businessData['banner_id'] ?>">
          </div>
        </div>

         <div class="businessPhoto d-flex justify-content-center">
          <div class="p1" style="background-image: url('<?php   echo $response['logourl'] ?>');"></div>
          <div id="profileImg" class="profileImg" >
            <span id="changeProfileImg"><i class="gicon-photo"></i></span>
            <input type="hidden" id="profileImgID" name="profileImgID" value="<?php   echo $businessData['logo_id'] ?>">
          </div>
        </div>

        <div class="row mt-3">
        <div class="mb-3 col-12 col-md-6 ">
        <label class="form-label" for="business_name">Nombre del negocio</label>
        <input class="form-control" type="text" name="business_name"   id="business_name" value="<?php echo $businessData['name'];?>" autocomplete="off"  required placeholder="Tienda de Caramelos" pattern="^.{3,50}$"> 
      </div> 

      

        <div class="mb-3 col-12 col-md-2 offset-md-4">
            <label class="form-label" for="isActive">Estado <i class="gicon-help tips" data-bs-toggle="tooltip" data-bs-title="Activa tu negocio para que sea visible al público"></i></label>
        <div class="form-check">

<?php   
$checked='';
  $status='Inactivo';
if ($businessData['is_active'] ==1) {
  $checked='checked';
  $status='Activo';
} ?>
  <input class="form-check-input" type="checkbox" name="isActive"  id="isActive" value="1" <?php   echo $checked  ?>>
  <label class="form-check-label isActive" for="isActive"><?php echo  $status ?></label>
</div>
 
        </div>


<div class="mb-3 col-12 col-md-12 ">
  <div class="row"> 
    <div class="col-12 col-md-6 ">
        <label class="form-label" for="business_category">Categoría</label>
         <?php $response = $this->getDatas('getBusinessCategoryList') ;
            //print_r($response); ?>
            <select class="form-select" id="business_category" >
              <?php 
              if (isset($response['listBcategory']) && is_array($response['listBcategory'])) {
                foreach ($response['listBcategory'] as $category) : ?>
                  <?php if ($category['category_id']==$businessData['category']): ?>
                    <option selected value="<?php echo $category['category_id']; ?>">
                    <?php echo $category['name']; ?>
                  </option>
                  <?php else: ?>
                  <option value="<?php echo $category['category_id']; ?>">
                    <?php echo $category['name']; ?>
                  </option>
                  <?php endif ?>
              <?php
                endforeach;
              }?>
            </select>
            </div>
            <div class="mt-3 my-md-0 col-12 col-md-6 align-self-end ">
            <?php  if (isset($response['listBcategory']) && is_array($response['listBcategory'])) : ?>
            <ul id="category_description" class="my-0 ">
              <?php 
              if (isset($response['listBcategory']) && is_array($response['listBcategory'])) {

                
                foreach ($response['listBcategory'] as $category) : ?>
                  <?php $disp= ($category['category_id']==$businessData['category']) ? '':'d-none'; ?>
                  <li id="<?php echo $category['category_id']; ?>" class="<?php echo $disp ?>">
                    <span>Ideal para negocios de: </span>
                    <?php echo $category['description']; ?>
                  </li>
              <?php
                endforeach;
              } ?>
            </ul>
        <input type="hidden" id="business_category_val" name="business_category_val" value="<?php echo $businessData['category'] ?>">
       <?php endif ?>
        </div>
</div>
</div>


         <div class="mb-3 col-12 ">
        <label class="form-label" for="business_alias">Alias del negocio</label>
        <div class="input-group  ">
          <span class="input-group-text" id="basic-addon"><?php echo site_url; ?></span>
          <input class="form-control" type="text" name="business_alias"   id="business_alias" value="<?php echo $businessData['url'];?>" autocomplete="off " placeholder="tienda_caramelos" required pattern="^[a-z0-9_\-]{3,50}$">
        </div>
      </div>
      <div class="mb-3 col-12  ">
        <label class="form-label" for="business_description">Descripción</label>
        <textarea class="form-control" type="text" name="busines_description"   id="business_description"   autocomplete="off"   placeholder="Describe tu negocio" pattern="^.{3,150}$"><?php echo $businessData['description'];?> </textarea>
      </div>
      </div>
       
      </form>
       <button id="submit_edit_business" class="btn btn-primary  ms-auto d-block disabled" >Guardar</button>
      </div>
    </div>
  </div>

 
  



