<?php 
// MetasController.php

class MetasController {
    protected $metaTags = [];
    protected $cssLinks = [];
    protected $jsScripts = [];
    protected $credits;
    private static $instance;

    public function __construct() {
        $this->initializeConfig();
    }

    /*public static function getInstanceCount() {
        return self::$instanceCount;
    }*/

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function initializeConfig() {
        // Configuraciones generales
        $this->setMetaTags([ // Meta etiquetas para SEO
            'title' => 'Botzy - Crea chatbots fácil y gratis para emprendedores', // Recomendado no mas de 50 caracteres
            'description' => 'Crea tu gratis chatbot en minutos con Botzy. Automatiza respuestas en WhatsApp, captura leads y aumenta tus ventas fácilmente.', // Recomendado no más de 155 caracteres
            'keywords' => 'Cómo hacer un chatbot para WhatsApp en Cuba', // ya no se usan
            'author' => 'Juank de Gorvet',
            'xcard' => 'summary_large_image',
            //'xtitle' => '',
            //'xdescription' => '   ',
            'ximage' => site_url . 'public/img/apple-touch-icon.png',
            'ximagealt' => 'Botzy Chatbot',
            'xsite' => '@GorvetEstudios',// @usuario de twitter
            'xcreator' => '@GorvetEstudios',// @usuario de twitter
            'ogtype' => 'website', //article, blog, ...buscar otras
            'ogsite_name' => 'Botzy - Chatbots fáciles para emprendedores',
            'ogurl' => site_url,
            'oglocale' => 'es_ES',
            //'ogtitle' => '',
            //'ogdescription' => '',
            'ogimage' => site_url . 'public/img/apple-touch-icon.png',
            'ogimagew' => '750', // defina el tamaño de la imagen de vista previa  
            'ogimageh' => '750', // defina el tamaño de la imagen de vista previa
            //'ogimagealt' => '',
            'infolink' => site_url,
        ]);

        //Define los CSS que serán comunes en todas las vistas
        $this->setCssLinks([
            'public/vendor/bootstrap/css/bootstrap.min.css',
            'public/vendor/sweetalert2/sweetalert2.min.css',
            'public/vendor/sweetalert2/sweetTheme.css',
            'public/css/common.css',
             //'public/css/variables.css',
           
        ]);

        //Define los JS que serán comunes en todas las vistas
        $this->setJsScripts([
            'public/vendor/jquery/jquery.min.js',
            'public/vendor/bootstrap/js/bootstrap.bundle.js',
            'public/js/utils/utils.js',
            'public/js/utils/alertToast.js',
            'public/vendor/sweetalert2/sweetalert2.all.min.js',
            'public/js/app/auth/AuthLogout.js',
        ]);
        
        $year=date('Y');
        $credits='
        <div class="copyright">
            <span>&copy; '.$year.' <strong><span>Botzy</span></strong>. Todos los Derechos Reservados</span>
        </div>
        <div class="credits">
            <span>Un producto de <a href="https://gorvet.com/">Gorvet Estudios </a> Potenciado por <a href="#">TechiEasy</a><span>
        </div>';
        $this->setFooterCredits($credits);

    }

    public function setMetaTags($metaTags) { 	 
        $this->metaTags = array_merge($this->metaTags, $metaTags);
    }

    public function setCssLinks($cssLinks) {
        $this->cssLinks = array_merge($this->cssLinks, $cssLinks);
    }

    public function setJsScripts($jsScripts) {
        $this->jsScripts = array_merge($this->jsScripts, $jsScripts);
    }

    public function setFooterCredits($credits) {
        $this->credits = $credits;
    }

    public function getMetaTag($tagName) {
        return isset($this->metaTags[$tagName]) ? $this->metaTags[$tagName] : '';
    }

    public function getCssLinks() {
        return $this->cssLinks;
    }

    public function getJsScripts() {
        return $this->jsScripts;
    }

    public function getFooterCredits() {
        return $this->credits;
    }


    
}
