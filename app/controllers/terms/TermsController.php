<?php


class TermsController  {  
   protected $metasController;

   public function __construct() {
      $this->metasController = MetasController::getInstance();
   }

   public function initializeConfig() {        

      $this->metasController->setMetaTags(['title' => 'Términos y Condiciones de Uso']);

      $this->metasController->setCssLinks([
         'public/vendor/bebots/style.css',
         'public/css/home/home.css',
      ]);
       $this->metasController->setJsScripts([
         'public/js/app/home/home.js',  
        ]);

   }





}
