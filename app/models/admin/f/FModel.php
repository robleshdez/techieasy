<?php
// app/models/admin/f/FModel.php
// Modelo para los Flujos


class FModel {




public  function addFlow($botID){
     try {
      $pdo = getPDOInstance();
  // veo cuantos flows tiene este bot
      $stmt = $pdo->prepare("SELECT COUNT(*) AS total_flows FROM flows WHERE bot_id  = :bot_id");
      $stmt->bindParam(':bot_id', $botID);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $total_flows = $result['total_flows'];
      // veo veo cuantos puede tener
      $stmt = $pdo->prepare("SELECT flow_limit FROM memberships WHERE bot_id = :bot_id");

      $stmt->bindParam(':bot_id', $botID);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      //print_r($result);
      $can_have = $result['flow_limit'];
      if ($total_flows >=$can_have) {
       $response = array('success' => false, 'message' => 'overPlan');
       return $response;
       exit;
     }  

     
     $flowName=randomNameGen('Flow');
     $trigger_words=json_encode(['hola']);
     $message_sequence=json_encode([
      [
        "id" => 1,
        "type" => "text",
        "content" => "Edita _este message_ para comenzar."
    ]
    ]) ;
     $stmt = $pdo->prepare("INSERT INTO flows (bot_id, name, trigger_words, message_sequence) VALUES (:bot_id, :name, :trigger_words, :message_sequence)");
     $stmt->bindParam(':bot_id', $botID);
     $stmt->bindParam(':name', $flowName);
     $stmt->bindParam(':trigger_words', $trigger_words);
     $stmt->bindParam(':message_sequence', $message_sequence  );
     $stmt->execute();
      //Obtener el flow_id recién insertado
     $flow_id = $pdo->lastInsertId();
     $response = array('status' => 'success', 'message' => 'registerOk','flow_id' => $flow_id,'bot_id' => $botID);
   }

   catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;
}

 public function delFlow($flow_id) {
   try {
      $pdo = getPDOInstance();
      $stmt = $pdo->prepare("SELECT flow_id FROM flows WHERE flow_id = ?");
      $stmt->bindValue(1, $flow_id);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if (isset($row['flow_id'])) { // Preparamos la consulta para eliminar el elemento
      $stmt = $pdo->prepare("DELETE FROM flows WHERE flow_id = ?");
      $stmt->bindValue(1, $flow_id);
      $stmt->execute();

      $flowTvsC= $this->getFlowTvsC($_POST['botID']);
      //print_r($flowTvsC);
        
      $response = array('status' => 'success', 'message' => 'delFlowOk');
      $response['can_have'] = $flowTvsC['can_have'];
      $response['total_flows'] = $flowTvsC['total_flows'];
       } else{
      $response = array('success' => false, 'message' => 'noMedia');
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

public function getFlowTvsC($itemID) {

  try {
   $pdo = getPDOInstance();
  // veo cuantos negocios tiene
   $stmt = $pdo->prepare("SELECT COUNT(*) AS total_flows FROM flows WHERE bot_id  = :bot_id");
   $stmt->bindParam(':bot_id', $itemID);
   $stmt->execute();
   $result = $stmt->fetch(PDO::FETCH_ASSOC);

  $total_flows = $result['total_flows'];
      // veo cuantas tiendas puede tener
   $stmt = $pdo->prepare("SELECT flow_limit FROM memberships WHERE bot_id = :bot_id");
   $stmt->bindParam(':bot_id', $itemID);
   $stmt->execute();
   $result = $stmt->fetch(PDO::FETCH_ASSOC);
  //var_dump($result);
   $can_have = $result['flow_limit'];

   $response = array('status' => 'success', 'can_have' => $can_have,'total_flows' => $total_flows);

 } catch (PDOException $e) {
  $response = array('status' => 'error', 'message' => $e->getCode());
} finally {
  $pdo = null;
}

return $response;
}


public  function getFlowDetails($params) {
  $flowID=$params['flowID'];
  $botID=$params['botID'];


  try {
    $pdo = getPDOInstance();

    $stmt =$pdo->prepare("SELECT COUNT(*) as total_flows FROM flows WHERE bot_id = :bot_id");
    $stmt->bindParam(':bot_id', $botID);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['total_flows'];

    $sql = "SELECT * FROM flows WHERE flow_id = :flowID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':flowID', $flowID);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!isset($row[0])) {
      $response = array('success' => false, 'message' => '404');
    }
    else{
    
      $response = array('status' => 'success','row'=>$row[0], 'total'=>$count);

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


public function getFlowTypes() {
    try {
      $pdo = getPDOInstance();
      $stmt = $pdo->prepare("
        SELECT type_id, name, description
        FROM flow_types 
        ORDER BY name ASC
        ");
      $stmt->execute();
      $flowListType = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($flowListType) {
        $response = array('status' => 'success', 'message' => '', 'flowListType' => $flowListType);
      } else {
        $response = array('success' => false, 'message' => 'noListFtypes');
      }
    } catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    } finally {
      $pdo = null;
    }

    return $response;
  }

public  function updateFlowName(){

  try {
    $pdo = getPDOInstance();
    $flow_name = sanitize($_POST['flow_name']);

    $stmt = $pdo->prepare("UPDATE flows 
      SET 
      name = :name 
      WHERE flow_id = :flow_id");

    $stmt->bindParam(':flow_id', $_POST['flowID']);
    $stmt->bindParam(':name', $flow_name);
    $stmt->execute();

    $response = array('status' => 'success', 'message' => 'updateOk');

  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;
}

public  function updateFlowDatas(){

  try {
    $pdo = getPDOInstance();
    $trigger_words = sanitize($_POST['trigger_words']);
    $messages = sanitize($_POST['messages']);
    $flow_name = sanitize($_POST['flow_name']);
 
     $stmt = $pdo->prepare("UPDATE flows 
      SET 
      name = :name ,
      trigger_words = :trigger_words, 
      message_sequence = :message_sequence, 
      type = :type
      WHERE flow_id = :flow_id");

    $stmt->bindParam(':flow_id', $_POST['flowID']);
    $stmt->bindParam(':name', $flow_name);
    $stmt->bindParam(':trigger_words', $trigger_words);
    $stmt->bindParam(':message_sequence', $messages);
    $stmt->bindParam(':type', $_POST['flowType']);
    $stmt->execute();

    $response = array('status' => 'success', 'message' => 'updateOk');

  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;
}


public  function editNext(){

$botID = $_POST['botID'];
$current_id = $_POST['flowID'];
$direction = $_POST['direction'];

  try {
    $pdo = getPDOInstance();
    
    if ($direction=="next") {
    $sql = "(
    SELECT flow_id 
    FROM flows 
    WHERE bot_id = :bot_id
      AND flow_id > :current_id
    ORDER BY flow_id ASC
    LIMIT 1
)
UNION ALL
(
    SELECT flow_id 
    FROM flows 
    WHERE bot_id = :bot_id
    ORDER BY flow_id ASC
    LIMIT 1
)
LIMIT 1;

    ";
    }
    else{
      $sql = "(
    SELECT flow_id 
    FROM flows 
    WHERE bot_id = :bot_id
      AND flow_id < :current_id
    ORDER BY flow_id DESC
    LIMIT 1
)
UNION ALL
(
    SELECT flow_id 
    FROM flows 
    WHERE bot_id = :bot_id
    ORDER BY flow_id DESC
    LIMIT 1
)
LIMIT 1;

    ";
    }
    
 
     $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':bot_id', $botID);
    $stmt->bindParam(':current_id', $current_id);
    $stmt->execute();

    $next_id = $stmt->fetchColumn();
    $response = array('status' => 'success', 'next_id' => $next_id);

  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;
}


/*fin de la clase*/
}
