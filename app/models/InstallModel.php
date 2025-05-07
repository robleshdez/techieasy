<?php
// InstallModel.php

require_once realpath( __DIR__.'/../../core/Utils.php');
require_once realpath( __DIR__.'/../../core/UtilsDB.php');

class InstallModel {

	public function	if_db() {

		try {
			$pdo = getPDOInstance();
			// Obtener la lista de tablas en la base de datos
			$tablesQuery = "SHOW TABLES";
			$tablesStmt = $pdo->query($tablesQuery);
			$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
			if ($tables) {
				$response = array('success'=> true,);
			} else {
				$response = array('success'=> false);
			}
		}   
		catch (PDOException $e) {
			$response = array('status' => 'error', 'message' => $e->getCode());
		}
		$pdo = null;
		return $response;
	}

	public function testDBConnection() {
    $dbName = $_POST["dbName"];
    $dbUser = $_POST["dbUser"];
    $dbPass = $_POST["dbPass"];
    $dbServer = $_POST["dbServer"];

    try {
        // Intenta establecer una conexión a la base de datos
        $pdo = new PDO("mysql:host=$dbServer;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifica si la conexión se ha realizado con éxito
        if ($pdo) {
            // Consulta para verificar si la base de datos está vacía
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($tables)) {
                // La base de datos está vacía
                $response = array('status' => 'success', 'message' => 'emptyDB');
            } else {
                // La base de datos contiene tablas
                $response = array('status' => 'error', 'message' => 'noEmptyDB');
            }
        } else {
            // No se pudo establecer la conexión
            $response = array('status' => 'error', 'message' => 'noConnect');
        }
    } catch(PDOException $e) {
        // Captura cualquier excepción que pueda ocurrir durante la conexión
        $response = array('status' => 'error', 'message' => $e->getMessage());
    }

    // Cierre de la conexión
    $pdo = null;

    return $response;
}


	public function configCreate(){
		try {
			$dbName = $_POST["dbName"];
			$dbUser = $_POST["dbUser"];
			$dbPass = $_POST["dbPass"];
			$dbServer = $_POST["dbServer"];
 
			$file = fopen("../../core/Config.php", "w");
			fwrite($file, "<?php\n\n");
			fwrite($file, "/*** Database settings - You can get this info from your web host ***/\n\n");
			fwrite($file, "/* The name of the database for G-Frame */\n");
			fwrite($file, "define( 'DB_NAME', '$dbName' );\n\n");
			fwrite($file, "/* Database username */\n");
			fwrite($file, "define( 'DB_USER', '$dbUser' );\n\n");
			fwrite($file, "/* Database password */\n");
			fwrite($file, "define( 'DB_PASSWORD', '$dbPass' );\n\n");
			fwrite($file, "/* Database hostname */\n");
			fwrite($file, "define( 'DB_HOST', '$dbServer' );\n\n");
			fwrite($file, "/* Database charset to use in creating database tables. */\n");
			fwrite($file, "define( 'DB_CHARSET', 'utf8mb4' );\n\n");
			fwrite($file, "/* Site URL. */\n");
			fwrite($file, "define( 'site_url', guess_url() );\n\n");
			fwrite($file, "/* Debug mode. */\n");
			fwrite($file, "define( 'DebugMode', true );\n\n\n\n");
			fwrite($file, "/** Time Zone */\n\n");
			fwrite($file, "//date_default_timezone_set('America/Havana');\n\n\n\n");
			fwrite($file, "/*** Mailer settings - You can get this info from your web host ***/\n\n");
			fwrite($file, "/* Mail smtp Host*/\n");
			fwrite($file, "define( 'M_Host', 'smtp.yourmailhost.com' );\n\n");
			fwrite($file, "/* Mail smtp port */\n");
			fwrite($file, "define( 'M_Port', 587 );\n\n");
			fwrite($file, "/* Mail username */\n");
			fwrite($file, "define( 'M_Username', 'yname@yourmailhost.com' );\n\n");
			fwrite($file, "/* Mail Password */\n");
			fwrite($file, "define( 'M_Password', 'YourPassword' );\n\n");
			fwrite($file, "/* Mail Secure Protocol */\n");
			fwrite($file, "define( 'M_Secure', 'tls' );\n\n");
			fwrite($file, "/* Mail Sender Address username */\n");
			fwrite($file, "define( 'M_From', 'yname@yourmailhost.com' );\n\n");
			fwrite($file, "/* Mail Sender Name */\n");
			fwrite($file, "define( 'M_Name', 'YourName' );\n\n\n\n");
			fwrite($file, "/*** WhatsApp API ***/\n\n");
			fwrite($file, "/* W API URL */\n");
			fwrite($file, "define( 'WAPI_URL', 'http://localhost:300/' );\n\n");
			fwrite($file, "/* W API key */\n");
			fwrite($file, "define( 'WAPIKEY', '1234' );\n\n\n\n");
			fclose($file);

			$response = array('status' => 'success', 'message' => 'Config.php creado con éxito.');
		}
		catch(PDOException $e) {
			$response = array('status' => 'error', 'message' => $e->getCode());
		}
		return $response;
	}

	public function installer(){
		$uMail = $_POST["uMail"];
		$uPass = $_POST["uPass"];
		$site_url= guess_url();
		$options = ['cost' => 12, // el número de rondas de cifrado (cuanto mayor sea, más lento será el cifrado)
];
		$hashed_password = password_hash($uPass, PASSWORD_BCRYPT, $options);
		$token = bin2hex(random_bytes(32));
	try {
	 	$pdo = getPDOInstance();
	// Ejecutamos el SQL
	$pdo->exec("
		CREATE TABLE users (
		user_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		email VARCHAR(255) NOT NULL,
		password VARCHAR(255) NOT NULL,
		role ENUM('admin', 'gestor', 'worker') NOT NULL DEFAULT 'worker',
		status ENUM('suspended','unverify', 'verify') NOT NULL DEFAULT 'unverify',
		token VARCHAR(255) NOT NULL,
		token_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		last_login TIMESTAMP NULL,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (user_id),
		UNIQUE KEY (email)
	);

	CREATE TABLE workers (
	worker_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id BIGINT(20) UNSIGNED DEFAULT NULL,
	name VARCHAR(50) NOT NULL,
	country_code BIGINT(4) UNSIGNED DEFAULT NULL,
	mobile_number VARCHAR(20)  DEFAULT NULL,
	email VARCHAR(255) DEFAULT NULL,
	description VARCHAR(255) DEFAULT NULL,
	payment_method JSON DEFAULT NULL,
	is_active TINYINT(1) NOT NULL DEFAULT 0,
	is_blocked TINYINT(1) NOT NULL DEFAULT 0,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (worker_id),
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
	INDEX (user_id)
 	);

	CREATE TABLE projects (
	project_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	description VARCHAR(255),
	start_date DATE,
    end_date DATE,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (project_id),
	INDEX (project_id)
	);
	
	CREATE TABLE project_profits (
    pf_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id BIGINT(20) UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    pd_date DATE NOT NULL,
    description VARCHAR(255),
    PRIMARY KEY (pf_id),
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE
);

CREATE TABLE payments (
    payment_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    worker_id BIGINT(20) UNSIGNED NOT NULL,
    project_id BIGINT(20) UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_date DATE NOT NULL,
    concept VARCHAR(255) DEFAULT NULL,      
    role_description VARCHAR(100) DEFAULT NULL,
    payment_method VARCHAR(50) DEFAULT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (payment_id),
    FOREIGN KEY (worker_id) REFERENCES workers(worker_id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
    INDEX (worker_id),
    INDEX (project_id),
    INDEX (payment_date)
);

	 
  	 
	CREATE TABLE project_worker (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id BIGINT(20) UNSIGNED NOT NULL,
    worker_id BIGINT(20) UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (project_id, worker_id),
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
    FOREIGN KEY (worker_id) REFERENCES workers(worker_id) ON DELETE CASCADE
);

CREATE TABLE project_users (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id BIGINT(20) UNSIGNED NOT NULL,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    role ENUM('gestor', 'visor') NOT NULL DEFAULT 'gestor',
    PRIMARY KEY (id),
    UNIQUE KEY (project_id, user_id),
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

 CREATE TABLE logs (
    log_id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED,
    action VARCHAR(100),
    target_table VARCHAR(50),
    target_id BIGINT(20),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

	INSERT INTO users (email, password, role, status, token)
	VALUES ('$uMail', '$hashed_password', 'admin', 'verify' ,'$token');
");


	// Enviamos la respuesta por AJAX
	$response = array('status' => 'success', 'message' => 'La instalación fue completada con éxito');
	} catch (PDOException $e) {
	// Si ocurre un error, mostramos el mensaje de error
		$response = array('status' => 'error', 'message' => $e->getCode());
	}
	$pdo = null;
    return $response;

}


}