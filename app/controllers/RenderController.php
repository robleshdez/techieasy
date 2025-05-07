<?php 
// RenderController.php
// Este archivo se encarga de crear la vista en pantalla
require_once realpath(ABSPATH . "app/controllers/error/ErrorController.php");
require_once realpath(ABSPATH . "app/controllers/MetasController.php");
require_once realpath(ABSPATH . "app/controllers/user/UserController.php");
 
class RenderController { 

    protected $metasController;
    protected $userController;
    protected $controller;
    protected $ajaxController;

    public function __construct() {
        $this->metasController = MetasController::getInstance();
        $this->userController = new UserController();
    }


    public function renderView(array $routeParams, array $datas=[]) {   
        //$traza = debug_backtrace();
       
       // print_r($routeParams);
         

        // Instanciar el controlador correspondiente a la vista solicitada
        $this->controller = $this->getControllerInstance($routeParams);
        
        // Cargar configuraciones específicas del controlador (si es necesario)
        $this->controller->initializeConfig($routeParams, $datas);
 
        //Cargar la vista con un contenido específico
        $content = $this->loadView($routeParams);
        
       // print_r($routeParams);

        return $content;
    }

    private function loadView(array $routeParams) {
          //echo '<pre>';
        //print_r($routeParams);
        
        //echo '</pre>';
        // Sanear la entrada para evitar posibles ataques
        $viewName = ucfirst(basename($routeParams['actionName']));
        $mainFolder = isset($routeParams['mainFolder']) ? $routeParams['mainFolder'] : $routeParams['templateName'];

        //echo $viewName;
        if ($routeParams['moduleName']=='') {
            $viewPath = realpath(ABSPATH . "app/views/".$mainFolder."/".$routeParams['templateName'].$viewName .".php");
        }   else {
            $viewPath = realpath(ABSPATH . "app/views/".$mainFolder."/".$routeParams['moduleName']."/".$routeParams['moduleName'].$viewName .".php");
        }
         

        if ($viewPath!='' && file_exists($viewPath)) {
            ob_start(); // Iniciar el búfer de salida
            require_once $viewPath; 
            return ob_get_clean(); // Obtener el contenido del búfer y limpiarlo
        } else {
            DebugMode ? $this->errorControl('404','','Vista no encontrada' ): $this->errorControl('404' );
        }
    }

    private function getControllerInstance(array $routeParams) {
       
       //var_dump($routeParams);
        $moduleName = $routeParams['moduleName']=='' ? $routeParams['templateName'] : $routeParams['moduleName'];
        $mainFolder = isset($routeParams['mainFolder']) ? $routeParams['mainFolder'] : $routeParams['templateName'];
        if ($routeParams['moduleName']=='') {
            $controllerPath = realpath(ABSPATH .'app/controllers/'.$mainFolder.'/'. ucfirst($moduleName) . 'Controller.php');
        }else{
             $controllerPath = realpath(ABSPATH .'app/controllers/'.$mainFolder.'/'.$moduleName.'/'. ucfirst($moduleName) . 'Controller.php');
        }
         
  
        if (file_exists($controllerPath)) {
        //comprobamos la existencia del controlador
            require_once $controllerPath;
            $controllerClass=ucfirst($moduleName) . 'Controller';
            $controllerInstance = new $controllerClass($this->userController);
            //var_dump($controllerInstance);
            return $controllerInstance;

        } else {
            DebugMode ? $this->errorControl('404','', 'Controlador no encontrado' ): $this->errorControl('404' );
        }
        
    }

    public function loadTemplate($content,array $routeParams) {    
        //print_r($routeParams);
       if (!file_exists(realpath(ABSPATH . 'app/views/templates/'.$routeParams['templateName'].'Template.php')))  {
            DebugMode ? $this->errorControl('404','','Template no encontrado' ): $this->errorControl('404' );
        }

        $templateName=$routeParams['templateName'];
        $viewName = $routeParams['actionName'];
        $moduleName=$routeParams['moduleName'];
        $mainFolder = isset($routeParams['mainFolder'])?$routeParams['mainFolder']:"";


        if ($routeParams['moduleName']!='' && $routeParams['templateName'] != $routeParams['mainFolder'] ){
        $bodyClass=  $mainFolder . ' ' .$mainFolder . '-' .$templateName. ' '. $moduleName .' '.$moduleName.'-'.$viewName;
        } else if ($routeParams['moduleName']!='' ){
        $bodyClass=  $mainFolder . ' '.$mainFolder . '-'.  $moduleName . ' '.  $moduleName .' '.$moduleName.'-'.$viewName;
        }
        else {
            $bodyClass=  $templateName. ' ' .$templateName.'-'.$viewName;
        }
               
        
    include realpath(ABSPATH . "app/views/templates/header.php");
    include realpath(ABSPATH . "app/views/templates/".$routeParams['templateName'].'Template.php');
    include realpath(ABSPATH . "app/views/templates/footer.php");

    }

    public function errorControl($action, $tolink="",$infoMsg=""){
        if ($tolink=="") {
            $tolink=site_url.'admin/';
        }
        $controllerInstance = new ErrorController();
        $routeParams['templateName']='error';
        $routeParams['moduleName']='';
        $routeParams['actionName']=$action;
        
        $controllerInstance->initializeConfig($routeParams,['tolink' =>$tolink,'infoMsg' =>$infoMsg]);
        $content = $this->loadView($routeParams);
        $this->errorTemplate($content, $routeParams);
        die();
    }


    public function errorTemplate($content, $routeParams=[]) { 
        $bodyClass=  $routeParams['templateName']. ' ' .$routeParams['templateName'].'-'.$routeParams['actionName'];   
        include realpath(ABSPATH . "app/views/templates/header.php");
        include realpath(ABSPATH . "app/views/templates/errorTemplate.php");
        include realpath(ABSPATH . "app/views/templates/footer.php");
    }
 
    public function ajaxToRender(array $routeParams) {
        $html = $this->renderView($routeParams);
        echo $html;
    }

    //extraemos los datos del método específico del controlador instanciado desde el RenderControler
    public function getDatas ($method='', array $params = []){
        if (method_exists($this->controller, $method))         {
            $datas = $this->controller->$method($params);
        } else {
            $datas = ['Nada que decir'];
        }
        return $datas; 
    }
   

}

