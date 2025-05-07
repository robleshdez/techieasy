<?php
// Intelligence Boots controller

require_once realpath(__DIR__.'/../../../core/Load.php');
require_once realpath( ABSPATH . "app/controllers/wapi/WapiController.php");
require_once realpath( ABSPATH . "public/vendor/textClassifier/TextClassifier.php");
require_once realpath( ABSPATH . "app/models/intelligence/IntelligenceModel.php");

class IntelligenceController { 

    private $wapiController;
    private $textClassifier;
    private $intelligenceModel;
    public static $instances= [];
    private $key;
 

    //public function __construct($botId, $receiver,$manager) {
        private  function __construct($key) {
        $this->key = $key;
        $this->wapiController = new WapiController();
        $this->textClassifier = new TextClassifier();
        $this->intelligenceModel = new IntelligenceModel();
        //$this->logg("constructor");
         
    }

   
    public static function getInstance($key) {
        /*if (!empty(self::$isLocked[$key]) && self::$isLocked[$key] === true) {
            $this->logg("está bloqueada.");
            throw new Exception("La instancia con clave '{$key}' está bloqueada.");
        }*/

        // Si no existe una instancia para la clave, crea una nueva
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($key);
         }
        //self::logg(self::$isLocked);
        return self::$instances[$key];
        
    }
  
   

/*funcionalidades*/
/*$conversation = [
            'messageTimestamp' => $messageTimestamp,
            'instance' => $instance,
            'remoteJid' => $remoteJid,
            'addressee' => $addressee,
            'pushName' => $pushName,
            'msgType' => $msgType,
            'message' => $message,
            'isGroup' => $isGroup,
            'senderType' => 'WhatsApp'
        ];*/


    public function classifyMsg($conversation) {
        
        try {
        $instance=$conversation['instance'];
        $messageTimestamp = $conversation['messageTimestamp'];
        $nowTimestamp = (int)time();
        $timeDiff = $nowTimestamp-(int)$messageTimestamp;
        if ($timeDiff/60>10) { //si el mensaje recibido fue hace mas de 10 min no resp
             return;
        }

        $botId=$this->intelligenceModel->getBotIdByMobileNumber($instance);
        if ($botId==0) { return; }

                 
        $flows = $this->intelligenceModel->getFlowsByBotId($botId);   
        if ($flows==0) { return; }

        $intents =$this->getIntents($flows);
        $text = $conversation['message'];
        $flowId = $this->textClassifier->intentsClassify($text, $intents);
        $messageSequence = $this->intelligenceModel->getMessageSequence($flowId);
        if (!isset($messageSequence) && $messageSequence==0) {return; }


        $this->sendMessages($conversation, $messageSequence);
        
        }
        catch (Exception $e) {
            // Manejo de errores: loguear o realizar acciones de recuperación
            $this->ilogg("Error: " . $e->getMessage());
         } finally {
              //self::releaseInstance($this->key);
             //self::unlockInstance($key);

        }
    } 


 private function sendMessages($conversation, $messageSequence) {
            $instance=$conversation['instance'];
            $addressee=$conversation['addressee'];
            $values=[
                    'name'=> $conversation['pushName'] 
                    //puedes añadir otros valores 
                ];
                
                $name=$this->intelligenceModel->getContactName($instance,$addressee);
                
                if ($name!='noName' && $name!='noBot') {
                   $values['name']=$name;
                }
                 //$this->ilogg($name);

          foreach ($messageSequence as $message) {        
            switch ($message['type']) {
                case 'text':

                $conversation['message']= $this->replacePlaceholders($message['content'], $values);
                $this->wapiController->sendTextSMS($conversation);
                break;

                default:
                // Si no coincide con ningún tipo, puedes manejar el caso por defecto
            } 
        }
    }
 

public function replacePlaceholders($text, $values) {
    // Iteramos sobre los valores clave-valor
    foreach ($values as $key => $value) {
        // Reemplazamos cada {{key}} con su valor correspondiente
        $text = str_replace("{{{$key}}}", $value, $text);
    }
    return $text;
}

public function exeComand($conversation){
$instance=$conversation['instance'];
$messageTimestamp=$conversation['messageTimestamp'];
$comand=$this->wapiController->getComand($instance,$messageTimestamp);
    //$this->ilogg($conversation);      

 if ($comand[0] === '/') {
 switch ($comand) {
    case '/start':
      $response = $this->intelligenceModel->updateBotStatus($instance,1);
        break;

    case '/stop':
      $response = $this->intelligenceModel->updateBotStatus($instance);
        break; 

    case '/status':
      $response = $this->intelligenceModel->getBotStatus($instance);
        break;

    default:
        $response['status']='success';
        $response['message']="Comando no reconocido
Intente con:
*/status* para ver el estado del Bot
*/start* para activar el Bot
*/stop* para apagar el Bot";      
    break;
    }
      
    if ($response['status']=='success') {

        $conversation['addressee']=$instance; 
        $conversation['message']='*Bot:* '.$response['message'];
    $this->wapiController->sendTextSMS($conversation);;
    }
    
        }

     
}

 
public function updateCnt($conversation,$type){
$res=$this->intelligenceModel->updateCnt($conversation, $type);
//$this->ilogg($res);
     
}



 public function ecoBot($conversation) {
 $instance=$conversation['instance'];
 $conversation['message']= json_encode($conversation);
 $this->wapiController->sendTextSMS($instance,$conversation);;
 }

public function getIntents($flows) {
    $intents = [];

    // Recorremos cada flow y separamos los valores
    foreach ($flows as $flow) {
        // Convertir trigger_words de string a array
        $triggerWordsArray = json_decode($flow['trigger_words']);

        // Asignar el flow_id como clave y las trigger_words como valor
        $intents[$flow['flow_id']] = $triggerWordsArray;
    }

    return $intents;
}

 
  private  function ilogg($data) {
        // Definir el archivo de log
        $logFile = 'ilogg.txt';
        
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


/* fin de la clase*/

}

 