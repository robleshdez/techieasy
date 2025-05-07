<?php 
// ['tipo','link','icono','title','id']
// el tipo puede ser heading, item, menu
//

// menu: id del submenu, el icono y nombre, y los subitems


$adminSidebar=[
  ['item',site_url.'admin/','gicon-dashboard','Escritorio'],
];

 
$userSidebar=[
  ['heading','Mis Bots'],
  ['item',site_url.'admin/b/','gicon-bots','Mis Bots'],
  ['item',site_url.'admin/b/'.$routeParams['businessID'],'gicon-edit','Editar Bot'], 
  ['item','#','gicon-chat','Añadir Flujo' ,'sAdd_flow'],
 
];

 

$accountSidebar =[
  ['heading','Cuenta'],
  //['item','#','gicon-user','Editar perfil'],
  ['item','#','gicon-exit','Salir' ,'slogout'],
];



 



$productSidebar=[
  ['heading','Productos'],
  ['item',site_url.'admin/b/','admin','Todos los Productos'],
  ['item',site_url.'admin/b/add/','admin','Añadir Producto'],
];
$mediasSidebar=[
  ['heading','Biblioteca de imágenes'],
  ['item',site_url.'admin/b/'.$routeParams['businessID'].'/m/','admin','Todas las Imágenes'],
  ['item',site_url.'admin/b/'.$routeParams['businessID'].'/m/add/','admin','Añadir Imágen'],
];

 

 ?>




<!-- ======= Sidebar ======= -->

<ul class="sidebar-nav" id="sidebar-nav">
 <li class="d-grid justify-content-end pt-0 pb-2"><i class="gicon-arrowrw toggle-sidebar-btn d-none d-md-block pe-4"></i></li>

<?php
buildMenu($adminSidebar,$routeParams['currentURL']);
buildMenu($userSidebar,$routeParams['currentURL']); 
//buildMenu($productSidebar); 
//buildMenu($mediasSidebar); 
buildMenu($accountSidebar); 


?>
 </ul>
  </aside><!-- End Sidebar-->
  <?php 


