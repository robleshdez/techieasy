<?php 
//core/Router.php
//Este archivo se encarga de analizar la petición de la url 
//para saber que ruta despachar e indicarle al render lo que debe mostrar

class Router {
   
    private $renderController;
 
    public function __construct(RenderController $renderController = null) {
        $this->renderController = $renderController;
    }

    //Determinamos que vista y acción se pide por url
    public function route() {
        $this->fixUrl(); //arreglamos la url
        $uri =  $this->getRelativeURI();//seccionamos la url en partes
        $routeParams= $this->getRouteParams($uri);//obtenemos los parámetros de ruta para el render
        //print_r($routeParams);
        $this->toRender($routeParams);//mandamos a renderizar
    }


    private function getRouteParams($uri){
    
        // Soporte para idioma opcional (/es/..., /en/..., etc.)
        $locale = in_array($uri[0], ['es', 'en']) ? array_shift($uri) : null;
        $mainFolder   = isset($uri[0]) ? ($uri[0] === 'login' ? 'auth' : $uri[0]): '';
        $templateName = $mainFolder;
        $moduleName   = '';
        $actionName   = isset($uri[0]) ? ($uri[0] === 'login' ? 'login' : 'index'): 'index';;
        $businessID   = '';
        $itemID       = '';
        $slug         = '';



        //locaciones que no quiero que se acceda por url
        //esto se usa en caso de que el servidor no gestione correctamente
        //las denegaciones por htacces
        foreach ($uri as $val) {
            if (strpos($val, '.php') !== false||strpos($val, 'app') !== false||strpos($val, 'core') !== false) {
                $routeParams=[
                'templateName' => 'error', 
                'moduleName' => '', 
                'actionName' => '403'
            ];
            return  $routeParams;
             }
        }

        // Si no hay segmentos, ir a login o home según lógica personalizada
        if (empty($uri[0])) {
            // si lo quieres con home (true)o directo al login (false)
             $routeParams= false?[
                'templateName' => 'home',
                'moduleName' => '',
                'actionName' => 'index'
            ]:$this->getAuthParams($uri);

            return  $routeParams;
        }
         

        // Excepciones definidas (admin, login, api, etc.) 
        // que no son rutas slug
        $noSlugRoutes = ['admin', 'login', 'api','terminos-uso'];

        if (!empty($uri[0])&&!in_array($uri[0], $noSlugRoutes)) {
            // Si no está en excepciones, se trata como slug
            $routeParams= [
                'templateName' => 'slug',
                'moduleName' => '',
                'actionName' => 'index',
                'slug' => $uri[0]
            ];
        return  $routeParams;
        }


        // Rutas que necesitan estar logueados
        $needLoginRoutes = ['admin'];
        if (!empty($uri[0])&&in_array($uri[0], $needLoginRoutes)&&!isset($_SESSION['userID'])) {
            //header("Location: " . site_url . "login");
            //exit();
        }

        //a donde voy si intento hacer el login estando logueado
        if ($uri[0]==='login'&&isset($_SESSION['userID'])) {
            header("Location: " . site_url . "admin");
            exit();
        }
        

        // Dispatcher por niveles según segmentos
       
         if (count($uri) === 2) {
            $templateName = $mainFolder;
            $actionName   = $uri[0] === 'login' ? $uri[1] : 'index';
            $moduleName =   $uri[0] === 'login' ? '' : $uri[1];
            echo "string";

        } elseif (count($uri) === 3) {
            $templateName = $mainFolder;
            $moduleName = $uri[1];
            $businessID = $uri[2];

        } elseif (count($uri) === 4) {
            $templateName = $uri[1];
            $moduleName = $uri[3];
            $businessID = $uri[2];

        } elseif (count($uri) === 5) {
            $templateName = $uri[1];
            $moduleName = $uri[3];
            $actionName = 'details';
            $businessID = $uri[2];
            $itemID = $uri[4];

        } elseif (count($uri) === 6) {
            $templateName = $uri[1];
            $moduleName = $uri[3];
            $actionName = $uri[4];
            $businessID = $uri[2];
            $itemID = $uri[5];
        }

  $routeParams= [
            'locale'       => $locale,
            'mainFolder'   => $mainFolder,
            'templateName' => $templateName,
            'moduleName'   => $moduleName,
            'actionName'   => $actionName,
            'businessID'   => $businessID,
            'itemID'       => $itemID
        ];      
 
         echo "<routerparams>";
    echo "<pre>";
    print_r($routeParams);
    echo "</pre>"; 
    return $routeParams;
    }

 /*   
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
            ];*/
             
         /*print_r($uri);
         print_r($routeParams);*/
    /*    return $routeParams;
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
    echo "<pre>";
    print_r($routeParams);
    echo "</pre>"; 
    return $routeParams;
}
*/

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


    private function getCurrentURL() {
        $base = dirname($_SERVER['SCRIPT_NAME']);
        $uri = site_url . ltrim(substr($_SERVER['REQUEST_URI'], strlen($base)), '/');
        return $uri;
    }

    private function getRelativeURI() {
        $base = dirname($_SERVER['SCRIPT_NAME']);
        $uri = substr($_SERVER['REQUEST_URI'], strlen($base));
        $uri = explode('?', $uri)[0];
        $uri = trim($uri, '/');
        $uri = explode('/', $uri);
        return $uri;
    }

    // No es necesario para funcionar pero estéticamente es mejor
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
 

