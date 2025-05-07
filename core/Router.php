<?php 
//core/Router.php
// Este archivo es gestionador de rutas

class Router {
   
    private $renderController;
 
    public function __construct(RenderController $renderController = null) {
        $this->renderController = $renderController;
    }

    //Determinamos que vista y acción se pide por url
    public function route() {
        $this->fixUrl(); // No es necesario para funcionar pero estéticamente es mejor
        $uri =  $this->getRelativeURI();
        $routeParams= $this->getRouteParams($uri);
        //print_r($routeParams);
        $this->toRender($routeParams);
    }


    public function getRouteParams($uri){
         //print_r($uri);
         
        
        switch ($uri[0]) {
            case '':
            $routeParams=[
                'templateName' => 'home', 
                'moduleName' => '', 
                'actionName' => 'index'
            ];
            break;

            case 'terminos-uso':
            $routeParams=[
                'templateName' => 'terms', 
                'moduleName' => '', 
                'actionName' => 'index'
            ];
            break;

            case 'admin':
            $routeParams= $this->getAdminParams($uri);
            break;

            case ($uri[0] === 'login' || strpos($uri[0], 'login?v=') === 0|| strpos($uri[0], 'login?rd=') === 0):// Verifica si $uri[0] es exactamente 'login' o comienza con 'login?v='
            $routeParams = $this->getAuthParams($uri);
            break;

         

            default: //'slug'
            //$routeParams=$this->getSlugParams($uri);
            $routeParams=[
                'templateName' => 'error', 
                'moduleName' => '', 
                'actionName' => '403'
            ];

        }
         foreach ($uri as $val) {
            if (strpos($val, '.php') !== false||strpos($val, 'app') !== false||strpos($val, 'core') !== false) {
                $routeParams=[
                'templateName' => 'error', 
                'moduleName' => '', 
                'actionName' => '403'
            ];
             }
        }
        return $routeParams;
    }

    
    private function getSlugParams($uri) {
        $templateName = 'slug';
        $moduleName = '';
        $actionName ='index';
        $businessSLug = $uri[0];
        
         if (empty($uri[1])) {
            //$actionName=$uri[0];
        }
        
        else {
        //$actionName=$uri[1];
        //comprobar aqui si tiene "?parámetros"
        //$actionName = strpos($actionName, '?') !== false ? strstr($actionName, '?', true) : $actionName;
        }

            $routeParams=[
                'templateName' => $templateName, 
                'moduleName' => $moduleName, 
                'actionName' => $actionName,
                'businessSLug' => $businessSLug,
            ];
             
         /*print_r($uri);
         print_r($routeParams);*/
        return $routeParams;
    }

    private function getAdminParams($uri) {
      // Verificar la existencia de la sesión antes de procesar la URL
    if (!isset($_SESSION['userID'])) {
        header("Location: " . site_url . "login");
        exit();
    }

    // Procesar la URL dinámicamente
     $partsCount = count($uri);

    if ($partsCount >= 2) {
        $moduleName = $uri[1];

        if ($partsCount >= 3) {
            $actionName = $uri[2];
        
            if ($actionName != 'add' && $actionName != 'edit' ){
                $actionName = 'details';
                $itemID = !empty($uri[2]) ? $uri[2] : '';
            }
            if ($partsCount >= 4 ) {
                $actionName = ($uri[2]!='edit')?'index':'edit';
                //$moduleName = ($uri[3]!='edit')?$uri[3]:$uri[1]; 
                $itemID = !empty($uri[3]) ? $uri[3] : '';

                if ($partsCount >= 5) {
                $actionName = "404";
                $moduleName = "error";
            }    
        } 
        }
    }
    $templateName = isset($templateName) ? $templateName : 'admin';
    $moduleName = isset($moduleName) ? $moduleName : '';
    $actionName = isset($actionName) ? $actionName : 'index';
    $businessID = isset($businessID) ? $businessID : '';
    $itemID = isset($itemID) ? $itemID : '';
    $mainFolder = 'admin';

   
    $routeParams= [
        'templateName' => $templateName,
        'moduleName' => $moduleName,
        'actionName' => $actionName,
        'businessID' => $businessID,
        'itemID' => $itemID,
        'mainFolder' => $mainFolder,
        
         ];
    /*echo "<pre>";
    print_r($routeParams);
    echo "</pre>"; */
    return $routeParams;
}


    private function getAuthParams($uri) {

       if (!empty($uri[1])) {
            $actionName=$uri[1];
        //comprobar aqui si tiene "?parámetros"
        $actionName = strpos($actionName, '?') !== false ? strstr($actionName, '?', true) : $actionName;
        } 
      
    
 
       
        //si está logueado no tiene sentido permitir la ruta al auth
        if (isset($_SESSION['userID'])) {
            header("Location: " . site_url . "admin");
            exit();
        }
         return [
                'templateName' => isset($templateName) ? $templateName : 'auth',
                'moduleName' => isset($moduleName) ? $moduleName : '',
                'actionName' => isset($actionName) ? $actionName : 'login',
            ];   
    }


    //Llamamos al render con los parámetros obtenidos
    public function toRender(array $routeParams, array $datas=[]) {
        /*echo "to render ";
        print_r($routeParams);
         echo "<br><br>";*/
        if (isset($routeParams['tolink'])) {
           $datas['tolink']=$routeParams['tolink'];
        }
        if (isset($routeParams['infoMsg'])) {
           $datas['infoMsg']=$routeParams['infoMsg'];
        }

        $routeParams['currentURL']=$this->getCurrentURL();
        $content = $this->renderController->renderView($routeParams, $datas);

        $this->renderController->loadTemplate($content, $routeParams);
       
    }


    public function getCurrentURL() {
        $base = dirname($_SERVER['SCRIPT_NAME']);
        $uri = site_url . ltrim(substr($_SERVER['REQUEST_URI'], strlen($base)), '/');
        return $uri;
    }

    public function getRelativeURI() {
        $base = dirname($_SERVER['SCRIPT_NAME']);
        $uri = substr($_SERVER['REQUEST_URI'], strlen($base));
        $uri = explode('?', $uri)[0];
        $uri = trim($uri, '/');
        $uri = explode('/', $uri);
        return $uri;
    }

   
    private function fixUrl() {
            if (preg_match('#/{2,}#', $_SERVER['REQUEST_URI'])) {
            $base = dirname($_SERVER['SCRIPT_NAME']);
            $uri = substr($_SERVER['REQUEST_URI'], strlen($base));
            //$fixedUrl = preg_replace('#/+#', '/', $uri);
            $fixedUrl = ltrim(preg_replace('#/+#', '/', $uri), '/');
            header("Location: " . site_url . $fixedUrl, true, 301);
            exit();
        }
    }

}
 

