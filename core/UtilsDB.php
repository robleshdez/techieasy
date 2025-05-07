<?php 
// core/UtilsDB.php
// Algunas funciones de ayuda que son usadas en varios archivos

if (file_exists(realpath( __DIR__.'/Config.php'))) {
require_once realpath( __DIR__.'/Config.php');
}
/*Funciones comunes para consultar y usar la db*/
function getPDOInstance(){
 $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME. ";charset=utf8mb4", DB_USER, DB_PASSWORD);
 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 return $pdo;
}






// Validación del CSRF 
function val_CSRF($params=[]){

  if (isset($_POST['middle_name']) && $_POST['middle_name']!="") {//q coj hice aqui
  return 'noToken'; 
  }

 $inCsrfToken = $_POST['csrfToken'] ?? $params['csrfToken'] ?? null;
 $inCsrfTimestamp = $_POST['csrfTimestamp'] ?? $params['csrfTimestamp'] ?? null;

 
if ($inCsrfToken !== null && $inCsrfTimestamp !== null) {

    $csrf_token = $inCsrfToken;
    $csrf_timestamp = $inCsrfTimestamp;
     //var_dump($_POST['csrfToken']);
     //var_dump($_POST['csrfTimestamp']);
    // Verifica si el token CSRF y la marca de tiempo coinciden con los valores almacenados en la sesión
     if (!isset($_SESSION['csrfToken']) || !isset($_SESSION['csrfTimestamp'])) {
      return 'toLogin'; 
      }
    if ($csrf_token === $_SESSION['csrfToken'] && (int)$csrf_timestamp === (int)$_SESSION['csrfTimestamp']) {
      //si estoy aqui es q hay actividad por tanto se aprovecha y actualiza lastActivity
      $_SESSION['lastActivity'] = time();
      return 'okToken'; 
    }
    else {// El token CSRF y/o la marca de tiempo no son válidos
      // Verifica si la marca de tiempo es anterior a la marca de tiempo almacenada en la sesión
      if ($csrf_timestamp < $_SESSION['csrfTimestamp']) {
        // La marca de tiempo es anterior, esto significa que la sesión se cerró y abrió en otra pestaña
        return 'toLogin'; 
      }
      else {
      // La marca de tiempo es posterior, esto significa que el token CSRF no coincide debido a un ataque CSRF
        return 'noToken'; 
      }
    }
  }

  else {// No se recibió un token CSRF y/o una marca de tiempo 
    return checkOrigin(); 
  }
}


function checkOrigin(){
  if (isset($_SERVER['HTTP_REFERER'])) {
     if (strpos($_SERVER['HTTP_REFERER'], site_url) !== false) {
         return 'okToken'; //echo "La solicitud vino desde el mismo sitio.";
    } else {
         return 'noToken'; //echo "La solicitud parece provenir de otro dominio.";
    }
} else {
     return 'noToken'; //echo "No se pudo determinar el origen de la solicitud (HTTP_REFERER no está presente).";
}
}

function val_AuthCSRF(){
   if (isset($_POST['middle_name']) && $_POST['middle_name']!="") {

  return 'noToken'; 
  }
  if (!empty($_POST)) {   
  if (isset($_POST['csrfToken'])&& isset($_SESSION['csrfToken'])) {
    $csrf_token = $_POST['csrfToken'];
    if ($csrf_token === $_SESSION['csrfToken']) {
      return 'okToken'; 
    }
    else { 
      return 'noToken'; 
    }
  } 
  else {// No se recibió un token CSRF y/o una marca de tiempo 
    return 'noToken'; 
  }
} else{return 'okToken';}


}


