<?php
// User controller


require_once realpath( ABSPATH . "app/models/user/UserModel.php");

class UserController {

  private $userModel;
  public function __construct() {
    $this->userModel = new UserModel();
    if (session_status() == PHP_SESSION_NONE) {
       session_start();
   }

 }
  

public function userPermissions($bot_id) {
   if (!isset($_SESSION['userRole'])) {
      return false;
   }
   if ($_SESSION['userRole'] !='admin') {
      $response=$this->userModel->userPermissions($bot_id);      
   } else{
      $response = true;
   }
    
    return $response;
}

public function userIsAdmin($user_id) {
   $response=$this->userModel->userIsAdmin($user_id); 
    return $response;
}

public function userRole($user_id) {
   $response=$this->userModel->userRole($user_id); 
    return $response;
}


private function getPermisosDefinidos() {
  return [
    'admin' => ['*'], // Puede todo

    /*'propietario' => [
      'producto' => ['crear', 'editar', 'borrar'],
      'tienda'   => ['editar'],
      'imagen'   => ['subir']
    ],*/

    'gestor' => [
      'project' => ['ver', 'editar'],
    ]
  ];
}
public function userCan($modulo, $accion) {
  $rol = $_SESSION['userRole'] ?? 'invitado';

  $permisos = $this->getPermisosDefinidos();

  // Admin puede todo
  if (in_array('*', $permisos[$rol] ?? [])) {
    return true;
  }

  // Si no hay permisos definidos para el módulo, denegar
  if (!isset($permisos[$rol][$modulo])) {
    return false;
  }

  return in_array($accion, $permisos[$rol][$modulo]);
}


 

}
