<?php
// Boots controller


require_once realpath( ABSPATH . "app/models/admin/b/BModel.php");

class BController {  
 protected $metasController;
 private $bModel;
 private $userController;


 public function __construct(UserController $userController = null) {
  $this->metasController = MetasController::getInstance();
  $this->userController = $userController;
  $this->bModel = new BModel();
 
}

public function initializeConfig($params) { 
    $actionName=$params['actionName'];
    if ($actionName=='index'){
        $title='Mis Bots';
        $jsScripts=['public/js/app/admin/b/bindex.js',
    ];

      

} elseif($actionName=='gestor'){
    $title='Administrar bots';
    $jsScripts=[];

} else{ 
    $title='Editar bot';
    $jsScripts=[
        'public/vendor/intlTelInput/js/intlTelInputWithUtils.min.js',
        'public/vendor/qrcode/qrcode.min.js',
        'public/js/app/admin/b/bedit.js',
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
        
        $this->metasController->setFooterCredits($credits);


 
}


/*funcionalidades*/



public  function getOwnBotList(){ 
     // Validación del token CSRF 
    $valCSRF =  val_CSRF();
    $userID= isset($params['userID']) ? $params['userID'] : $_SESSION['userID'];
    if ($valCSRF=='okToken') {
        $response=$this->bModel->getOwnBotList($userID); 
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    
    return $response;
}

 

public  function addBot(){ /// falta el user permision
         // Validación de datos enviados
   if (empty($_POST['bot_name'])  || empty($_POST['bot_category_val'])) {
      $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
  }
     // Validación del token CSRF 
  $valCSRF=val_CSRF();
  if ($valCSRF=='okToken') {
            $response=$this->bModel->addBot($_SESSION['userID']); 
  } else {
            $response = array('success' => false, 'message' => $valCSRF);
  }

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
             $response=$this->bModel->updateBot();
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}

public  function updateBotStatus(){
         // Validación de datos enviados
 
  $valCSRF=val_CSRF();
  $userPermissions=$this->userController->userPermissions($_POST['botID']);
 
    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->bModel->updateBot();
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}
 


public  function getBotCategoryList() {
    $response=$this->bModel->getBotCategoryList(); 
    return $response;
}

public  function getBotTvsC() {
    $response=$this->bModel->getBotTvsC(); 
    return $response;
}

//recibe 
public  function getBotDetails($params) {

    $valCSRF=val_CSRF($params);
    $userPermissions=$this->userController->userPermissions($params['itemID']);
    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->bModel->getBotDetails($params['itemID']);
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
             $response=$this->bModel->delBot();
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }

    return $response;
}





 /* metodos para los flujos q se optienen desde la vista del bot*/
 
public  function getFlowTvsC($params) {
    $response=$this->bModel->getFlowTvsC(($params['itemID'])); 
    return $response;
}


public  function getBotFLowList($params){ 
     // Validación del token CSRF 
    $valCSRF =  val_CSRF();
    $userID= isset($params['userID']) ? $params['userID'] : $_SESSION['userID'];
    $botID= isset($params['itemID']) ? $params['itemID'] : $_POST['botID'];
    if ($valCSRF=='okToken') {
        $response=$this->bModel->getBotFLowList($botID); 
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    
    return $response;
}




public  function updateWtsCnt(){
         // Validación de datos enviados
    
  
  $valCSRF=val_CSRF();
  $userPermissions=$this->userController->userPermissions($_POST['botID']);
 
    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->bModel->updateWtsCnt();
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}

 




/* fin de la clase*/

}

 