<footer id="footer">
<?php //echo $this->metasController->getFooterWidget() ?>
<div class="footer-credits  pb-4">
    <div class="">
<?php echo $this->metasController->getFooterCredits() ?>
    </div>
</div>
</footer>


<script>
<?php echo 'var site_url = "' . site_url . '";'; ?>
</script>

<!-- Dinamic JS File -->
<?php 
$jsScripts = $this->metasController->getJsScripts();
 
foreach ($jsScripts as $jsScript):
 $jsSRC= (substr($jsScript, 0, 8) === "https://") ?$jsScript: site_url.$jsScript;?>

  <script type="text/javascript" src="<?= $jsSRC ?>"></script>
  
<?php endforeach; ?>

<script type="text/javascript">
    (function(){
        var data = '{\
  "@context": "https://schema.org",\
  "@graph": [\
    {\
      "@type": "WebPage",\
      "@id": "https://botzy.app/",\
      "url": "https://botzy.app/",\
      "name": "Botzy - Crea chatbots fácil y gratis para emprendedores",\
      "alternateName": ["Botzy App", "Botzy Builder Bots"],\
      "isPartOf": {\
        "@id": "https://botzy.app/#website"\
      },\
      "about": {\
        "@id": "https://botzy.app/#organization"\
      },\
      "primaryImageOfPage": {\
        "@id": "https://botzy.app/#primaryimage"\
      },\
      "image": {\
        "@id": "https://botzy.app/#primaryimage"\
      },\
      "thumbnailUrl": "https://botzy.app/public/img/apple-touch-icon.png",\
      "description": "Crea tu gratis chatbot en minutos con Botzy. Automatiza respuestas en WhatsApp, captura leads y aumenta tus ventas fácilmente.",\
      "inLanguage": "es"\
    },\
    {\
      "@type": "ImageObject",\
      "inLanguage": "es",\
      "@id": "https://botzy.app/#primaryimage",\
      "url": "https://botzy.app/public/img/apple-touch-icon.png",\
      "contentUrl": "https://botzy.app/public/img/apple-touch-icon.png",\
      "width": 500,\
      "height": 500\
    },\
    {\
      "@type": "WebSite",\
      "@id": "https://botzy.app/#website",\
      "url": "https://botzy.app/",\
      "name": "Botzy - Crea chatbots fácil y gratis para emprendedores",\
      "description": "Crea tu gratis chatbot en minutos con Botzy. Automatiza respuestas en WhatsApp, captura leads y aumenta tus ventas fácilmente.",\
      "publisher": {\
        "@id": "https://botzy.app/#organization"\
      }\
    },\
    {\
      "@type": "Organization",\
      "@id": "https://botzy.app/#organization",\
      "name": "Botzy - Crea chatbots fácil y gratis para emprendedores",\
      "url": "https://botzy.app/",\
      "logo": {\
        "@type": "ImageObject",\
        "inLanguage": "es",\
        "@id": "https://botzy.app/#/schema/logo/image/",\
        "url": "https://botzy.app/public/img/apple-touch-icon.png",\
        "contentUrl": "https://botzy.app/public/img/apple-touch-icon.png",\
        "width": 500,\
        "height": 500,\
        "caption": "Botzy chatbots gratis"\
      },\
      "image": {\
        "@id": "https://botzy.app/#/schema/logo/image/"\
      },\
      "sameAs": [\
        "https://www.facebook.com/GorvetEstudios/",\
        "https://x.com/GorvetEstudios",\
        "https://www.instagram.com/gorvetestudios/",\
        "https://www.youtube.com/channel/UCTWQJWqUuTn3Nd_dBWD0aQA"\
      ]\
    }\
  ]\
}\
';

        var arr = [
        '__ENTERPRISE__', 
        '__IMAGE__', 
        '__LOGO__',
        '__URL__',
        ];
        var val = [
        'Botzy',
        'https://botzy.app/public/img/favicon.png',
        'https://botzy.app/public/img/favicon.png',
        'https://botzy.app',
        ];

        //for(var idx in arr){ data = data.replace(arr[idx], val[idx]); }

        var script = document.createElement('script');
        script.type = "application/ld+json";

        script.innerHTML = data;
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
</script>

 

<!-- Toast container -->
<div id="toastBox" class=""> </div>


</body>
</html>