<?php

// Clave secreta para encriptar y desencriptar los datos
$secretKey = 'your_secret_key_here'; // Sustituye esto con la clave secreta

// Asegurarse de que la clave tenga una longitud de 32 bytes (256 bits)
$finalKey = hash('sha256', $secretKey, true);  // Usamos SHA-256 para obtener una clave de 256 bits

$algorithm = 'aes-256-ctr'; // Algoritmo de encriptación
$ivLength = 16; // Longitud del IV (16 bytes)

// Función para generar un IV aleatorio
function generateIv() {
    return openssl_random_pseudo_bytes(16);  // Genera un IV de 16 bytes aleatorios
}

// Función para encriptar
function encrypt($text, $key) {
    // Generar IV aleatorio
    $iv = generateIv();
    // Encriptar el texto
    $encryptedData = openssl_encrypt($text, 'aes-256-ctr', $key, 0, $iv);
    
    // Retornamos el IV y los datos encriptados juntos en un arreglo
    return [
        'iv' => bin2hex($iv),  // Convertimos el IV a formato hexadecimal
        'encryptedData' => bin2hex($encryptedData)  // Convertimos los datos encriptados a formato hexadecimal
    ];
}

// Función para desencriptar
function decrypt($encryptedData, $iv, $key) {
    // Convertir datos encriptados y IV de hexadecimal a binario
    $encryptedData = hex2bin($encryptedData);
    $iv = hex2bin($iv);
    
    // Desencriptar los datos
    $decrypted = openssl_decrypt($encryptedData, 'aes-256-ctr', $key, 0, $iv);
    return $decrypted;
}

// Ejemplo de uso:
/*$plaintext = "Texto a encriptar";

// Encriptar el texto
$encrypted = encrypt($plaintext, $finalKey);
echo "Datos Encriptados:\n";
print_r($encrypted);

// Desencriptar el texto
$decrypted = decrypt($encrypted['encryptedData'], $encrypted['iv'], $finalKey);
echo "Texto Desencriptado:\n";
echo $decrypted;*/

?>
