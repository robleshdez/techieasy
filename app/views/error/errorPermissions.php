 
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-8 d-flex justify-content-center pb-3">

				<img src="<?php echo site_url.  '/public/img/logo.png' ?>" class="img-fluid logo-404">
			</div>

			<div id="p404" class="col-12 col-lg-8 ">
				<div class="card  ">
					<span  class="d-block fs-error">¡Ops!</span>
					<p class="lead border-bottom my-2"></p>
					<p class="lead2 my-3">Lo sento, parece que no tienes suficientes permisos para realizar esta acción.</p>
					<a class="btn btn-primary  mx-auto d-block" href="<?php echo $this->metasController->getMetaTag('tolink')?>">Regresar</a>
				</div>
			</div>
		</div>
	</div>
 