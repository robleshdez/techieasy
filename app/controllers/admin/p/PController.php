<?php
// Projects controller


require_once realpath( ABSPATH . "app/models/admin/P/PModel.php");

class PController {  
 protected $metasController;
 private $pModel;
 private $userController;


 public function __construct(UserController $userController = null) {
  $this->metasController = MetasController::getInstance();
  $this->userController = $userController;
  $this->pModel = new PModel();
 
}

public function initializeConfig($params) { 
    $actionName=$params['actionName'];
    if ($actionName=='index'){
        $title='Proyectos';
        $jsScripts=['public/js/app/admin/p/pindex.js',
    ];

      

} elseif($actionName=='add'){
    $title='Añadir Proyecto';
     $jsScripts=[
        'public/js/app/admin/p/padd.js',
    ];

} else{ 
    $title="editar";
    $jsScripts=[
        'public/js/app/admin/p/pedit.js',
    ];

}      

$this->metasController->setMetaTags([ 'title' => $title,]);
$this->metasController->setCssLinks(
    [
       'public/vendor/bebots/style.css',
       'public/vendor/intlTelInput/css/intlTelInput.min.css',
       'public/css/app/admin/admin.css',
       'public/css/app/admin/b/business.css',
   ]);

$this->metasController->setJsScripts(
   array_merge(
    $jsScripts,
    [ 'public/js/app/admin/dashboard.js'
]
)
); 

$credits=' 
        <div class="copyright">
         <span><strong>Botzy</strong> La mejor experiencia para automatizar tu negocio.</span>
        </div>';
        
        //$this->metasController->setFooterCredits($credits);


 
}


/*funcionalidades*/



public  function getCurrencies(){ 
    $response=$this->pModel->getCurrencies(); 
    return $response;
}

public  function getWorkers(){ 
     // Validación del token CSRF 
        $response=$this->pModel->getWorkers();
        return $response;
}

public  function getGestors(){ 
     // Validación del token CSRF 
        $response=$this->pModel->getGestors();
        return $response;
}
 
 
public  function addProject(){ /// falta el user permision
         // Validación de datos enviados
   if (empty($_POST['project_name'])  || empty($_POST['description'])) {
      $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
  }
     // Validación del token CSRF 
  $valCSRF=val_CSRF();
  if ($valCSRF=='okToken') {
            $response=$this->pModel->addProject(); 
  } else {
            $response = array('success' => false, 'message' => $valCSRF);
  }

  return $response;

} 
 
 
public  function getProjectsForUser($params){ 
     // Validación del token CSRF 
    $valCSRF =  val_CSRF();
    $userID= isset($params['userID']) ? $params['userID'] : $_SESSION['userID'];
    $userRole= isset($params['userRole']) ? $params['userRole'] : $_SESSION['userRole'];
    if ($valCSRF=='okToken') {
        $response=$this->pModel->getProjectsForUser($userID,$userRole); 
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    
    $response['projects'] = $response['rows'] ?? []; 
    ob_start();
     include realpath( ABSPATH .  'app/views/admin/p/_list.php');
   $response['html']=ob_get_clean();

    return $response;
}




public  function updateBot(){
         // Validación de datos enviados
   if (empty($_POST['bot_name']) ) {
      $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
  }
  

  $valCSRF=val_CSRF();
  $userPermissions=$this->userController->userPermissions($_POST['botID']);
 
    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->pModel->updateBot();
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}


//recibe 
public  function getBotDetails($params) {

    $valCSRF=val_CSRF($params);
    $userPermissions=$this->userController->userPermissions($params['itemID']);
    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->pModel->getBotDetails($params['itemID']);
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}


public function delBot(){

// Validación del token CSRF 
    $valCSRF=val_CSRF();
    $userPermissions=$this->userController->userPermissions($_POST['botID']);

    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->pModel->delBot();
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }

    return $response;
}






public  function getBotFLowList($params){ 
     // Validación del token CSRF 
    $valCSRF =  val_CSRF();
    $userID= isset($params['userID']) ? $params['userID'] : $_SESSION['userID'];
    $botID= isset($params['itemID']) ? $params['itemID'] : $_POST['botID'];
    if ($valCSRF=='okToken') {
        $response=$this->pModel->getBotFLowList($botID); 
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    
    return $response;
}








/* fin de la clase*/

}

 