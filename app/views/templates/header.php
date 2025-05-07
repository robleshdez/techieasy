<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="utf-8">
   <meta content="width=device-width, initial-scale=1.0" name="viewport">

   <title><?= $this->metasController->getMetaTag('title') ?></title>

   <!-- Metas Básicas -->

   <meta name="description" content="<?= $this->metasController->getMetaTag('description') ?>" >
   <meta name="keywords" content="<?= $this->metasController->getMetaTag('keywords') ?>" >
   <meta name="author" content="<?= $this->metasController->getMetaTag('author') ?>" >

   <!-- Metas de Twitter -->
   <meta name="twitter:card" content="<?= $this->metasController->getMetaTag('xcard') ?>" />
   <meta name="twitter:title" content="<?= $this->metasController->getMetaTag('title') ?>">
   <meta name="twitter:description" content="<?= $this->metasController->getMetaTag('description') ?>">
   <meta name="twitter:image" content="<?= $this->metasController->getMetaTag('ximage') ?>">
   <meta name="twitter:image:alt" content="<?= $this->metasController->getMetaTag('ximagealt') ?>">
   <meta name="twitter:site" content="<?= $this->metasController->getMetaTag('xsite') ?>">
   <meta name="twitter:creator" content="<?= $this->metasController->getMetaTag('xcreator') ?>">

   <!-- Metas de Open Graf (Facebook) -->
   <meta property="og:type" content="<?= $this->metasController->getMetaTag('ogtype') ?>" />
   <meta property="og:site_name" content="<?= $this->metasController->getMetaTag('ogsite_name') ?>" />
   <meta property="og:url" content="<?= $this->metasController->getMetaTag('ogurl') ?>" />
   <meta property="og:locale" content="<?= $this->metasController->getMetaTag('oglocale') ?>" />
   <meta property="og:title" content="<?= $this->metasController->getMetaTag('title') ?>">
   <meta property="og:description" content="<?= $this->metasController->getMetaTag('description') ?>" />
   <meta property="og:image"content="<?= $this->metasController->getMetaTag('ogimage') ?>">
   <meta property="og:image:width"content="<?= $this->metasController->getMetaTag('ogimagew') ?>"> 
   <meta property="og:image:height"content="<?= $this->metasController->getMetaTag('ogimageh') ?>">
   <meta property="og:image:alt"content="<?= $this->metasController->getMetaTag('ximagealt') ?>">  

   <!-- Indexación y seguimiento de links -->
   <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">


   <link rel="canonical" href="<?php echo site_url?>" />


   <!-- Favicons -->
   <link href="<?php echo site_url . 'public/img/favicon.png' ?>" rel="icon">
   <!-- <link href="<?php echo site_url . 'favicon.ico' ?>" rel="icon"  type="image/x-icon"> -->
   <link href="<?php echo site_url . 'public/img/apple-touch-icon.png' ?>" rel="
   apple-touch-icon">

   <!-- Dinamic CSS File -->
   <?php $cssLinks = $this->metasController->getCssLinks();
   foreach ($cssLinks as $cssLink): 
   $cssSRC= (substr($cssLink, 0, 8) === "https://") ?$cssLink: site_url.$cssLink;?>
   
   <link rel="stylesheet" type="text/css" href="<?= $cssSRC ?>">

  <?php endforeach; ?> 

</head>


<body data-gf-theme="light" class="<?php echo $bodyClass; ?>">

    


