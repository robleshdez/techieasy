<?php 

// index.php
// Este archivo es la entrada principal de la aplicación

require_once 'core/Utils.php';

$site_url=guess_url();
if (!file_exists('core/Config.php') && file_exists('install.php')){
    header('Location:' .$site_url.'install.php');
    exit();
} elseif (!file_exists('core/Config.php') && !file_exists('install.php')) {

  echo '
  <!DOCTYPE html>
  <html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width">
  <title>Error de Configuración</title>
  <link href="'.$site_url.'public/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="'.$site_url.'public/css/common.css" rel="stylesheet">
  <link href="'.$site_url.'public/css/404/404.css" rel="stylesheet">
  </head>
  <body class="h-100" cz-shortcut-listen="true" style="line-height:1.5;">
  <main class="py-5">
  <div class="container">
  <div class="row justify-content-center">
  <div id="p404" class="col-12 col-lg-8 ">
  <div class="card  ">
  <span  class="d-block fs-error">404</span>
  <p class="lead border-bottom">Servicio no disponible</p>
  <p>Lo sentimos, pero no hemos encontrado el archivo <code>config.php</code>. Se ha intentado iniciar el asistente de instalación sin éxito, probablemente debido a la ausencia del archivo <code>install.php</code>.</p> 
  <p>Por favor, asegúrate de que el archivo <code>install.php</code> exista o intenta crear <code>config.php</code> de forma manual.</p>
  </div>       
  </div> 
  </div>
  </div>
  </main>
  </body>
  </html>';
  die();
}

session_start();

// Incluimos algunos archivos necesarios para el funcionamiento
require_once 'core/Load.php';
require_once 'core/Router.php';
require_once 'app/controllers/RenderController.php';
require_once 'app/controllers/AjaxController.php';
require_once 'app/controllers/wapi/WapiController.php';
require_once 'app/controllers/AsyncController.php';
require_once 'app/controllers/EmailController.php';

 

  

// Generar token CSRF 
if (!isset($_SESSION['csrfToken']) ) {
    $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
} 
// Actualizar la última actividad del usuario
if (isset($_SESSION['lastActivity'])) {
    $_SESSION['lastActivity'] = time();
}

$renderController = new RenderController(); // instancialmos al Render
$router = new Router($renderController); // instanciamos al router y le inyectamos al render
$emailController=new EmailController();

/*$async = new Async();
$async->create(function() {
    $mailed=$emailController->sendMail('juankar@gorvet.com', 'asunto3','q onda carnal' );
    // Formatear los datos
    $logFile = 'Async.txt';
    $logData = [
        'hour' => date('H:i:s'),
        'datos' => $mailed,
    ];
    file_put_contents($logFile, print_r($logData, true), FILE_APPEND);
                 
});*/

$error_code = $_GET['error_code'] ?? $_SERVER['ERROR_CODE'] ?? null;
$error_response_code = (http_response_code() !== null && http_response_code() !== 200) 
    ? http_response_code() 
    : ($error_code !== null ? $error_code : null);

//echo $error_code;
//echo $error_response_code;
// Lógica para manejar la solicitud según sea AJAX o no
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Si es una solicitud AJAX para mostrar error
    if (isset($_POST['errorType'])) {
    $routeParams = [
        'templateName' => 'error',
        'moduleName' => '',
        'actionName' => isset($_POST['errorType']) ? $_POST['errorType'] : '404'
    ];
     $datas = [
        'tolink' => isset($_POST['tolink']) ? $_POST['tolink'] : '',
        'infoMsg' => isset($_POST['infoMsg']) ? $_POST['infoMsg'] : '',
    ];
    $router->toRender($routeParams,$datas); 
    }
      
} 


elseif ($error_response_code === 403 || $error_response_code === 404 || $error_response_code === 500 || $error_response_code === 503 ) {
     $routeParams = [
    'templateName' => 'error',
    'moduleName' => '',
    'actionName' => $error_response_code,
];
$datas = [
        'tolink' => site_url,
    ];

$router->toRender($routeParams,$datas);
} else {
    // Si no es una solicitud AJAX, ni es error, realizar el enrutamiento normal
    $router->route();
}

/*$async = new Async();

    $async->create(function () {
    $webSocketServer = new WebSocketController(301);
    $webSocketServer->start();
});
*/
 
           