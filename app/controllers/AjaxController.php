<?php 
// AjaxController.php
 
require_once realpath( __DIR__.'/../../core/Load.php');
require_once realpath(ABSPATH . "app/controllers/MetasController.php");
require_once realpath(ABSPATH . "app/controllers/user/UserController.php");
//require_once realpath(ABSPATH . "app/controllers/AsyncController.php");


class AjaxController { 

    protected $userController;
    private $controllerInstance;
   
    public function __construct() {
          $this->metasController = MetasController::getInstance();
          $this->userController = new UserController();
    }

    public function getAjaxRoute(){
        $this->controllerInstance = $this->getAjaxControllerInstance($_POST['controller'] );
        //var_dump($_POST['controller']);
        $params = isset($_POST['params'])? $_POST['params']:[];
        $response = $this->getDatas ($_POST['action'] ,$params);
        header('Content-Type: application/json');
        echo json_encode($response);
        

    }



    public function getAjaxRelativeURI() {
        $uri = trim($_POST['currentURL'], '/');
        $uri = explode('/', $uri);
        return $uri;
    }

 

    private function getAjaxControllerInstance($controller) {
       
        $controllerPath = realpath(ABSPATH .'app/controllers/'.$controller.'.php');
  
        if (file_exists($controllerPath)) {
        //comprobamos la existencia del controlador
            require_once $controllerPath;
             $controllerClass=pathinfo($controllerPath, PATHINFO_FILENAME);
             $controllerInstance = new $controllerClass($this->userController);
            return $controllerInstance;
        } 
        
    }

    

    //extraemos los datos del método específico del controlador instanciado desde el RenderControler
    public function getDatas ($method='',  $params = []){
       // var_dump($this->controllerInstance);
        if (method_exists($this->controllerInstance, $method))         {
            $datas = $this->controllerInstance->$method($params);
        } else {
            $datas = ['Nada que decir'];
        }
        return $datas; 
    }
    

}

 

// Manejar la solicitud Ajax
if ($_SERVER['REQUEST_METHOD'] === 'POST') {;
    if (isset($_POST['action'])) {
        //$router = new Router(); 
        $ajaxController = new AjaxController();
        $ajaxController->getAjaxRoute(); 
    }  
}