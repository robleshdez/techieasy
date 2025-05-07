<?php
// app/controllers/WapiController.php
// Controlador para la conexión con la api de WhatsApp

//require_once realpath( __DIR__.'/../../core/Load.php');

class WapiController {  

 
   public function linkUpWhatsApp(){

    $phoneNumber=$_POST['fullPhoneNumber'];
    $response=$this->findSession($phoneNumber);
   
        if ($response==true) {
             $response=$this->sessionStatus($phoneNumber);
             if ($response!='authenticated') {
                $response=$this->unLinkWhatsApp($phoneNumber);
                $response=$this->createSession($phoneNumber);
            }
             else {
                 $response =array(
               'status' => 'success', 
               'message' => 'recovered',
                );
             }
        }
        elseif($response==false) {
             $response= $this->createSession($phoneNumber);
        }
//print_r($response);     
return $response;
}

private function findSession($phoneNumber){

    try {
        // Verificar si la sesión existe
        $findSessionUrl = "sessions/find/".$phoneNumber;
        $findSessionResponse = $this->makeApiRequest($findSessionUrl, 'GET', null);
        if ($findSessionResponse['httpCode']===200) {
            return true;
        } 
           return false ;
            
        }
     catch (Exception $e) {
          return array('status' => 'error', 'message' => $e->getCode());

    }

}


private function sessionStatus($phoneNumber){

    try {
        // Verificar si la sesión existe
        $sessionStatusUrl = "sessions/status/".$phoneNumber;
        $sessionStatuResponse = $this->makeApiRequest($sessionStatusUrl, 'GET', null);
    
        if ($sessionStatuResponse['httpCode']===200) {
           return $sessionStatuResponse['response']->data->status;
        } 
           return false;  
        }
     catch (Exception $e) {
          array('status' => 'error', 'message' => $e->getCode());

    }

}

private function createSession($phoneNumber){

    try {
            $addSessionUrl = "sessions/add";
            $payload = [
                'id' => $phoneNumber,
                'isLegacy' => 'false',
                'typeAuth' => 'qr',//code or qr
                //'phoneNumber' => $phoneNumber,
            ];
            $addSessionResponse = $this->makeApiRequest($addSessionUrl, 'POST', json_encode($payload));

            if ($addSessionResponse['httpCode'] === 200) {
                $qrCode = $addSessionResponse['response']->data->qr;//qr or code
                $response = array(
               'status' => 'success', 
               'message' => 'created',
               'qrCode' => $qrCode,
            );
            } else {
                $response = array('status' => 'error', 'message' => 'uncreated');
            }                          
        }
     catch (Exception $e) {
          $response = array('status' => 'error', 'message' => $e->getCode());
    }
    return $response ;
}
   
   public function unLinkWhatsApp($phoneNumber=null){
        if (isset($_POST['fullPhoneNumber'])&& $_POST['fullPhoneNumber']!=null) {
           $fullPhoneNumber = $_POST['fullPhoneNumber'];
        }else if ($phoneNumber!=null) {
            $fullPhoneNumber =$phoneNumber;
        }else  {
           $fullPhoneNumber ="";
        }
     try {
        // Verificar si la sesión existe
        $checkSessionUrl = "sessions/delete/".$fullPhoneNumber;
        
        $checkSessionResponse = $this->makeApiRequest($checkSessionUrl, 'DELETE', null);

        if ($checkSessionResponse['httpCode'] === 200) {
         $response = array(
               'status' => 'success', 
               'message' => 'deleted',
            );
            
        } else {
            //echo "Sesión no encontrada  
                $response = array('status' => 'error', 'message' => 'undeleted',
            );
            

            
        }
    } catch (Exception $e) {
          $response = array('status' => 'error', 'message' => $e->getCode());

    }

    return $response;
}

public function sendTextSMS($conversation){
$instance=$conversation['instance'];
$receiver = $conversation['addressee']; // Obtener el  receptor

  $this->sendPresenceUpdate('composing',$instance,$receiver);

$sendSMSurl = "chats/send?id=".$instance;
$messageText = $conversation['message'];
$name=isset($conversation['pushName'])?$conversation['pushName']:'';
$payload = [
            'receiver' => $receiver,
            'message' => ["text"  => $messageText]
            ];

  $this->makeApiRequest($sendSMSurl, 'POST', json_encode($payload));
 //$filePath = ABSPATH . '/tmp/msg_' . $instance; // Ruta del archivo temporal;
       //file_put_contents($filePath, json_encode($payload)); // Guardar los datos 
 
  }


 public function sendPresenceUpdate($presence,$instance,$receiver){
    //$presence= unavailable available composing recording paused
    $sendUrl = "chats/send-presence?id=".$instance;
    $payload = [
        'receiver' => $receiver,
        'isGroup' => false,
        'presence' => $presence
            ];
   $this->makeApiRequest($sendUrl, 'POST', json_encode($payload));          
   
} 



public function getComand ($instance,$messageTimestamp){

$sendSMSurl = "chats/".$instance."@s.whatsapp.net?id=".$instance."&limit=1&cursor_id=REDACTED&cursor_fromMe=false";
   $res= $this->makeApiRequest($sendSMSurl, 'GET', null);
    if ($res['httpCode']!=200 && $res["response"]->success!=true) {
       $res='noComand';
    }
    $resMessageTimestamp = $res["response"]->data[0]->messageTimestamp;

    if ($resMessageTimestamp!=$messageTimestamp) {
        $res='noComand';
    }
    $res = isset($res["response"]->data[0]->message->conversation)?$res["response"]->data[0]->message->conversation:$res["response"]->data[0]->message->extendedTextMessage->text;
  return $res;
  }


  /*
public function getContactName ($conversation){
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
/*$instance=$conversation['instance'];
$receiver=$conversation['addressee'];
$sendSMSurl = "chats/?id=".$instance."&limit=1&cursor_id=REDACTED&cursor_fromMe=false";
   $res= $this->makeApiRequest($sendSMSurl, 'GET', null);
    if ($res['httpCode']!=200/* && $res["response"]->success!=true*//*) {
       return 'noChat';
       
    }
 
    if (!isset($res['response']->data[1]->name)) {
         return 'noName';
    }

     $name= explode(" ", $res['response']->data[1]->name)[0];
     
  return $name;

  }

*/




public function makeApiRequest($url, $method, $body = null)
{
    $curl = curl_init();

    $headers = [
        'Content-Type: application/json',
        'apikey: ' . WAPIKEY
    ];

    curl_setopt_array($curl, [
        CURLOPT_URL => WAPI_URL.$url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    if ($body) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    }
    //$filePath = ABSPATH . '/tmp/curl'; // Ruta del archivo temporal;

       //file_put_contents($filePath, json_encode($body)); // Guardar los datos 

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        throw new Exception('cURL error: ' . curl_error($curl));
    }

    curl_close($curl);
    return [
        'httpCode' => $httpCode,
        'response' => json_decode($response)
    ];
   }



/*fin de la clase*/
}
