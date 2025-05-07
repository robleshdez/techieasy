<?php

//admin controller: dashboard

require_once realpath( ABSPATH . "app/models/admin/AdminModel.php");

class AdminController {  
   protected $metasController;
   private $adminModel;
   //private $userController;

   public function __construct() {
      $this->metasController = MetasController::getInstance();
      //$this->userController = $userController;
      $this->adminModel = new AdminModel();
   }

   public function initializeConfig($actionName) {        
// Configuraciones específicas del controlador Admin
      $this->metasController->setMetaTags(['title' => 'Admin']);
      $this->metasController->setCssLinks([
         'public/css/app/admin/admin.css',
         'public/vendor/bebots/style.css',
         //'public/vendor/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css'
      ]);
      $this->metasController->setJsScripts([
         'public/js/app/admin/dashboard.js',
      ]);

      $credits=' 
        <div class="copyright">
         <span><strong>Botzy</strong> La mejor experiencia para automatizar tu negocio.</span>
        </div>';
        
        $this->metasController->setFooterCredits($credits);
      

   }

  /*Funcionalidades*/ 



//obtener el total de usuarios por estatus
public  function getTotalUser(){ 
     // Validación del token CSRF 
    $valCSRF =  val_CSRF();
    $userID= isset($params['userID']) ? $params['userID'] : $_SESSION['userID'];
    if ($valCSRF=='okToken') {
        $response=$this->adminModel->getTotalUser($userID); 
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}
//obtener el total de bots
public  function getTotalBots(){ 
     // Validación del token CSRF 
    $valCSRF =  val_CSRF();
    $userID= isset($params['userID']) ? $params['userID'] : $_SESSION['userID'];
    if ($valCSRF=='okToken') {
        $response=$this->adminModel->getTotalBots($userID); 
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}

}
