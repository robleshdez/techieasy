<?php
// app/models/admin/b/BModel.php
// Modelo para la autenticación


class PModel extends ORM{

    protected $table = 'projects';
    protected $primaryKey = 'project_id';
    protected $fillable = ['project_code', 'name', 'description','start_date','end_date'];
    protected $casts = ['project_id' => 'int'];


public static function getCurrencies() {

     try {
        $rows = self::queryTable('currencies')
            ->selectAll()// o select('*') si preferís
            ->orderBy('code')
            ->get();

        if ($rows) {
            return [
                'status' => 'success',
                'message' => 'ok',
                'rows' => $rows
            ];
        } else {
            return [
                'success' => false,
                'message' => 'notOk'
            ];
        }
    } catch (PDOException $e) {
        $response = array(
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        );
    }
    return $response; 
}

public static function getWorkers() {
    try {
         $rows = self::queryTable('workers')
            ->select('worker_id', 'name')
            ->get();
        if ($rows) {
            return [
                'status' => 'success',
                'message' => 'ok',
                'rows' => $rows
            ];
        } else {
            return [
                'success' => false,
                'message' => 'notOk'
            ];
        }
    } catch (PDOException $e) {
        $response = array(
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        );
    }
    return $response; 
}

public static function getGestors() {
    try {
         $rows = self::queryTable('users')
            ->select('user_id', 'email')
            ->get();

        if ($rows) {
            return [
                'status' => 'success',
                'message' => 'ok',
                'rows' => $rows
            ];
        } else {
            return [
                'success' => false,
                'message' => 'notOk'
            ];
        }
    } catch (PDOException $e) {
        $response = array(
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        );
    }
    return $response; 
}
   

public function addProject() {
    try {
        self::beginTransaction();

        // 1. Crear proyecto con ORM
        $project = new self([
            'name' => $_POST['project_name'],
            'description' => $_POST['description'],
            'project_code' => $_POST['project_name'],
            'start_date' => $_POST['start_date'] ?: null,
            'end_date' => $_POST['end_date'] ?: null
        ]);
        $project_id = $project->save();

        // 2. Insertar monto inicial si existe
        $initialAmount = floatval($_POST['initial_amount'] ?? 0);
        if ($initialAmount > 0) {
            self::raw("
                INSERT INTO project_profits (project_id, amount, currency, pd_date, description)
                VALUES (?, ?, ?, ?, ?)", [
                    $project_id,
                    $initialAmount,
                    $_POST['initial_currency'],
                    date('Y-m-d'),
                    'Fondo inicial'
            ], 'none');

            // 3. Actualizar wallet
            $this->updateWallet(self::$pdo, $project_id);

            // 4. Log del ingreso
            $this->logAction(
                self::$pdo,
                $_SESSION['userID'],
                'Crear ingreso inicial;',
                'project_profits',
                self::raw("SELECT LAST_INSERT_ID()", [], 'column'),
                json_encode([
                    'project_id' => $project_id,
                    'amount' => $_POST['initial_amount'],
                    'currency' => $_POST['initial_currency'],
                    'pd_date' => date('Y-m-d'),
                    'description' => 'Fondo inicial'
                ])
            );
        }

        // 5. Asignar trabajadores
        $workers = $_POST['workers'] ?? [];
if (!empty($workers)) {
    foreach ($workers as $worker_id) {
        self::raw(
            "INSERT INTO project_worker (project_id, worker_id) VALUES (?, ?)",
            [$project_id, $worker_id],
            'none'
        );
    }
}


        // 6. Asignar gestores
        $gestors = $_POST['gestors'] ?? [];
if (!empty($gestors)) {
    foreach ($gestors as $user_id) {
        self::raw(
            "INSERT INTO project_users (project_id, user_id, role) VALUES (?, ?, 'gestor')",
            [$project_id, $user_id],
            'none'
        );
    }
}


        // 7. Log del proyecto
        $this->logAction(
            self::$pdo,
            $_SESSION['userID'],
            'Crear proyecto;',
            'projects',
            $project_id,
            json_encode([
                'project_id' => $project_id,
                'name' => $_POST['project_name'],
                'description' => $_POST['description'],
                'gestors' => $gestors,
                'workers' => $workers
            ])
        );

        self::commit();

        return [
            'status' => 'success',
            'message' => 'registerOk',
            'project_id' => $project_id
        ];
    } catch (PDOException $e) {
        self::rollBack();
        return [
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ];
    }
}



  public  function addProject2(){

    try {
      $pdo = getPDOInstance();
      $pdo->beginTransaction();

  
      $stmt = $pdo->prepare(
  "INSERT INTO projects (name, description, start_date, end_date)
   VALUES (:name, :description, :start_date, :end_date)");

      $stmt->execute([
        'name' => $_POST['project_name'],
        'description' => $_POST['description'],
        'start_date' => $_POST['start_date'] ?: null,
        'end_date' => $_POST['end_date'] ?: null
      ]);

      $project_id = $pdo->lastInsertId();

      // 2. Si hay monto inicial, insertarlo en project_profits
$initialAmount = floatval($_POST['initial_amount'] ?? 0);
if ($initialAmount > 0) {
        $stmt = $pdo->prepare("INSERT INTO project_profits 
          (project_id, amount, currency, pd_date, description)
          VALUES (:project_id, :amount, :currency, :pd_date, :description)");
        $stmt->execute([
          'project_id' => $project_id,
          'amount' => $initialAmount,
          'currency' => $_POST['initial_currency'],
          'pd_date' => date('Y-m-d'),
          'description' => 'Fondo inicial'
        ]);

// 3. Actualizar wallet
        $this->updateWallet($pdo,$project_id);

// 4. Log del ingreso
        $data=[
          'project_id' => $project_id,
          'amount' => $_POST['initial_amount'],
          'currency' => $_POST['initial_currency'],
          'pd_date' => date('Y-m-d'),
          'description' => 'Fondo inicial'
        ];
        $this->logAction($pdo,$_SESSION['userID'],'Crear ingreso inicial;', 'project_profits', $pdo->lastInsertId(), json_encode($data));
      }

// 5. Asignar trabajadores
      $workers = $_POST['workers'] ?? [];
      $stmt = $pdo->prepare("INSERT INTO project_worker (project_id, worker_id) VALUES (:project_id, :worker_id)");
      foreach ($workers as $worker_id) {
        $stmt->execute([
          'project_id' => $project_id,
          'worker_id' => $worker_id
        ]);
      }

// 6. Asignar gestores
      $gestors = $_POST['gestors'] ?? [];
      $stmt = $pdo->prepare("INSERT INTO project_users (project_id, user_id, role) VALUES (:project_id, :user_id, 'gestor')");
      foreach ($gestors as $user_id) {
        $stmt->execute([
          'project_id' => $project_id,
          'user_id' => $user_id
        ]);
      }

// 7. Log del proyecto
       $data=[
          'project_id' => $project_id,
          'name' => $_POST['project_name'],
          'description' => $_POST['description'],
          'gestors' => $gestors,
          'workers' => $workers
        ];
        $this->logAction($pdo,$_SESSION['userID'],'Crear proyecto;', 'projects', $project_id, json_encode($data));
       
      

      $pdo->commit();


      $response = array('status' => 'success', 'message' => 'registerOk','project_id' => $project_id);

    }
    catch (PDOException $e) {
        $pdo->rollBack();
      $response = array('status' => 'error','message' => $e->getMessage(),'code' => $e->getCode());
    }
    finally {
      $pdo = null;
    }
    return $response;
  }



 
 private function updateWallet($pdo,$project_id) {
   $stmt = $pdo->prepare("
    INSERT INTO project_wallets (project_id, currency, balance)
    VALUES (:project_id, :currency, :amount)
    ON DUPLICATE KEY UPDATE balance = balance + :amount
  ");
  $stmt->execute([
    'project_id' => $project_id,
    'currency' => $_POST['initial_currency'],
    'amount' => $_POST['initial_amount']
  ]);
}



private function logAction($pdo,$user_id, $action, $table, $target_id, $details = null) {
   $stmt = $pdo->prepare(
    "INSERT INTO logs (user_id, action, target_table, target_id, details) 
     VALUES (:user_id, :action, :target_table, :target_id, :details)");
  $stmt->execute([
    'user_id' => $user_id,
    'action' => $action,
    'target_table' => $table,
    'target_id' => $target_id,
    'details' => $details
  ]);
}

public static function getProjectsForUser($userID, $userRole) {
    $itemsPerPage = 5;
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    try {
        $instance = new static();

        if ($userRole === 'admin') {
            $query = $instance->newQuery()->orderBy('created_at', 'ASC');
            $rows = $query->paginate($page, $itemsPerPage);
            $totalItems = $instance->newQuery()->count(); // separada para no heredar filtros del anterior
        } else {
            $query = $instance->newQuery()
                ->join('project_users pu', 'pu.project_id', '=', 'projects.project_id')
                ->where([
                    ['pu.user_id', '=', $userID],
                     ['pu.role', '=', 'gestor']
                ])
                ->select('projects.project_id', 'projects.name', 'projects.description')
                ->orderBy('projects.created_at', 'ASC');

            $rows = $query->paginate($page, $itemsPerPage);

            // Para contar: mismo WHERE pero sin LIMIT
             $totalItems=   $instance->newQuery()
                ->join('project_users pu', 'pu.project_id', '=', 'projects.project_id')
                ->where([
                    ['pu.user_id', '=', $userID],
                    ['pu.role', '=', 'gestor']
                ])
                ->count('projects.project_id');  
        }

        $totalPages = ceil($totalItems / $itemsPerPage);

        $response = [
            'status' => 'success',
            'message' => 'updateOk',
            'rows' => $rows,
            'total_pages' => $totalPages,
            'page' => $page,
            'userRole' => $userRole
        ];
    } catch (PDOException $e) {
        $response = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ];
    } finally {
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














/*fin de la clase*/
}
