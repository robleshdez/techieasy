<?php
// Intelligence Boots model


class IntelligenceModel {  


public function getBotIdByMobileNumber($instance) {
      
   try {
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare("
      SELECT bot_id
      FROM bots 
      WHERE mobile_number = :instance  AND is_active = :is_active AND is_blocked = :is_blocked
     ");
     $stmt->bindValue(':instance', $instance, PDO::PARAM_STR); // Aquí se usa :instance
     $stmt->bindValue(':is_active', 1, PDO::PARAM_INT);
     $stmt->bindValue(':is_blocked', 0, PDO::PARAM_INT);    

     $stmt->execute();
     $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      

       if ($rows) {
        $response = $rows[0]['bot_id'];
        return $response;
      } else {
        $response = 0;
      }
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }finally {
      $pdo = null;
    }
    return $response; 

  }


 public function getFlowsByBotId($botId) {
    try {
        $pdo = getPDOInstance();

        $stmt = $pdo->prepare("
            SELECT flow_id, trigger_words
            FROM flows
            WHERE bot_id = :bot_id AND type = 1
        ");

        // Vinculación del parámetro bot_id
        $stmt->bindValue(':bot_id', $botId, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los flows
        $flows = $stmt->fetchAll(PDO::FETCH_ASSOC);

      
       if ($flows) {
        $response = $flows;
        return $response;
      } else {
        $response = 0;
      }
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }finally {
      $pdo = null;
    }
    return $response; 
}
 
 public function getMessageSequence($flowId) {

    try {
        $pdo = getPDOInstance();

        $stmt = $pdo->prepare("
            SELECT message_sequence
            FROM flows
            WHERE flow_id = :flow_id
        ");

        // Vinculación del parámetro bot_id
        $stmt->bindValue(':flow_id', $flowId, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los flows
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

      
       if ($row) {
         $jsonDatas = $row[0]['message_sequence'];
         $arrayDatas = json_decode($jsonDatas, true);
         $response =  $arrayDatas ; 
      
        return $response;
      } else {
        $response = 0;
      }
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }finally {
      $pdo = null;
    }
    return $response; 
}




public  function updateBotStatus($instance,$status=0){

  try {
    $pdo = getPDOInstance();
 
    $stmt = $pdo->prepare("UPDATE bots SET 
      is_active = :isActive         
      WHERE mobile_number = :mobile_number");

    $stmt->bindParam(':mobile_number', $instance);
    $stmt->bindParam(':isActive', $status);
    $stmt->execute();
    $res=$status==1?'Bot activo':'Bot apagado';
    $response = array('status' => 'success', 'message' => $res);

  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;
}
public  function getBotStatus($instance){

  try {
    $pdo = getPDOInstance();
     $stmt = $pdo->prepare("SELECT is_active, is_blocked FROM bots WHERE mobile_number = :mobile_number");

    $stmt->bindParam(':mobile_number', $instance);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result =$result[0];

    if ($result['is_blocked']) {
      $status='Este Bot está bloqueado por el sistema. Si cree que es un error contacte con el administador.';
    }
    else{
       $status=$result['is_active']?'El Bot está activo':'El Bot está apagado';
    }
     $response = array('status' => 'success', 'message' => $status);

  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;
}


public function updateCnt($conversation, $type='update') {
  
    $instance = $conversation['instance'];
    $contacts = array_unique($conversation['contacts'], SORT_REGULAR);

    try {
        $pdo = getPDOInstance();

        // Buscar el bot_id asociado al número de instancia
        $stmt = $pdo->prepare("SELECT bot_id FROM bots WHERE mobile_number = :instance");
        $stmt->bindValue(':instance', $instance);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $bot_id = $row['bot_id'] ?? null;
        if (!$bot_id) { return "nobotid";}

        // Buscar el grupo por bot_id y tipo de evento 
        $gname= $type==='upsert'? 'Personal':'Nuevos';
        $stmt = $pdo->prepare("SELECT cg_id FROM contacts_group WHERE bot_id = :bot_id and name = :gname");
        $stmt->bindValue(':bot_id', $bot_id);
        $stmt->bindValue(':gname', $gname);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cg_id = $row['cg_id'] ?? null;
        if (!$cg_id) {return "nocigd";}

        $batchSize = 10; // Tamaño del lote
        $batches = array_chunk($contacts, $batchSize);
        $allInsertedIds = [];

foreach ($batches as $batch) {
    // Construir la consulta de insertar contacto por cada lote
    $query = "INSERT IGNORE INTO contacts (bot_id, name, mobile_number) VALUES ";
    $values = [];
    $params = [];

    foreach ($batch as $index => $contact) {
        $values[] = "(:bot_id_$index, :name_$index, :mobile_number_$index)";
        $params["bot_id_$index"] = $bot_id;
        $params["name_$index"] = $contact['name'];
        $params["mobile_number_$index"] = $contact['mobile_number'];
    }

    // Finalizar la consulta para el lote
    $query .= implode(", ", $values);
    $stmt = $pdo->prepare($query);

    // Enlazar parámetros
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    // Ejecutar la consulta para el lote
    $stmt->execute();
    $lastInsertId = $pdo->lastInsertId();

    // Almacenar los IDs generados para este lote
    $insertedCount = $stmt->rowCount(); // Número de registros insertados

    // Si el número de registros insertados es mayor a 0, agregamos los IDs
    if ($insertedCount > 0) {
        for ($i = 0; $i < $insertedCount; $i++) {
            $allInsertedIds[] = $lastInsertId + $i;
        }
    }
}

//////////

     //return json_encode($allInsertedIds);
  
// Tamaño del paquete
$batchSize = 10;

// Dividir el array de IDs en paquetes de tamaño definido
$batches = array_chunk($allInsertedIds, $batchSize);

// Preparar las consultas por adelantado
$queryCheck = "
    SELECT 1
    FROM contacts_groups_mapping
    WHERE contact_id = :contact_id
    LIMIT 1";
$stmtCheck = $pdo->prepare($queryCheck);

$queryInsert = "
    INSERT INTO contacts_groups_mapping (cg_id, contact_id)
    VALUES (:cg_id, :contact_id)";
$stmtInsert = $pdo->prepare($queryInsert);

// Procesar cada paquete
foreach ($batches as $batch) {
    foreach ($batch as $contactId) {
        // Verificar si el contact_id ya existe
        $stmtCheck->bindValue(":contact_id", $contactId);
        $stmtCheck->execute();

        // Si ya existe, saltar a la siguiente iteración
        if ($stmtCheck->fetch()) {
            continue;
        }

        // Si no existe, realizar la inserción
        $stmtInsert->bindValue(":cg_id", $cg_id);  // Asumiendo que $cg_id ya está definido
        $stmtInsert->bindValue(":contact_id", $contactId);
        $stmtInsert->execute();
    }
}



        $response = array('status' => 'success', 'message' => 'Contactos sincronizados correctamente.');
    } catch (PDOException $e) {
        // Capturar errores y devolver un mensaje informativo
        $response = array(
            'status' => 'error',
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        );
    } finally {
        $pdo = null;
    }

    return $response;
}


public  function getContactName($instance,$addressee){

  try {
    $pdo = getPDOInstance();
    // Buscar el bot_id asociado al número de instancia
        $stmt = $pdo->prepare("
            SELECT bot_id
            FROM bots
            WHERE mobile_number = :instance
        ");
        $stmt->bindValue(':instance', $instance);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $bot_id = $row[0]['bot_id'] ?? null;
        if (!$bot_id) {
            return 'noBot';
        }
       
        $stmt = $pdo->prepare("SELECT name FROM contacts WHERE mobile_number = :addressee AND bot_id = :bot_id");
        $stmt->bindValue(':addressee', $addressee);
        $stmt->bindValue(':bot_id', $bot_id);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $name = $row[0]['name'] ?? null;
         
        if ($name) {

          $response= $this->fixingName($name);
        }
        else {
          $response= 'noName';
        }

  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;
       
       
}


 private function fixingName($name) {
    $partes = explode(" ", $name);
    
    // Si el segundo nombre es corto (puedes definir un límite de caracteres)
    if (isset($partes[1]) && strlen($partes[1]) <= 4) {
        // Unimos el primer, segundo y tercer nombre si existen
        $fixName = isset($partes[2]) ? $partes[0] . ' ' . $partes[1] . ' ' . $partes[2] : $partes[0] . ' ' . $partes[1];
    } else {
        // Si el segundo nombre no es corto, solo dejamos el primer y segundo
        $fixName = $partes[0] . ' ' . $partes[1];
    }
    
    return $fixName;
}

 

/*funcionalidades*/




  

/* fin de la clase*/

}

 