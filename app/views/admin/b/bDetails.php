<?php 
$response = $this->getDatas('getBotDetails', [
  'itemID'=>$routeParams['businessID'],
  'csrfToken'=> $_SESSION['csrfToken'],
  'csrfTimestamp'=> $_SESSION['csrfTimestamp']
] ) ;

   
 //var_dump($response);
if (isset($response['message']) && $response['message']=='404') {
  $this->errorControl('404', site_url.'admin/b/');
}elseif (isset($response['message']) && $response['message']=='notPermission') {
  $this->errorControl('Permissions', site_url.'admin/b/', '');
}

$botData=$response['row'][0];
//var_dump($botData['mobile_number']);
if ($botData['mobile_number']=='') {
 $mobile_number='';
}
else{
  $mobile_number='+' . $botData['mobile_number'];
}

$flowTvsC = $this->getDatas('getFlowTvsC', ['itemID'=>$routeParams['businessID']] ) ;
$botFLowList = $this->getDatas('getBotFLowList', ['itemID'=>$routeParams['businessID']] ) ;
//var_dump($botFLowList);
?>


<div class="container ">
  <div class="row">
    <div class="col-12">
      <form id="editBot" class="needs-validation " novalidate>

        <input type="hidden" id="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
        <input type="hidden" id="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
        <input type="hidden" id="botID" name="botID" value="<?php echo $routeParams['businessID']; ?>">
        <div class="container px-4 px-2 card mb-3">


          <div class="row mt-3 ">

            <div class="mb-3 col-12 col-md-4 ">
              <div class="d-flex">
                <input class="form-control" type="text" name="bot_name"   id="bot_name" value="<?php echo $botData['name'];?>" autocomplete="off"  required placeholder="Ventas de..." pattern="^.{3,50}$" style="width: calc(100% - 30px);">
                <div>
                  <i  class="gicon-help tips" data-bs-toggle="tooltip" data-bs-title="Puedes cambiarle el nombre a tu Bot para identificarlo mejor. "></i> 
                </div>
              </div>
            </div> 

            <div class="mb-3 col-12 col-md-7  offset-md-1 d-flex justify-content-md-end">
              <div class="btn-toolbar" role="toolbar">
                <?php   
                $checked='';
                $disabled='';
                $status='Inactivo';
                if ($botData['is_active'] ==1) {
                  $checked='checked';
                  $status='Activo';
                  $disabled='';
                } 
                if ($botData['is_blocked'] ==1) {
                  $checked='';
                  $status='Bloqueado';
                  $disabled='disabled bloqued';
                }
                else if($mobile_number =='') {
                  $checked='';
                  $status='Inactivo';
                  $disabled='disabled';
                }
                 ?>
                <div class="btn-group mb-3 mb-md-0 ms-md-auto" role="group">
                  <button id="add_flow" type="button" class="btn btn-outline">Añadir Flujo</button>
                  <button id="show_config" type="button" class="btn btn-outline">Configurar</button>
                  <input class="btn-check" type="checkbox" name="isActive"  id="isActive" value="1" <?php   echo $checked . ' ' .$disabled; ?>>
                  <label class="btn btn-outline  align-content-center isActive <?php echo  $disabled ?>" for="isActive"><?php echo  $status ?></label>
                </div>
                <div class="btn-group ms-md-auto ms-lg-2 mt-2 mt-lg-0 d-none" role="group">
                  <button id="submit_edit_bot" class="btn btn-primary  ms-auto d-block disabled " >Guardar</button>
                </div>
              </div>
            </div>
          </div>
        </div>




      </form>
       
    <div class="col-12 mb-3 mt-3 ">
     
       
          <span id="flowTvsC">Flows creados: <?php echo $flowTvsC['total_flows'] ?> de <?php echo $flowTvsC['can_have']?>
        </span>
        <?php if ($flowTvsC['total_flows']>=$flowTvsC['can_have']) {
            echo '<span><a href="#">Mejorar mi plan</a></span>';
          } ?> 
      </div>
    <div id="all_flow" class=" row ">
     
      <?php 
// Verificar si 'rows' está presente en el array
      if (isset($botFLowList['rows']) && is_array($botFLowList['rows'])) {
        foreach ($botFLowList['rows'] as $row) {
// Acceder a los valores dentro de cada fila
          $flowId = $row['flow_id'];
          $bot_id = $row['bot_id'];
          $name = $row['name'];
          $type = $row['type'];
          $trigger_words = json_decode($row['trigger_words'],true);


          ?>
          <div class="col-12 col-md-6 col-lg-4 mb-3">
              <div id="flow_<?php echo $flowId ?>" class="card b-list-card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between ">
                    <h5 class="mt-0 mb-0"><?php echo $name; ?></h5>
                    <div class="actions d-flex  ">
                  <a id="<?php echo 'f_' . $flowId; ?>" class="" href="<?php echo site_url.'admin/b/'.$bot_id. '/f/'.$flowId.'/' ?>">
                  <i class="gicon-config me-3  d-inline-block"> </i></a>
                  <a id="df_<?php echo $flowId ?>"href="#"><i class="gicon-trash  d-inline-block ms-2"></i></a>
                </div>
                  </div>
                  <div>
                    <span><strong>Tipo:</strong> Flow de <?php echo $type ?></span>
                  </div>
                  <div>
                    <span><strong>Disparadores:</strong> 
                      <?php 
                      echo implode(', ', $trigger_words);
                      ?>
                    </span>
                  </div>

                 </div>
              </div>
            
          </div>
          <?php 
        }
      } else {
        echo '<span id="noF">No tienes Flows creados.</span>';
      }
      ?>
</div>



    


    <?php
    if (isset($botFLowList['total_pages'])&& $botFLowList['total_pages']>1) { 
      pagination($botFLowList['total_pages'],$botFLowList['page']);
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
      </nav> </div>
      ';

    }  ?>
   




 <div id="bot_config" class=" row mt-3 d-none">
 
 <div class="pagetitle mt-2">
  <h2  >Configuración</h2>   
</div>

 <div class="col-12 col-md-4 mb-3">
    <div id="" class="card b-list-card h-100" style="overflow: visible;">
      <div class="card-body">
        <div class="d-flex justify-content-between ">
          <h5 class="mt-0 mb-0">Vincular WhatsApp</h5>

          <div class="actions d-flex justify-content-end  ">
            <a id="wts_cnt" href="#">
              <i class="gicon-save "> </i>
            </a>
            
          </div>
        </div>
        <form id="editWts_cnt" class="needs-validation " novalidate method="post">
      

        <label class="mb-2">Número de teléfono:</label>
        <input class="form-control" type="text" name="mobile_number"   id="mobile_number"   value="<?php echo $mobile_number;?>"    >
        <div class="invalid-feedback validation_mobile_number"></div>
       </form>
     
      

        </div>

        </div>

 </div>




 <div class="col-12 col-md-4 mb-3">
  <div id="" class="card b-list-card h-100 d-grid" >
    <div class="card-body d-flex flex-column justify-content-between">
      <div class=" ">
        <h5 class="mt-0 mb-0">Eliminar Bot</h5>
      </div>
      <div class="d-flex  ">
        <button id="del_bot" class="btn btn-danger m-auto   d-block   py-2 " style=" min-width: 100px;">Eliminar</button>
      </div>
    </div>
  </div>
</div>

</div>







  </div>
</div>
</div>






