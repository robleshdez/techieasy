<?php
// app/models/AuthModel.php
// Modelo para la autenticación


class AuthModel {

  // Manejo de solicitudes de registro
  public function registerAcount() {

  //Verificación de correo electrónico único
    try {
      $pdo = getPDOInstance();
      $pdo->beginTransaction();
      $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :register_email");
      $stmt->execute(['register_email' => $_POST['register_email']]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($user) {
        $response = array('success' => false, 'message' => 'userExists');
        return $response;
        exit;
      }
    // Agregar nuevo usuario a la base de datos
      $options = [
        'cost' => 12, // el número de rondas de cifrado (cuanto mayor sea, más lento será el cifrado)
      ];

    // Generar token de verificación
    // Aquí no hacemos uso de getUserToken pues no se ha insertado el usuario
      $token = bin2hex(random_bytes(32));

      $stmt = $pdo->prepare("INSERT INTO users (email, password, token) VALUES (:register_email, :register_password, :ruser_token)");
      $stmt->execute([
        'register_email' => $_POST['register_email'],
        'register_password' => password_hash($_POST['register_password'],  PASSWORD_BCRYPT, $options),
        'ruser_token' => $token,
      ]);
       $stmt = $pdo->prepare("INSERT INTO user_memberships (user_id) VALUES (:user_id)");
       $user_id = $pdo->lastInsertId();
      $stmt->execute([
        'user_id' => $user_id,
      ]);

      $pdo->commit();
      $response = array('status' => 'success', 'message' => 'registerOk','token' => $token);
    }
    catch (PDOException $e) {
      $pdo->rollBack();
      $response = array('status' => 'error', 'message' => $e->getCode());
    }
    finally {
        $pdo = null;
    }

    return $response; 

  }


  // Manejo de solicitudes de validación mediante el token en el correo
  public function validateAcount() {

    try {
      $pdo = getPDOInstance();
      $stmt = $pdo->prepare("SELECT user_id, status FROM users WHERE token = :vtoken");
      $stmt->execute(['vtoken' => $_POST['vtoken']]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && $user['status']!='suspended') {
        $user_id = $user['user_id']; // Almacena el ID del usuario
        // Generar nuevo token de verificación
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("UPDATE users SET status= :user_status, token= :user_token, token_updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id ");
        $stmt->execute([
          'user_status' => 'verify',
          'user_token' => $token,
          'user_id' => $user_id
        ]);

        $response = array('status' => 'success', 'message' => 'userVerify');
      } else{
        $response = array('success' => false, 'message' => 'invalidToken');
      } 
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }

    $pdo = null;
    return $response; 

  }

  //Manejo de solicitudes de recuperacion, se hace la petición del token para el correo 
  public function recoveryAcount() {

    try {
      $pdo = getPDOInstance();
      $stmt = $pdo->prepare("SELECT email, token FROM users WHERE email = :recovery_email");
      $stmt->execute(['recovery_email' => $_POST['recovery_email']]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user) {
        $token = bin2hex(random_bytes(32));//regeneramos el token aquí tambien

        $stmt = $pdo->prepare("UPDATE users SET token= :user_token, token_updated_at = CURRENT_TIMESTAMP WHERE email = :recovery_email ");
        $stmt->execute([
          'recovery_email' => $_POST['recovery_email'],
          'user_token' => $token
        ]);
        $response = array('status' => 'success','token' => $token);
      } else{
        $response = array('success' => false, 'message' => 'notExists');
      }
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }

    $pdo = null;
    return $response;

  }

  //Manejo de solicitudes de resetear contraseña mediante el token en el correo
  public function resetPassword() {

    try {
      $pdo = getPDOInstance();
      $stmt = $pdo->prepare("SELECT token_updated_at, user_id FROM users WHERE token = :user_token");
      $stmt->execute(['user_token' => $_POST['rpuser_token']]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user) {
      $token_updated_at = strtotime($user['token_updated_at']);
      $intervalo = 86400; // 24h Intervalo en segundos
      $now = time();
      $diferencia = $now - $token_updated_at;
      //echo ' ahora'.$ahora;
      //echo ' token '.$token_updated_at;
      //echo ' diferencia '.$diferencia;

      if ($diferencia <= $intervalo) {
        // verificamos la antiguedad del token
        $options = ['cost' => 12,];
        $user_id = $user['user_id'];
        $token = bin2hex(random_bytes(32));
        $newpassword  =  password_hash($_POST['reset_password'],  PASSWORD_BCRYPT, $options);
        $stmt = $pdo->prepare("UPDATE users SET password= :user_password, token= :user_token, token_updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id ");
        $stmt->execute([
          'user_id'=> $user_id,
          'user_password' => $newpassword,
          'user_token' => $token
        ]);

        $response = array('status' => 'success', 'message' => 'okReset');

      } else {
        $response = array('success' => false, 'message' => 'invalidToken');
      }
    } else{
      $response = array('success' => false, 'message' => 'invalidToken');
    }
  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  $pdo = null;
  return $response; 
} 

  // Manejo de solicitudes de login
public function login() {
  try {
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :login_email");
    $stmt->execute(['login_email' => $_POST['login_email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($_POST['login_password'], $user['password'])) {
      $response = array('success' => false, 'message' => 'invalidUser');  
    } else {
      if ($user['status']==='unverify') {
        $response = array('success' => false, 'message' => 'unverifyAcount');
      } elseif ($user['status']==='suspended') {
        $response = array('success' => false, 'message' => 'suspendedAcount');
      } else{

        $stmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = :user_id ");
        $stmt->execute([
         'user_id' => $user['user_id']
        ]);

        $response = array('status' => 'success');
        // Establecimiento de sesión de usuario
        $_SESSION['userID'] = $user['user_id'];
        $_SESSION['userEmail'] = $user['email'];
        $_SESSION['userRole'] = $user['role'];
        $_SESSION['lastActivity'] = time();
        $_SESSION['csrfTimestamp'] = time();
      } 
    }

  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }

  $pdo = null;
  return $response;  

}

 // Manejo de solicitudes de reenvío del correo de verificación
public function verifyAcount() {
  try {
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :login_email");
    $stmt->execute(['login_email' => $_POST['login_email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      $response = array('success' => false, 'message' => 'invalidUser');  
    } elseif ($user['status']==='suspended') {
      $response = array('success' => false, 'message' => 'suspendedAcount');
    } elseif ($user['status']==='unverify') {
        //reenviamos el correo
      //primero regeneramos el token y su fecha
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("UPDATE users SET token= :user_token, token_updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id ");
        $stmt->execute([
          'user_id'=> $user['user_id'],
          'user_token' => $token
        ]);
      $response = array('status' => 'success', 'message' => 'userOk','token' => $token);
    }   

  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }

  $pdo = null;
  return $response;  

}

public function getUserToken($email){
    try {
      $pdo = getPDOInstance();;
      $stmt = $pdo->prepare("SELECT user_id, token FROM users WHERE email = :email");
      $stmt->execute(['email' => $email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($user) {
        // Generar token de verificación
        $token = bin2hex(random_bytes(32));
           $stmt = $pdo->prepare("UPDATE users SET token= :user_token, token_updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id ");
          $stmt->execute([
          'user_id'=> $user['user_id'],
          'user_token' => $token
          ]);
         
        $response = array('status' => 'success', 'message' => 'userOk','token' => $token);

        return $response;
        exit;
      }
      else{ //no existe el usuario con ese correo
        $response = array('success' => false, 'message' => 'invalidUser');
      }

    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }

    $pdo = null;
    return $response; 

}
/*fin de la clase*/
}
