<?php

class ErrorController {  
    protected $metasController;

    public function __construct() {
        $this->metasController = MetasController::getInstance();
    }

    public function initializeConfig($routeParams, $datas=[]) {
    // Configuraciones específicas del controlador Admin
        //var_dump($datas);
        $this->metasController->setMetaTags([
            'title' => 'Error - '.$routeParams['actionName'],
            'tolink' => isset($datas['tolink']) ? $datas['tolink'] : '',
            'infoMsg' =>isset($datas['infoMsg']) ? $datas['infoMsg'] : '',
        ]);

 
        $this->metasController->setCssLinks([
            '/public/css/404/404.css',
        ]);

    }

}