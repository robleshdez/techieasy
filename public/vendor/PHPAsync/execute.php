<?php
/*Este Archivo ejecuta las tareas en segundo plano que reciba del AsyncController*/

require_once realpath(__DIR__.'/../../../core/Load.php');

/*ClosureWrapper es una clase auxiliar para emvolver la closure con su contexto*/
require_once realpath(ABSPATH . "public/vendor/PHPAsync/ClosureWrapper.php");

/*Aqui cargar las clases y controladores que sean necesarios para ejecutar las funciones async sin que pierdan el contexto ej:
la funcion de enviar correo al registrarte se origina en el AuthController*/

require_once realpath( ABSPATH . "app/controllers/auth/AuthController.php"); 


if (php_sapi_name() !== 'cli') {
    // Si no es la interfaz de línea de comandos, negar acceso
    header('HTTP/1.0 403 Forbidden');
    die('Acceso denegado: este script solo puede ejecutarse desde la línea de comandos.');
}

// Obtiene el argumento (la función serializada) de la línea de comandos
if (isset($argv[1])) {

    $serializedWrapper = base64_decode($argv[1]);
    $wrapper  = unserialize($serializedWrapper);
    $closure = ClosureWrapper::unserialize($wrapper);
    //file_put_contents('debug_async.txt', print_r($wrapper, true));

    // Verifica si es una Closure antes de ejecutarla
    if ($closure instanceof  Closure ) {
        call_user_func($closure);
        //file_put_contents('debug_async.txt', print_r($closure, true));
    } else {
        //file_put_contents('debug_async.txt', 'No es una Closure válida.');
    }
} else {
    //file_put_contents('debug_async.txt', 'No se pasó ningún argumento.');
}


 