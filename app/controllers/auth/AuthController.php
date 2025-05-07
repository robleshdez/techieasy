<?php
// app/controllers/AuthController.php
// Controlador para la autenticación

//require_once realpath( __DIR__.'/../../../core/Load.php');
//require_once realpath( ABSPATH . "app/controllers/MetasController.php");
require_once realpath(ABSPATH . "app/controllers/AsyncController.php");
require_once realpath( ABSPATH . "app/controllers/EmailController.php"); 
require_once realpath( ABSPATH . "app/models/auth/AuthModel.php");


class AuthController { 

    protected $metasController; // Para modificar las metas generales de la vista
    private $authModel; // Para conectar con su modelo correspondiente
    private $emailController; // Para el controlador para envío de correos
  

    public function __construct() {
        $this->metasController = MetasController::getInstance();
        $this->authModel = new AuthModel();
        $this->emailController =  new EmailController();      
    }

// Iniciamos las metas generales del template para login/register/lostPasw, etc. En esta vista el login es similar al index en las otras.

// Configuraciones específicas del controlador Auth

    public function initializeConfig($params) {
        $actionName=$params['actionName'];
        $authJS=ucfirst($actionName);//para cargar el js correspondiente a la vista

        if ($actionName=='register'){
            $title='Registrarse';
        } elseif($actionName=='lostpassword'){
            $title='Recuperar cuenta';
        } elseif($actionName=='resetpassword'){
            $title='Restablecer contraseña';
        } else{ 
            $title='Acceder';
        }

        $this->metasController->setMetaTags([
            'title' => $title,
        ]);

        $this->metasController->setCssLinks([
            'public/css/app/auth/auth.css',
            'public/vendor/passwordUtils/passwordUtils.css',

        ]);

        $this->metasController->setJsScripts([
            'public/vendor/passwordUtils/passwordUtils.js',
            'public/js/app/auth/Auth'.$authJS.'.js',  
        ]);

    }

/*Funciones para Auth*/

    // Función para el registrar de usuarios en la app
    public function registerAcount() {


        if (empty($_POST['register_email']) || empty($_POST['register_password'])) {
            $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
        }

    // Validación del token CSRF 
        $valCSRF=val_CSRF();
        if ($valCSRF=='okToken') {
            $response=$this->authModel->registerAcount(); 
        } else {
            $response = array('success' => false, 'message' => $valCSRF);
        }

        if (isset($response['status']) && $response['status']==='success') {

            $params = [
                'address' => $_POST['register_email'],
                'title' => 'Verifique su cuenta',
                'aText' => 'Verificar mi cuenta',
                'aHref' => rtrim(site_url, '/') . '/login?v=' . $response['token'],
                'name' => ucfirst(current(explode('@', $_POST['register_email']))),
                'h1' => ucfirst(current(explode('@', $_POST['register_email']))) . ', ¡Gracias por registrarte!',
                'p1' => 'Haga clic en el siguiente botón para verificar su cuenta:',
                'p2' => '', // Si necesitas un segundo párrafo, lo puedes añadir aquí
                'logo' => rtrim(site_url, '/') . "/public/img/logo.png",
                'htmlContent' => 'app/views/templates/mailTemplate.html'
            ];

            $async = new Async();
            $async->create(function () use ($params) {
                $mailed=$this->emailController->sendAsyncMail($params);
                $logFile = 'mailed.txt';
    
                // Formatear los datos
                $logData = [
                    'hour' => date('H:i:s'),
                    'datos' => $mailed,
                    //'cabecera' => getallheaders(),
                ];
    
        // Escribir los datos en el archivo de log
                //file_put_contents($logFile, print_r($logData, true), FILE_APPEND);
                 });

            //$this->emailController->sendMail($register_email,$title,$body);
        }

        $response['token'] = 'Ja te lo creiste';//anulo el token en la respuesta json
        return $response;
    }


    // Función para el validar cuentas
    public function validateAcount() {

    // Validación del token CSRF 
        $valCSRF=val_CSRF();
        if ($valCSRF=='okToken') {
            $response=$this->authModel->registerAcount(); 
        } else {
            $response = array('success' => false, 'message' => $valCSRF);
        }

        if (empty($_POST['vtoken'])) {
         $response = array('success' => false, 'message' => 'invalidToken');
        } else { $response=$this->authModel->validateAcount();}

        return $response;  
    }

    // Función para el pedir recuperar la cuenta
    public function recoveryAcount() {  

        if (empty($_POST['recovery_email']) ) {
            $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
        }

    // Validación del token CSRF 
        $valCSRF=val_CSRF();
        if ($valCSRF=='okToken') {
            $response=$this->authModel->recoveryAcount(); 
        } else {
            $response = array('success' => false, 'message' => $valCSRF);
        }

        if (isset($response['status']) && $response['status']==='success') {
            $params = [
                'address' => $_POST['recovery_email'],
                'title' => 'Recupere su cuenta',
                'aText' => 'Recuperar mi cuenta',
                'aHref' => rtrim(site_url, '/') . '/login/resetpassword?rp='. $response['token'],
                'name' => ucfirst(current(explode('@', $_POST['recovery_email']))),
                'h1' => ucfirst(current(explode('@', $_POST['recovery_email']))) . ', Recupera tu cuenta',
                'p1' => 'Hemos recibido una petición para recuperar su cuenta, si ha sido un error puede ignorar este correo electrónico y no pasará nada.<br><br>Haga clic en el siguiente botón para recuperar su cuenta:',
                'p2' => 'Este enlace caducará en 24 horas.', // Si necesitas un segundo párrafo, lo puedes añadir aquí
                'logo' => rtrim(site_url, '/') . "/public/img/logo.png",
                'htmlContent' => 'app/views/templates/mailTemplate.html'
            ];

            $async = new Async();
            $async->create(function () use ($params) {
                $this->emailController->sendAsyncMail($params);
            });

        }

        $response['token'] = 'Ja te lo creiste';
        return $response;  
    }


// Función para el resetear el passw
public function resetPassword() {

    if (empty($_POST['rpuser_token']))  {
        $response = array('success' => false, 'message' => 'emptyField');
        return $response; 
        exit;
    }

 // Validación del token CSRF 
    $valCSRF=val_CSRF();
    if ($valCSRF=='okToken') {
        $response=$this->authModel->resetPassword(); 
    } else { 
        $response = array('success' => false, 'message' => $valCSRF);
    }

    return $response;  
}

// Función para hacer login
public function login() {


    if (empty($_POST['login_email']) || empty($_POST['login_password'])) {
        $response = array('success' => false, 'message' => 'emptyField');
    }

// Validación del token CSRF 
    $valCSRF=val_CSRF();
    if ($valCSRF=='okToken') {
        $response=$this->authModel->login(); 
    } else { 
        $response = array('success' => false, 'message' => $valCSRF);
    }




    return $response; 

}


// Función para hacer Logout
public function logout() {

    if (isset($_SESSION['userID'])) {
        $response = array('status' => 'success', 'message' => 'logout');
        session_destroy();
    } else {
        $response = array('status' => 'success', 'message' => 'logout');
    }

    return $response;  

}


// Función para hacer Logout por inactividad
public function checkSesion() {
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    

// Comprobar si la última actividad del usuario se ha actualizado recientemente
if (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity']) >1800) {  // La sesión ha expirado, devolver una respuesta que desencadene el cierre de sesión en el lado del cliente
  $response = 'expired' ;
  session_destroy();
} elseif ((!isset($_SESSION['lastActivity']) || !isset($_SESSION['userID']))&& strpos($url, "login") !== false) {
    var_dump($this->$thisparams);
  $response = 'expired' ;
  session_destroy();
// Establece el tiempo de expiración de la cookie de sesión en el pasado
//setcookie(session_name(), '', time() - 1800);
} else {
    $response ='isLogin';
}

return $response;  
}


    // Función para el reenvío del correo de verificación
    public function verifyAcount() {

        if (empty($_POST['login_email'])) {
            $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
        }
    // Validación del token CSRF 
        $valCSRF=val_CSRF();
        if ($valCSRF=='okToken') {
            $response=$this->authModel->verifyAcount(); 
        } else {
            $response = array('success' => false, 'message' => $valCSRF);
        }

    // Si todo ok entonces mandamos el correo
        if (isset($response['status']) && $response['status']==='success') {

            $params = [
                'address' => $_POST['login_email'],
                'title' => 'Verifique su cuenta',
                'aText' => 'Verificar mi cuenta',
                'aHref' => rtrim(site_url, '/') . '/login?v=' . $response['token'],
                'name' => ucfirst(current(explode('@', $_POST['login_email']))),
                'h1' => ucfirst(current(explode('@', $_POST['login_email']))) . ', ¡Gracias por registrarte!',
                'p1' => 'Haga clic en el siguiente botón para verificar su cuenta:',
                'p2' => '', 
                'logo' => rtrim(site_url, '/') . "/public/img/logo.png",
                'htmlContent' => 'app/views/templates/mailTemplate.html'
            ];

            $async = new Async();
            $async->create(function () use ($params) {
                $this->emailController->sendAsyncMail($params);
            });
        }
        $response['token'] = 'Ja te lo creiste';
        return $response;  
    }

/*fin de la clase*/
}

