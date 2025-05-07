<?php
// app/models/admin/User/UserModel.php

class UserModel {

  public function userPermissions($bot_id) {
  //vemos si el usuario q pide los detalles es owner o admin

    try {
      $pdo = getPDOInstance();
      $sql = "SELECT permission_level FROM user_permissions WHERE bot_id = :bot_id AND user_id = :user_id";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':bot_id', $bot_id);
      $stmt->bindParam(':user_id', $_SESSION['userID']);
      $stmt->execute();
      $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
 
      if (isset($row[0])) {
        if (($row[0]['permission_level']!='owner' && $row[0]['permission_level']!='gestor')) {
          $response = false;
        } else{
        $response= true;
      } 
    }else {
        $response = false;
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

public function userIsAdmin($user_id) {
  //vemos si el usuario q pide los detalles es owner o admin
  try {
    $pdo = getPDOInstance();
    $sql = "SELECT role FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $role = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }finally {
    $pdo = null;
  }
   return ($role !='admin') ?  false : true ; 
}

public function userRole($user_id) {
  //vemos si el usuario q pide los detalles es owner o admin
  try {
    $pdo = getPDOInstance();
    $sql = "SELECT role FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id',$user_id);
      $stmt->execute();
      $role = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }finally {
      $pdo = null;
    }
    return $role; 
  }


  /*fin de la clase*/
}
