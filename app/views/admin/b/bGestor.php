<div class="pagetitle ">
  <h1 class="me-3">Negocios que administras <i class="gicon-help tips" data-bs-toggle="tooltip" data-bs-title="El texto que aparecerá antes de que la imagen cargue y con el que identificarás la imagen. Ayuda a mejorar el posicionamiento."></i></h1>
</div><!-- End Page Title -->

<form id="tokens">
  <input type="hidden" class="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
  <input type="hidden" class="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
</form>
<?php 
$response = $this->getDatas('getBusinessList') ;
//print_r($response);
?>
<div class="container mt-5">
  <div id="all_items" class="row businessList">
    <?php 
      // Verificar si 'rows' está presente en el array
    if (isset($response['rows']) && is_array($response['rows'])) {
      foreach ($response['rows'] as $row) {
        // Acceder a los valores dentro de cada fila
        $businessId = $row['business_id'];
        $name = $row['name'];
        $url = $row['url'];
        $isActive = ($row['is_active'] == 1) ? 'Publicado' : 'Borrador';
        $isBlocked = ($row['is_blocked'] == 1) ? 'Bloqueado' : $isActive;
        ?>
        <div class=" col-6 col-md-3 col-lg-3 col-sm-3">
        <a id="<?php echo 'b-' . $businessId; ?>" class="b-list-card" href="<?php echo site_url.'admin/b/'.$businessId.'/' ?>">
          <div class="card ">
            <img class="card-img-top" src="<?php echo site_url. 'public/img/b/profile.jpg' ?>">
            <div class="card-body">
              <h5 class="mt-3 mb-0"><?php echo $name; ?></h5>
              <span class="b-status <?php echo $isBlocked; ?>"><?php echo $isBlocked; ?></span>
            </div>
          </div>
        </a>
      </div>
<?php 
      }
    } else {
      echo '<span id="noB">No  sdfsdf tienes negocios creados.</span>';
    }
    ?>    
  </div>
  <?php
   if (isset($response['total_pages'])&& $response['total_pages']>1) { 
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




   
  