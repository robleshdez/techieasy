<?php

require_once realpath( ABSPATH . "app/models/slug/SlugModel.php");


class SlugController  {  

    protected $metasController;
    private $slugModel;


    public function __construct() {
        $this->metasController = MetasController::getInstance();
        $this->slugModel = new slugModel();

    }
    public function initializeConfig($params){  
        //var_dump($params);

        $actionName=$params['actionName'];
// Configuraciones específicas del controlador slug
        if ($actionName=='contabilidad'){
            $title='Sistema de contabilidad para empresas | LiangApp';
            $description='LiangApp te ayudará a gestionar tu contabilidad de la forma más sencilla, sin preocupaciones y en tiempo real.';
            $keywords='gestionar la contabilidad en tiempo real';
            $ximage= site_url . 'public/img/secciones/contabilidad/rrss.png';
            $ximagealt='contabilidad';
            $ogimage= site_url . 'public/img/secciones/contabilidad/rrss.png';
            $ogimagew='1200';
            $ogimageh='675';
        } elseif($actionName=='facturacion') {
            $title = 'Sistema para hacer facturas en línea | LiangApp';
            $description='Crea facturas de manera eficiente, expórtalas con facilidad y realiza un seguimiento detallado de su estado.';
            $keywords='sistema de facturacion online';
            $ximage= site_url . 'public/img/secciones/facturacion/rrss.png';
            $ximagealt='facturacion';
            $ogimage= site_url . 'public/img/secciones/facturacion/rrss.png';
            $ogimagew='1200';
            $ogimageh='675';
        } elseif($actionName=='capital-humano'){
            $title='Sistema para gestionar tu capital humano | LiangApp';
            $description='';
            $keywords='gestion de recursos humanos';
            $ximage= site_url . 'public/img/secciones/capital-humano/rrss.png';
            $ximagealt='capital-humano';
            $ogimage= site_url . 'public/img/secciones/capital-humano/rrss.png';
            $ogimagew='1200';
            $ogimageh='675';
        } else{ //dejarlos vacio para q tomen los valores generales
            $title=$this->slugModel->test();
            $description='';
            $keywords='';
            $ximage='';
            $ximagealt='';
            $ogimage='';
            $ogimagew='';
            $ogimageh='';
        }
        $this->metasController->setMetaTags([
            !empty($title) ? 'title' : null => $title,
            !empty($description) ? 'description' : null => $description,
            !empty($keywords) ? 'keywords' : null => $keywords,
            !empty($ximage) ? 'ximage' : null => $ximage,
            !empty($ximagealt) ? 'ximagealt' : null => $ximagealt,
            !empty($ogimage) ? 'ogimage' : null => $ogimage,
            !empty($ogimagew) ? 'ogimagew' : null => $ogimagew,
            !empty($ogimageh) ? 'ogimageh' : null => $ogimageh,
            'template'=> $this->slugModel->test(),
        ]);
    }

}

