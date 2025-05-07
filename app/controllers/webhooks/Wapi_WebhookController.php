<?php
// app/controllers/webhooks/Wapi_WebhookController.php
// Controlador webhooks de la API de WhatsApp

require_once realpath(__DIR__.'/../../../core/Load.php');
require_once realpath( ABSPATH . "app/controllers/intelligence/IntelligenceController.php");
 $cacheInfo = apcu_cache_info();
 // Verificar si hay entradas en la caché
 if (!empty($cacheInfo['cache_list'])) {
    foreach ($cacheInfo['cache_list'] as $entry) {
        echo "Clave: " . $entry['info'] . PHP_EOL;
        echo "Valor: " .apcu_fetch($entry['info']). PHP_EOL;
         //echo "Tamaño: " . $entry['mem_size'] . " bytes" . PHP_EOL;
        //echo "Tiempo de creación: " . date('Y-m-d H:i:s', $entry['creation_time']) . PHP_EOL;
         echo '<br>' . PHP_EOL;
        echo str_repeat('-', 20) . PHP_EOL;
         echo '<br>' . PHP_EOL;
    }
} else {
    echo "No hay datos almacenados en APCu." . PHP_EOL;
}
 
class Wapi_WebhookController {
    
    private $intelligenceController;
 
     // Constructor
    public function __construct() {
    }

    // Método para manejar la solicitud de webhook
    public function handleRequest() {

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: Content-Type");
            
        // Verificar que el método sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendErrorResponse(405, "Método no permitido ");
        }

        // Verificar que la solicitud provenga del servidor API (por la URL)
        if (!$this->isValidOrigin()) {
            $this->sendErrorResponse(403, "Acceso no permitido");
        }

        // Leer la entrada JSON de la solicitud
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
  
        // Verificar que los datos sean válidos
        if ($data === null) {
            $this->sendErrorResponse(400, "Datos inválidos");
        }

        // Procesar el evento recibido
        $this->processWebhookData($data);
    }

    // Verificar si la solicitud proviene del origen permitido
    private function isValidOrigin() {
        $headers = getallheaders();
        $receivedKey = $headers['X-Webhook-Wapi'] ?? '';
        //return hash_equals(WAPIKEY, $receivedKey);
        return true;
    }


    // Procesar los datos del webhook
    private function processWebhookData($data) {
        //$this->logRequest($data);///log de lo recivido


        
        //el instance es el #de telefono del host
        $instance = isset($data['instance'])?$data['instance']:"no instance";
        $type = $data['type'];//="CONNECTION_UPDATE"
        //$data['data']['connection']="close";
        //$instance="5353779424";
          

        if ($type=="CONNECTION_UPDATE" /*|| $type=="QRCODE_UPDATED"*/) {
            $qr = isset($data['data']['qr']) ? $data['data']['qr'] : null;
             ;
            if (isset($qr)) {
                 //$this->handleQrCodeUpdated($instance, $qr); //este es raro 
                 //$this->logg('normal para '.$instance);
            }
            elseif ($data['data']['connection']=="close") { //este devielve imag64
                //$this->logg('repite para '.$instance);
                 $this->newQrCodeRequest($instance); 
                
            }
            elseif ($data['data']['isNewLogin']==true) {
                 $key = 'sse_qr_' . $instance; // Clave única para el dato en APCu
                 apcu_store($key, 'isNewLogin',120);
                //$this->logg('nuevo inicio '.$instance);
            }
        }

        if ($type=="CONTACTS_SET" || $type=="CONTACTS_UPDATE") {
            $conversation=$this->handleContacts($data);
            //$this->logg($conversation);
            $botId=$conversation['instance'];
            $receiver='_cnt';
            $key = self::getKey($botId, $receiver);
            try {
                $this->intelligenceController = IntelligenceController::getInstance($key);
                $this->intelligenceController->updateCnt($conversation,'update');
              } catch (Exception $e) {
                 $this->logg("Error: " . $e->getMessage());
             } finally {
            }
        }

        if ($type=="noCONTACTS_UPSERT") {
            $conversation=$this->handleContacts($data);
            //$this->logg($conversation);
            $botId=$conversation['instance'];
            $receiver='_cnt';
            $key = self::getKey($botId, $receiver);
            try {
                $this->intelligenceController = IntelligenceController::getInstance($key);
                $this->intelligenceController->updateCnt($conversation,'upsert');
              } catch (Exception $e) {
                 $this->logg("Error: " . $e->getMessage());
             } finally {
            }
        }

            
        if ($type=="MESSAGES_UPSERT") {
            $conversation=$this->handleMessages($data);
            $botId=$conversation['instance'];
            $receiver=$conversation['addressee'];

            $key = self::getKey($botId, $receiver);
            try {
                if ($this->isLocked($key)) {return;}
                $this->setLock($key);
                $this->intelligenceController = IntelligenceController::getInstance($key);
                
                $this->intelligenceController->classifyMsg($conversation);
                
             } catch (Exception $e) {
                 $this->logg("Error: " . $e->getMessage());
             } finally {
                $this->unsetLock($key);
            }   
        }  
     
        if ($type=="CHATS_UPDATE") {
            $conversation=$this->handleUpdateChat($data);
            //$this->logg($conversation);
            $botId=$conversation['instance'];
            $receiver=$conversation['addressee'];
            if ($botId!==$receiver) {
                return;
            }

            $key = self::getKey($botId, $receiver);
            try {
                if ($this->isLocked($key)) {return;}
                $this->setLock($key);
                $this->intelligenceController = IntelligenceController::getInstance($key);
                $this->intelligenceController->exeComand($conversation);
              } catch (Exception $e) {
                 $this->logg("Error: " . $e->getMessage());
             } finally {
                $this->unsetLock($key);
            }
        }

            




    }
    

   
    public function handleQrCodeUpdated($instance, $qr) {
        $key = 'sse_qr_' . $instance; // Clave única para el dato en APCu
        apcu_store($key, $qr,120); // Almacena los datos en APCu
        if ( apcu_fetch($key)==null) {
               apcu_delete($key); 
            }
        //$this->logg($qr);
    }

    public function newQrCodeRequest($instance) {
        $key = 'sse_qr_' . $instance; // Clave única para el dato en APCu
        $retryKey='nOfRetry_' . $instance;

        //$this->logg("fue close y mandamos a llamar a new qr para ".$instance);
        //$this->logg("apcu_exists? " . (apcu_exists($retryKey) ? "si" : "no"));

        $retryValue = apcu_fetch($retryKey); 
        //$this->logg("apcu_fetch? " . var_export($retryValue, true));

         if ($retryValue===false) {
            $nOfRetry=0;
            apcu_store($retryKey,0);
            //$this->logg("primera vez, iniciando ".$retryKey);
        }
        elseif ($retryValue===null) {
             //$this->logg("ya es null ".$retryKey);
               //apcu_delete($retryKey); 
            }
      
        elseif ($retryValue>4) {
            //$this->logg("ya es mas 5 veces ".$retryKey);
            return;
        }
        
        else {
            $nOfRetry=$retryValue+1;
            apcu_store($retryKey, $nOfRetry,320);
            //$this->logg("ya es".$nOfRetry." ".$retryKey);
        }
        
        $curl = curl_init();
       $headers = [
        //'Content-Type: application/json',
        'apikey: ' . WAPIKEY
    ];
       curl_setopt_array($curl, array(
        CURLOPT_URL => WAPI_URL.'sessions/add',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => 'id='.$instance.'&isLegacy=false&typeAuth=qr&phoneNumber='.$instance,//code or qr
        ));
        $data = curl_exec($curl);
        $data=json_decode($data,true);//esto cuando es code
       
        curl_close($curl);
        $instance = isset($data['instance'])?$data['instance']:"no instance";
        $type = $data['type'];//="CONNECTION_UPDATE"
        //$type=="CONNECTION_UPDATE"|| $type=="QRCODE_UPDATED"
         if ($data["success"]) { 
                //$data['data']['code'] cuando es code
             $qr = isset($data['data']['qr']) ? $data['data']['qr'] : null;
             
            if (isset($qr)) {
               apcu_store($key, $qr,120); // Almacena los datos en APCu
            }
             //$this->logg($key ."-" .$qr);
            
        }
        
    }   
    


    public function handleMessages($data) {
        $instance = isset($data['instance'])?$data['instance']:"no instance";
        $remoteJid = $data['data'][0]['key']['remoteJid'];
        $jidParts = explode('@', $remoteJid); 
        $remoteJid = $jidParts[0];
    
        $participant = isset($data['data'][0]['key']['participant'])?$data['data'][0]['key']['participant']:null;
        if ($participant!=null) {
            $participantParts = explode('@', $participant); 
            $participant = $participantParts[0];
        }
    
        $pushName = $data['data'][0]['pushName'];

        if (isset($data['data'][0]['message']['conversation'])) {
            $message = $data['data'][0]['message']['conversation'];
            $msgType="text";       
        }
        elseif (isset($data['data'][0]['message']['extendedTextMessage']['text'])) {
            $message = $data['data'][0]['message']['extendedTextMessage']['text'];
            $msgType="text"; 
        } 
        else {
            $message = null;
            $msgType=null;  
        }
            
            $isGroup =isset($data['data'][0]['message']['extendedTextMessage']['contextInfo']['mentionedJid'])? true:false; 
            $addressee = $isGroup?$participant:$remoteJid;

            $messageTimestamp= $data['data'][0]['messageTimestamp'];

            $conversation = [
            'messageTimestamp' => $messageTimestamp,
            'instance' => $instance,
            'remoteJid' => $remoteJid,
            'addressee' => $addressee,
            'pushName' => $pushName,
            'msgType' => $msgType,
            'message' => $message,
            'isGroup' => $isGroup,
            'senderType' => 'WhatsApp'
        ];
         
        return $conversation;
    }
public function handleUpdateChat($data) {
        $instance = isset($data['instance'])?$data['instance']:"no instance";
        $remoteJid = $data['data'][0]['id'];
        $jidParts = explode('@', $remoteJid); 
        $remoteJid = $jidParts[0];
        $messageTimestamp= $data['data'][0]['conversationTimestamp'];
        $conversation = [
            'messageTimestamp' => $messageTimestamp,
            'instance' => $instance,
            'addressee' => $remoteJid,
            'senderType' => 'WhatsApp'
        ];
         
        return $conversation;
    }

    public function handleContacts($data) {
        $instance = isset($data['instance'])?$data['instance']:"no instance";

        // Reconstruir el array homogenizado
        $homogenizedData = [];
        foreach ($data['data'] as $item) {

        $remoteJid = $item['id'];
        $jidParts = explode('@', $remoteJid); 
        $phoneNumber = $jidParts[0];
     
        $newItem = [
            'mobile_number' => $phoneNumber,
            'name' => $item['name'] ?? $item['notify'] ?? null // Verifica las posibles propiedades
        ];
    
    // Agregar el nuevo elemento al array homogenizado
    $homogenizedData[] = $newItem;
}  
        $conversation = [
            'messageTimestamp' => time(),
            'instance' => $instance,
            'contacts' => $homogenizedData  
        ];
         
        return $conversation;
    }



    //bloquear inteligence controller si la peticion de instancias tiene el mismo key
    public function setLock($key, $ttl = 5) {
        apcu_add($key, true, $ttl);
    }

     public function unsetLock($key) {
         apcu_delete($key);
    } 

    // Verifica si existe un bloqueo para la clave
    public function isLocked($key) {
        return apcu_exists($key);
    }


    private static function getKey($botId, $receiver) {
        $key= "lock_".$botId."_".$receiver;
        return $key;
    }
 
// Función para enviar una respuesta estándar en formato JSON
    private function sendErrorResponse($statusCode, $message) {
        // Registrar la respuesta
        $responseLog = [
            'date' => date('Y-m-d'),
            'hour' => date('H:i:s'),
            'status_code' => $statusCode,
            'message' => $message,
        ];
        //file_put_contents('response_log.txt', print_r($responseLog, true), FILE_APPEND);
        // Enviar la respuesta real
        http_response_code($statusCode);
        echo json_encode(['message' => $message]);
        exit;
    }


 // Método para registrar los datos de entrada en un archivo de log
    private function logRequest($data) {
        // Definir el archivo de log
        $logFile = 'webhook_log.txt';
    
        // Formatear los datos
        $logData = [
            'date' => date('Y-m-d'),
            'hour' => date('H:i:s'),
            'datos' => $data,
            //'cabecera' => getallheaders(),
        ];
    
        // Escribir los datos en el archivo de log
        file_put_contents($logFile, print_r($logData, true), FILE_APPEND);
    }
    // Método para registrar los datos de entrada en un archivo de log
    private function logg($data) {
        // Definir el archivo de log
        $logFile = 'logg.txt';
        // Formatear los datos
        $logData = [
            //'date' => date('Y-m-d'),
            //'hour' => date('H:i:s'),
            'datos' => $data,
            //'cabecera' => getallheaders(),
        ];
        // Escribir los datos en el archivo de log
        file_put_contents($logFile, print_r($logData, true), FILE_APPEND);
    }

   




/*Fin de la clase*/
}

// Auto Instanciarse
$wapi_WebhookController = new Wapi_WebhookController();
$wapi_WebhookController->handleRequest();
