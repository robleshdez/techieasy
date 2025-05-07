<?php

require_once realpath(ABSPATH . "app/controllers/AsyncController.php");
require_once realpath( ABSPATH . "app/controllers/EmailController.php");
require_once realpath( ABSPATH . "app/models/home/HomeModel.php");

class HomeController  {  
   protected $metasController;
   protected $asyncController;
   protected $emailController;
   protected $homeModel;

   public function __construct() {
      $this->metasController = MetasController::getInstance();
        $this->homeModel = new HomeModel() ;
        $this->emailController =new EmailController() ;


   }

   public function initializeConfig() {        
// Configuraciones específicas del controlador Home

      $this->metasController->setCssLinks([
         'public/vendor/bebots/style.css',
         'public/vendor/aos/aos.css',
         'public/css/home/home.css',
      ]);
       $this->metasController->setJsScripts([
         'public/vendor/aos/aos.js',
         'public/vendor/purecounter/purecounter_vanilla.js',
         'public/js/app/home/home.js',
             
        ]);

   }

   public function getHomeStat(){
         $res=$this->homeModel->getHomeStat();
      return $res ;//
   }


     public function contactEmail() {
         if (empty($_POST['contact_name']) || empty($_POST['contact_email'])|| empty($_POST['contact_message'])) {
            $response = array('success' => false, 'message' => 'emptyField');
            return $response; 
            exit;
        }
 
            $toMe = [
                'email' => $_POST['contact_email'],
                'title' =>'Contacto desde Botzy',// 
                'name' => ucfirst(current(explode('@', $_POST['contact_name']))),
                 'subject' => $_POST['contact_subject'],
                'message' =>  $_POST['contact_message'],
                'logo' => rtrim(site_url, '/') . "/public/img/logo.png",
                'htmlContent' => 'app/views/templates/contactTemplate.html'
            ];

            $mailed=$this->emailController->sendAsyncMail($toMe);
             
            if ($mailed=="okMailSend") {
              $response = array('status' => 'success', 'message' => 'sendOK');
            }
            else {
              $response = array('success' => false, 'message' => $mailed);
            }
        

        $response['token'] = 'Ja te lo creiste';//anulo el token en la respuesta json
        return $response;
    }









}
