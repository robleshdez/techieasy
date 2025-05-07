<?php
// app/models/admin/b/BModel.php
// Modelo para la autenticación


class BModel {

  // Manejo de solicitudes de registro
  public function getOwnBotList($userID) {
   $items_per_page = 8;
   $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
   $offset = ($page - 1) * $items_per_page;
   try {
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare("
      SELECT bot_id, name, is_active, is_blocked
      FROM bots 
      WHERE owner_id = :user_id /*AND is_active = :is_active*/
      ORDER BY created_at DESC 
      LIMIT :offset, :items_per_page" 
    );
    $stmt->bindValue(':user_id', $userID, PDO::PARAM_INT);
    /*$stmt->bindValue(':is_active', '0', PDO::PARAM_INT);*/
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($rows);
    /*$logoid= isset($rows[0]['logo_id'])?$rows[0]['logo_id']:null;
    if ($logoid!=null ) {
          $sql = "SELECT media_url FROM medias WHERE media_id = :logo_id";
         $stmt = $pdo->prepare($sql);
         $stmt->bindParam(':logo_id', $logoid);
         $stmt->execute();
         $logourl = $stmt->fetchAll(PDO::FETCH_ASSOC);
         $logourl = site_url . $logourl[0]['media_url'];

       }else{$logourl = "";}*/


       // Consulta para obtener el número total de páginas
       $stmt =  $pdo->prepare("
        SELECT COUNT(*) AS total_bots
        FROM bots
        WHERE owner_id = :user_id");

       $stmt->bindValue(':user_id', $userID, PDO::PARAM_INT);
       $stmt->execute();
       $result = $stmt->fetch(PDO::FETCH_ASSOC);
       $total_items = $result['total_bots'];
       $total_pages = ceil($total_items / $items_per_page);

       if ($rows) {
        $response = array('status' => 'success', 'message' => '', 'rows' => $rows, 'total_pages' => $total_pages, 'page' => $page,);
        return $response;
      } else {
        $response = array('success' => false, 'message' => 'noBots');
      }
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }finally {
      $pdo = null;
    }
    return $response; 

  }

  public function getBotCategoryList() {
    try {
      $pdo = getPDOInstance();
      $stmt = $pdo->prepare("
        SELECT category_id, name, description
        FROM bot_categories 
        ORDER BY name ASC
        ");
      $stmt->execute();
      $listBcategory = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($listBcategory) {
        $response = array('status' => 'success', 'message' => '', 'listBcategory' => $listBcategory);
      } else {
        $response = array('success' => false, 'message' => 'noListBcategory');
      }
    } catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    } finally {
      $pdo = null;
    }

    return $response;
  }

  public  function addBot(){

    try {
      $pdo = getPDOInstance();
  // veo cuantas tiendas tiene
      $stmt = $pdo->prepare("SELECT COUNT(*) AS total_bots FROM bots WHERE owner_id  = :user_id");
      $stmt->bindParam(':user_id', $_SESSION['userID']);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $total_bots = $result['total_bots'];
      // veo cuantas tiendas puede tener
      $stmt = $pdo->prepare("SELECT bots_limit FROM user_memberships WHERE user_id = :user_id");
      $stmt->bindParam(':user_id', $_SESSION['userID']);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $can_have = $result['bots_limit'];
      if ($total_bots >=$can_have) {
       $response = array('success' => false, 'message' => 'overPlan');
       return $response;
       exit;
     }


     $bot_name = sanitize($_POST['bot_name']);
     $stmt = $pdo->prepare("INSERT INTO bots (owner_id, name, category) VALUES (:owner_id, :name,:category)");
     $stmt->bindParam(':owner_id', $_SESSION['userID']);
     $stmt->bindParam(':name', $bot_name);
     $stmt->bindParam(':category', $_POST['bot_category_val']);
     $stmt->execute();

      //Obtener el bot_id recién insertado
     $bot_id = $pdo->lastInsertId();

     //establezco el nivel de permiso para el bot
     $stmt = $pdo->prepare("INSERT INTO user_permissions (user_id, bot_id, permission_level) VALUES (:user_id, :bot_id, 'owner')");
     $stmt->bindParam(':user_id', $_SESSION['userID']);
     $stmt->bindParam(':bot_id', $bot_id);
     $stmt->execute();
     $start_date = date('Y-m-d'); // Formato de fecha: Año-Mes-Día
    //creo la membrecía del negocio
     $stmt = $pdo->prepare("INSERT INTO memberships (bot_id, start_date) VALUES (:bot_id, :start_date)");
     $stmt->bindParam(':bot_id', $bot_id);
     $stmt->bindParam(':start_date', $start_date);
     $stmt->execute();

     //Creo el primer floW
     $flowName="Flow de Bienvenida";
     $trigger_words=json_encode(['hola', 'buenas', 'hello']);
     $message_sequence=json_encode([
      [
        "id" => 1,
        "type" => "text",
        "content" => "¡Hola! Bienvenido a *Botzy*.\n¿En qué puedo ayudarte hoy?"
    ]
    ]) ;
     $stmt = $pdo->prepare("INSERT INTO flows (bot_id, name, trigger_words, message_sequence) VALUES (:bot_id, :name, :trigger_words, :message_sequence)");
     $stmt->bindParam(':bot_id', $bot_id);
     $stmt->bindParam(':name', $flowName);
     $stmt->bindParam(':trigger_words', $trigger_words);
     $stmt->bindParam(':message_sequence', $message_sequence  );
     $stmt->execute();



      //Creo las libretas de contacto inicales
     $stmt = $pdo->prepare("INSERT INTO contacts_group (bot_id, name) VALUES (:bot_id, :name)");
     $name='Personal';
     $stmt->bindParam(':bot_id', $bot_id);
     $stmt->bindParam(':name', $name);
     $stmt->execute();
     $name='Nuevos';
     $stmt = $pdo->prepare("INSERT INTO contacts_group (bot_id, name) VALUES (:bot_id, :name)");
     $stmt->bindParam(':bot_id', $bot_id);
     $stmt->bindParam(':name',  $name);
     $stmt->execute();
    
   //creo la historia de la membrecia o la orden de pago, pensarlo mejor

     $response = array('status' => 'success', 'message' => 'registerOk','bot_id' => $bot_id);

   }
   catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;
}



public  function updateBot(){

  try {
    $pdo = getPDOInstance();
    $bot_name = sanitize($_POST['bot_name']);
    $isActive= isset($_POST['isActive'])? $_POST['isActive']: 0;

    $stmt = $pdo->prepare("UPDATE bots 
      SET 
      name = :name, 
      is_active = :isActive         
      WHERE bot_id = :bot_id");

    $stmt->bindParam(':bot_id', $_POST['botID']);
    $stmt->bindParam(':name', $bot_name);
    $stmt->bindParam(':isActive', $isActive);
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

public  function updateBotStatus(){

  try {
    $pdo = getPDOInstance();
    $isActive= isset($_POST['isActive'])? $_POST['isActive']: 0;

    $stmt = $pdo->prepare("UPDATE bots 
      SET 
      is_active = :isActive         
      WHERE bot_id = :bot_id");

    $stmt->bindParam(':bot_id', $_POST['botID']);
    $stmt->bindParam(':isActive', $isActive);
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


public function getBotTvsC() {
  try {
   $pdo = getPDOInstance();
  // veo cuantos negocios tiene
   $stmt = $pdo->prepare("SELECT COUNT(*) AS total_bots FROM bots WHERE owner_id  = :user_id");
   $stmt->bindParam(':user_id', $_SESSION['userID']);
   $stmt->execute();
   $result = $stmt->fetch(PDO::FETCH_ASSOC);

   $total_bots = $result['total_bots'];
      // veo cuantas tiendas puede tener
   $stmt = $pdo->prepare("SELECT bots_limit FROM user_memberships WHERE user_id = :user_id");
   $stmt->bindParam(':user_id', $_SESSION['userID']);
   $stmt->execute();
   $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //var_dump($result);
   $can_have = $result['bots_limit'];

   $response = array('status' => 'success', 'can_have' => $can_have,'total_bots' => $total_bots);

 } catch (PDOException $e) {
  $response = array('status' => 'error', 'message' => $e->getCode());
} finally {
  $pdo = null;
}

return $response;
}



public  function getBotDetails($itemID) {
  try {
    $pdo = getPDOInstance();
    $sql = "SELECT * FROM bots WHERE bot_id = :itemID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':itemID', $itemID);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!isset($row[0])) {
      $response = array('success' => false, 'message' => '404');
    }
    else{
      $response = array('status' => 'success','row'=>$row);

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



public function delbot() {
   try {
      $pdo = getPDOInstance();


      // borramos del sistema
      $stmt = $pdo->prepare("SELECT bot_id FROM bots WHERE bot_id = ?");
      $stmt->bindValue(1, $_POST['botID']);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
    

      if (isset($row['bot_id'])) { // Preparamos la consulta para eliminar el elemento
      $stmt = $pdo->prepare("DELETE FROM bots WHERE bot_id = ?");
      $stmt->bindValue(1, $_POST['botID']);
      $stmt->execute();
        
      $response = array('status' => 'success', 'message' => 'delBotOk');
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





/* metodos de flujos pero q se ejecutan desde el bot controller*/

public function getBotFLowList($bot_id) {
    $items_per_page = 8;
   $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
   $offset = ($page - 1) * $items_per_page;
   try {
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare("
      SELECT flow_id, bot_id, name, type, trigger_words
      FROM flows 
      WHERE bot_id = :bot_id 
      ORDER BY created_at ASC 
      LIMIT :offset, :items_per_page" 
    );
    $stmt->bindValue(':bot_id', $bot_id, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($rows);
     

       // Consulta para obtener el número total de páginas
       $stmt =  $pdo->prepare("
        SELECT COUNT(*) AS total_flows
        FROM flows
        WHERE bot_id = :bot_id");

       $stmt->bindValue(':bot_id', $bot_id, PDO::PARAM_INT);
       $stmt->execute();
       $result = $stmt->fetch(PDO::FETCH_ASSOC);
       $total_items = $result['total_flows'];
       $total_pages = ceil($total_items / $items_per_page);

       if ($rows) {
        $stmt =  $pdo->prepare("SELECT name FROM flow_types WHERE type_id = :type_id");
        $stmt->bindParam(':type_id', $rows[0]['type']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $rows[0]['type']=$result['name'];
        $response = array('status' => 'success', 'message' => '', 'rows' => $rows, 'total_pages' => $total_pages, 'page' => $page,);
        return $response;
      } else {
        $response = array('success' => false, 'message' => 'noFlows');
      }
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }finally {
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



 



public  function updateWtsCnt(){
    if ($_POST['fullPhoneNumber']!='') {
        $fullPhoneNumber=$_POST['fullPhoneNumber'];
    }else {
        $fullPhoneNumber=null;
      }

 try {
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare("SELECT mobile_number FROM bots WHERE mobile_number = :fullPhoneNumber AND bot_id != :bot_id");
    $stmt->execute([
            'fullPhoneNumber' => $fullPhoneNumber, 
            'bot_id' => $_POST['botID']
        ]);
     $datas = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($datas) {
       $response = array('success' => false, 'message' => 'numberExists');
       return $response;
       exit;
    }
    
    $stmt = $pdo->prepare("SELECT mobile_number, is_active, is_blocked FROM bots WHERE bot_id = :bot_id");
    $stmt->execute([
            'bot_id' => $_POST['botID']
        ]);
     $datas = $stmt->fetch(PDO::FETCH_ASSOC);
 
    $isActive= $fullPhoneNumber=="" ? 0: $datas['is_active'];
    $stmt = $pdo->prepare("UPDATE bots 
      SET 
      mobile_number = :fullPhoneNumber, 
      country_code = :countryCode,
      is_active = :isActive         
      WHERE bot_id = :bot_id");

    $stmt->bindParam(':bot_id', $_POST['botID']);
    $stmt->bindParam(':fullPhoneNumber',  $fullPhoneNumber);
    $stmt->bindParam(':countryCode',  $_POST['countryCode']);
    $stmt->bindParam(':isActive', $isActive);
    $stmt->execute();

    $sectionWAPI= $fullPhoneNumber=="" ? 'eliminate': 'preserve';

    $response = array('status' => 'success', 
      'message' => 'updateOk',
      'isActive' => $datas['is_active'],
      'isBlocked' => $datas['is_blocked'],
      'sectionWAPI' => $sectionWAPI
    );
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
