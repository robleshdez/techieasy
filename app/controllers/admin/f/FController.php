<?php
// Flow controller


require_once realpath( ABSPATH . "app/models/admin/f/FModel.php");

class FController {  
 protected $metasController;
 private $bModel;
 private $userController;


 public function __construct(UserController $userController = null) {
  $this->metasController = MetasController::getInstance();
  $this->userController = $userController;
  $this->fModel = new FModel();
 
}

public function initializeConfig($params) { 
    
    $actionName=$params['actionName'];
    if ($actionName=='edit'){ 
    $title='Editar flujo';
    $jsScripts=[
        'public/vendor/jquery/jquery-ui.min.js',
        'public/vendor/jquery/jquery.ui.touch-punch.min.js',
        'public/js/app/admin/f/fedit.js',
    ];

}      

$this->metasController->setMetaTags([ 'title' => $title,]);
$this->metasController->setCssLinks(
    [
       'public/vendor/bebots/style.css',
       'public/vendor/jquery/jquery-ui.css',
       'public/css/app/admin/b/business.css',
       'public/css/app/admin/admin.css',

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


public  function addFlow(){ // falta el user permision
         // Validación de datos enviados
   if (empty($_POST['botID'])  ) {
      $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
  }
     // Validación del token CSRF 
  $valCSRF=val_CSRF();
  if ($valCSRF=='okToken') {
            $response=$this->fModel->addFLow($_POST['botID']); 
  } else {
            $response = array('success' => false, 'message' => $valCSRF);
  }

  return $response;
}


public function delFlow(){

// Validación del token CSRF 
    $valCSRF=val_CSRF();
    $userPermissions=$this->userController->userPermissions($_POST['botID']);
    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->fModel->delFlow($_POST['flow_id']);
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }

    return $response;
}


public  function getFlowDetails($params) {
    $valCSRF=val_CSRF($params);
    $userPermissions=$this->userController->userPermissions($params['botID']);
    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->fModel->getFlowDetails($params);
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}

/*helpers*/

// Función para procesar el contenido y aplicar formato similar a WhatsApp
public function json2html($params) {
    // Convertir saltos de línea (\n) en <br>
    $content = nl2br(htmlspecialchars($params['content']));

    // Aplicar formato para negrita, cursiva y tachado usando expresiones regulares
    $content = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $content); // Negrita
    $content = preg_replace('/_(.*?)_/', '<em>$1</em>', $content);          // Cursiva
    $content = preg_replace('/~(.*?)~/', '<del>$1</del>', $content);        // Tachado

    return $content;
}


public  function getFlowTypes() {
    $response=$this->fModel->getFlowTypes(); 
    return $response;
}


public  function updateFlowName(){
         // Validación de datos enviados
   if (empty($_POST['flow_name']) ) {
      $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
  }
  

  $valCSRF=val_CSRF();
  $userPermissions=$this->userController->userPermissions($_POST['botID']);
 
    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->fModel->updateFlowName();
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}


public  function updateFlowDatas(){
         // Validación de datos enviados

   if (empty($_POST['messages']) ) {
      $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
  }
  
  $valCSRF=val_CSRF();
  $userPermissions=$this->userController->userPermissions($_POST['botID']);
 
    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->fModel->updateFlowDatas();
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }
    return $response;
}


public  function editNext() {
    $response=$this->fModel->editNext(); 
    return $response;
}



 /*fin de la clase*/
}

 