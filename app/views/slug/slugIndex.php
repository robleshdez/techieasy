<!-- En /proyecto/app/views/admin/index.php -->
<h1>Bienvenido al slugview</h1>

<?php var_dump($routeParams) ?>

<?php $template= $this->metasController->getMetaTag('template');

require_once realpath(ABSPATH . "app/views/slug/template/".$template.".php");

 ?>