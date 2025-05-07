<?php
// app/models/admin/AdminModel.php
 

class HomeModel {
 
 
  public function getHomeStat() {
   try {
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare("
        SELECT 
            (SELECT COUNT(*) FROM users) AS total_users,
            (SELECT COUNT(*) FROM bots) AS total_bots,
            (SELECT COUNT(*) FROM flows) AS total_flows;
    " );
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
     if ($rows) {
        $rows  =$rows[0];
        $response = array('status' => 'success', 'total_users' => $rows['total_users'], 'total_bots'=>$rows['total_bots'], 'total_flows'=>$rows['total_flows']);
        return $response;
      } else {
        $response = array('success' => false, 'message' => 'nostat');
      }
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }finally {
      $pdo = null;
    }
    return $response; 

  }



public function getTotalBots() {
   try {
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare(" 
      SELECT 
      COUNT(*) AS total_bots,
    SUM(CASE WHEN is_active = true AND is_blocked = false THEN 1 ELSE 0 END) AS total_active,
    SUM(CASE WHEN is_active = false AND is_blocked = false THEN 1 ELSE 0 END) AS total_inactive,
    SUM(CASE WHEN is_blocked = true THEN 1 ELSE 0 END) AS total_blocked
    FROM bots;" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //$total_bots=$total_active=$total_inactive=$total_blocked=0;
    if ($result) {
      $result=$result[0];
        $response = array('status' => 'success', 'total_bots' => $result['total_bots'], 'total_active' => $result['total_active'],'total_inactive' => $result['total_inactive'],'total_blocked' => $result['total_blocked']);
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




 




/*fin de la clase*/
}
