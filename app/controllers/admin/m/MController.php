<?php
// Medias controller

require_once realpath( ABSPATH . "app/models/admin/m/MModel.php");

class MController {  
    protected $metasController;
    private $mModel;
    private $userController;

    public function __construct(UserController $userController = null) {
        $this->metasController = MetasController::getInstance();
        $this->userController = $userController;
        $this->mModel = new MModel();
    }

    public function initializeConfig($params) { 
        $actionName=$params['actionName'];
        if ($actionName=='index'){
            $title='Biblioteca de imágenes';
            $jsScripts=['public/js/app/admin/m/mindex.js',
        ];

    } elseif($actionName=='add'){
        $title='Añadir imagen';
        $jsScripts=['public/js/app/admin/m/madd.js',
    ];

}
else{ 
    $title='Editar imagen';
    $jsScripts=['public/js/app/admin/m/medit.js'];
}      

$this->metasController->setMetaTags([ 'title' => $title,]);
$this->metasController->setCssLinks(
    [
        'public/css/app/admin/style.css',
        'public/vendor/gicon/style.css',
        'public/css/app/admin/m/medias.css'
    ]);

$this->metasController->setJsScripts(
    array_merge(
        $jsScripts,
        [   'public/js/utils/utils.js',
        'public/js/app/admin/dashboard.js'
    ]
)
);  
}




/*funcionalidades*/


public  function addMedias() {

// Validación del token CSRF 
    $valCSRF=val_AuthCSRF();
    $userPermissions=$this->userController->userPermissions($_POST['businessID']);

    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->mModel->addMedias($_POST['businessID']);
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }

    return $response;
}


public  function getMediaDetails() {

// Validación del token CSRF 
    $valCSRF=val_AuthCSRF();
    $userPermissions=$this->userController->userPermissions($_POST['businessID']);

    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->mModel->getMediaDetails($_POST['media_id']);
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }

    return $response;
}



public function getTotalSpace($params){
    $businessId= isset($params['business_id']) ? $params['business_id'] : $_POST['businessID'];
    $userPermissions=$this->userController->userPermissions($businessId);

    if ($businessId!=null && $userPermissions) {
        $response=$this->mModel->getTotalSpace($businessId);
    }
    else {
          $response=array('success' => 'false', 'message'=>'noItemID'); ; 
    }

    return $response;
}

public function disk_capacity($params){
    $businessId= isset($params['business_id']) ? $params['business_id'] : $_POST['businessID'];
    $userPermissions=$this->userController->userPermissions($businessId);

    if ($businessId!=null && $userPermissions) {
        $response=$this->mModel->disk_capacity($businessId);
    }
    else {
          $response=array('success' => 'false', 'message'=>'noItemID'); ; 
    }

    return $response;
}


public  function getMediasList($params){
    $businessId= isset($params['business_id']) ? $params['business_id'] : $_POST['businessID'];
        $userPermissions=$this->userController->userPermissions($businessId);

    if ($businessId!=null && $userPermissions) {
        $response=$this->mModel->getMediasList($businessId);
    }
    else {
        $response='noItemID';
    }

    return $response;
}

public function getThumbnailUrl($params) {
    $mediaUrl = $params['mediaURL'];
    $size = $params['size'];
    $extension = pathinfo($mediaUrl, PATHINFO_EXTENSION);
    $fileNameWithoutExtension = str_replace("." . $extension, "", $mediaUrl);
    $thumbnailName = $fileNameWithoutExtension . '-' . $size . '.' . $extension;

    return $thumbnailName;
}

public function delMedia(){

// Validación del token CSRF 
    $valCSRF=val_AuthCSRF();
    $userPermissions=$this->userController->userPermissions($_POST['businessID']);

    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->mModel->delMedia($_POST['media_id']);
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }

    return $response;
}

public function editMedia(){

$valCSRF=val_AuthCSRF();
    $userPermissions=$this->userController->userPermissions($_POST['businessID']);

    if ($valCSRF=='okToken') {
        if ($userPermissions==true) {
             $response=$this->mModel->editMedia($_POST['media_id']);
        } else { 
            $response = array('success' => false, 'message' => 'notPermission');
        }
    } else {
        $response = array('success' => false, 'message' => $valCSRF);
    }

    return $response;

}
  


}

