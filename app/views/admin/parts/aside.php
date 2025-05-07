<?php 

// ['tipo','link','icono','title','id']
// el tipo puede ser heading, item, menu
//

// menu: id del submenu, el icono y nombre, y los subitems


$adminSidebar=[
  ['item',site_url.'admin/','gicon-dashboard','Escritorio'],
  ['heading','Proyectos'],
  ['item',site_url.'admin/p/','gicon-user','Proyectos'],
  ['item',site_url.'admin/p/add','gicon-plus','Añadir Proyecto' ,'add_proyect'],
  ['heading','Trabajadores'],
  ['item',site_url.'admin/t/','gicon-user','Trabajadores'],
  ['item',site_url.'admin/t/add','gicon-plus','Añadir Trabajador' ,'add_worker'],
];

$gestorSidebar=[
  ['item',site_url.'admin/','gicon-dashboard','Escritorio'],
  ['heading','Proyectos'],
  ['item',site_url.'admin/p/','gicon-user','Proyectos'],
  ['heading','Trabajadores'],
  ['item',site_url.'admin/t/','gicon-user','Trabajadores'],
  ['item',site_url.'admin/t/add','gicon-plus','Añadir Trabajador' ,'add_worker'],

 ];
 
$userSidebar=[
  /*['heading','Bots'],
  ['item',site_url.'admin/b/','gicon-bots','Mis Bots'],
  ['item','#','gicon-plus','Añadir Bot' ,'add_bot'],
  //['item',site_url.'admin/b/gestor','admin','Bots administrados'],*/
];

$accountSidebar =[
  ['heading','Cuenta'],
  //['item','#','gicon-user','Editar perfil'],
  ['item','#','gicon-exit','Salir' ,'slogout'],
];

$submenu =[
  ['menu','components-nav','admin', 'Submenu',
    [
      ['item','#','info','item 1'],
      ['item','#','check','item 2'],
      ['item','#','info','item 3'],
      ['item','#','check','item 4'],
    ]
  ],
];

 ?>




<!-- ======= Sidebar ======= -->

<ul class="sidebar-nav" id="sidebar-nav">
  <li class="d-grid justify-content-end pt-0 pb-2"><i class="gicon-arrowrw toggle-sidebar-btn d-none d-md-block pe-4"></i></li>


<?php
if ($_SESSION['userRole']=="admin") {
buildMenu($adminSidebar,$routeParams['currentURL']);
}
else   {
 buildMenu($gestorSidebar,$routeParams['currentURL']);
}


//buildMenu($submenu);  
buildMenu($userSidebar,$routeParams['currentURL']); 
buildMenu($accountSidebar,$routeParams['currentURL']);  



?>
 </ul>
  </aside><!-- End Sidebar-->
  <?php 


 