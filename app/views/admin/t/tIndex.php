<div class="pagetitle ">
	<div class="d-flex align-items-start">
		<div class="d-inline-block me-2">
			<h1 class="me-3">Mis Bots</h1>
		<?php $response = $this->getDatas('getBotTvsC') ;
  //print_r($response); ?>
  <span class="d-block">Bots creados: <?php echo $response['total_bots'] ?> de <?php echo $response['can_have']?>
  <?php if ($response['total_bots']>=$response['can_have']) {
    echo '<a href="#">Mejorar mi plan</a>';
  } ?> 
</span>
		</div>
<div class="d-inline-block ms-md-4">
	<a id="submit_add_bot" class="btn btn-primary mt-3 mt-md-0" >Añadir Bot</a>
</div>

	</div>
	
	  
</div><!-- End Page Title -->

<form id="tokens">
	<input type="hidden" class="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
	<input type="hidden" class="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
</form>
<?php 
$response = $this->getDatas('getOwnBotList') ;
 
?>
<div class="container mt-3">
	<div id="all_items" class="row botList">
		<?php 
			// Verificar si 'rows' está presente en el array
		if (isset($response['rows']) && is_array($response['rows'])) {
 			foreach ($response['rows'] as $row) {
        // Acceder a los valores dentro de cada fila
				$botId = $row['bot_id'];
				$name = $row['name'];
				$isActive = ($row['is_active'] == 1) ? 'Activo' : 'Inactivo';
				$isBlocked = ($row['is_blocked'] == 1) ? 'Bloqueado' : $isActive;
				?>
				<div class=" col-12 col-md-3 col-lg-3 col-sm-3">
				<a id="<?php echo 'b-' . $botId; ?>" class="" href="<?php echo site_url.'admin/b/'.$botId.'/' ?>">
					<div class="card b-list-card">
 						<div class="card-body">
 							<div class="d-flex justify-content-between">
							<h5 class="mt-0 mb-0"><?php echo $name; ?></h5>
							<div class="action"><i class="gicon-config   d-block"> </i></div>
							</div>
							<span class="b-status <?php echo $isBlocked; ?>"><?php echo $isBlocked; ?></span>
						</div>
					</div>
				</a>
			</div>
<?php 
			}
		} else {
			echo '<span id="noB">No tienes Bots creados.</span>';
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




	 
	