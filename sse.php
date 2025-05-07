<?php
require_once 'core/Load.php';
require_once realpath(ABSPATH . "public/vendor/php-sse/Event.php");
require_once realpath(ABSPATH . "public/vendor/php-sse/SSE.php");
require_once realpath(ABSPATH . "public/vendor/php-sse/StopSSEException.php");


// PHP-FPM SSE Example: push messages to client

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Nginx: unbuffered responses suitable for Comet and HTTP streaming applications

$callback = function() {
    $instance = $_GET['sseInstance'];  
    $key = 'sse_qr_' . $instance;
    //apcu_store($key, 'noQR');
    $news = apcu_fetch($key);
    apcu_delete($key);
    if (empty($news)) {
        //return false; // Return false if no new messages
    }
     /*if ($news === 'noQR' || (!empty($news) && $news !== null)) {
        // Si el dato es 'noQR' o válido para enviar
        //apcu_store($key, null); // Limpiar la clave después de procesarla
        return json_encode(compact('news')); // Enviar el dato al cliente
    } */
    return json_encode(compact('news'));
    //$filePath = ABSPATH . '/tmp/sse_qr_' . $instance; 

     /*if (!file_exists($filePath)) {
        usleep(100000); // Esperar 100ms si no hay datos
        return false;
     } */
     

    //$news = [['id' => $id, 'title' => 'title ' . $id, 'content' => 'content ' . $id]]; // Get news from database or service.
    //$news=$message;
    //$news =  json_decode(file_get_contents($filePath), true); // Leer el archivo
    //unlink($filePath); // Eliminar el archivo después de procesarlo (limpieza)
    
    $shouldStop = false; // Stop if something happens or to clear connection, browser will retry
    if ($shouldStop) {
        throw new StopSSEException();
    }
     //return ['event' => 'ping', 'data' => 'ping data']; // Custom event temporarily: send ping event
    //return ['id' => uniqid(), 'data' => json_encode(compact('news'))]; // Custom event Id

};



(new SSE(new Event($callback, 'news')))->start(5);
