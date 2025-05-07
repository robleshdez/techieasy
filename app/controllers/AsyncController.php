<?php
// app/controllers/AsyncController.php
// Controlador para crear y ejecutar las funciones asincrónicas, hace uso del auxulair ClosureWrapper.php para envolcer y desemvolver las closures con el contexto

require_once realpath(__DIR__.'/../../core/Load.php');
require_once realpath(ABSPATH . "public/vendor/PHPAsync/ClosureWrapper.php");
 

class Async {
    protected $serializedWrapper;


    public function create(Closure $closure) {
        
        $wrapper = ClosureWrapper::serialize($closure);
        $this->serializedWrapper = serialize($wrapper);

       //file_put_contents('epadebug_async.txt', print_r($this->serializedWrapper, true));

        $this->run($this->serializedWrapper);
    }

    public function run($serialized) {
       
        $nullDevice = (strncasecmp(PHP_OS, 'WIN', 3) == 0) ? 'NUL' : '/dev/null';
        $executeURI = realpath(ABSPATH . "public/vendor/PHPAsync/execute.php");
        $serializedB64=base64_encode($serialized);
        //$serializedB64=$serialized;

        $command = (strncasecmp(PHP_OS, 'WIN', 3) == 0) 
            ? "powershell -Command \"Start-Process php -ArgumentList '" . escapeshellarg($executeURI)  . "', '" . escapeshellarg($serializedB64) . "' -WindowStyle Hidden\""
            : "php " . escapeshellarg($executeURI)  . " " . escapeshellarg($serializedB64) . " > $nullDevice 2>&1 &";
         //$command="php " . $executeURI ;   
        //echo $command;
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            error_log("Error en la ejecución asincrónica. Código de retorno: $return_var. Salida: " . implode("\n", $output), 3, 'async_errors.log');
        }
        
    //echo "Output:\n" . implode("\n", $output) . "\n";
    //echo "Return Var: " . $return_var . "\n";
    }
     
}
