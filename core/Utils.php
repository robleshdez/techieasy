<?php 

// core/Utils.php
// Algunas funciones de ayuda que son usadas en varios archivos

/**
 * Recupera la URL del dominio del sitio
 * 
 * @since 0.0.1
 *
 * @return string La URL recuperada.
 */

function guess_url() {

  // Determinamos si es SSL
  $schema = is_ssl() ? 'https://' : 'http://';

  // Determinamos el nomnbre del servidor
  $host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
  //$host = isset($_SERVER['SERVER_NAME']) ? strtolower($_SERVER['SERVER_NAME']) : '';

  if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
  } else {
    $port = '';
  }

  // if (!empty($host) && $host !== $server_name) {
  //   $host = strtolower($host);
  // }

  // Obtener la ruta del script actual; por la dinámica del enrutador simpre será .../index.php pq es el controlador frontal
  $scriptPath = $_SERVER['SCRIPT_NAME'];
  //$scriptPath = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

  // Obtener la carpeta raíz del proyecto
  $projectRoot = dirname($scriptPath);
  if (strpos($projectRoot, "/app/") !== false) {
    $projectRoot = substr($projectRoot, 0, strpos($projectRoot, "/app/"));
}

 

  $url = $schema;

  if (!empty($host)) {
    $url .= $host;
    if (!empty($port)) {
      $url .= ':' . $port;
    }
  } elseif (!empty($_SERVER['SERVER_ADDR'])) {
    $url .= $_SERVER['SERVER_ADDR'];
  } else {
    $url .= '127.0.0.1';
  }

  if (!empty($projectRoot) && $projectRoot != '/') {
     if (substr($projectRoot, -1)=='/') {
      $url .= $projectRoot;
      //echo 'ya tiene';
    }
    else { 
      $url .= $projectRoot.'/';
      //echo 'no tiene se lo a;ado';
    }
    
  }
  elseif (substr($projectRoot, -1)!=='/') {
    //echo 'no  va ya tiene';
     $url;
      
    }
    else {
     //echo 'no va y se lo a;ado/';
     
      $url = $url.'/';
    }
    
  return $url;
}



/**
 * Determina si se está usando SSL.
 *
 * @since 0.0.1
 *
 * @return bool True si es SSL, o false en caso contrario.
 */
function is_ssl() {
  if ( isset( $_SERVER['HTTPS'] ) ) {
    if ( 'on' === strtolower( $_SERVER['HTTPS'] ) ) {
      return true;
    }

    if ( '1' == $_SERVER['HTTPS'] ) {
      return true;
    }
  } elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
    return true;
  }
  return false;
}



function sanitize($value){
 // Eliminar espacios en blanco al final
$noSpaces = rtrim($value);

// Eliminar caracteres HTML y JavaScript
//return  htmlspecialchars($noSpaces, ENT_QUOTES, 'UTF-8', false);
//return preg_replace("/<[^>]*>|&[^;]*;/", " ", $noSpaces);
return strip_tags($noSpaces);

}


function randomNameGen($name) {
    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $resultado = $name.'_'; // Inicia con 'Bot_'
    
    for ($i = 0; $i < 6; $i++) {
        $indice = rand(0, strlen($caracteres) - 1);
        $resultado .= $caracteres[$indice]; // Agrega un carácter aleatorio
    }
    
    return $resultado; // Retorna el nombre generado
}

/*construye el menu*/
function buildMenu($menuItems,$currentURL='') {
  foreach ($menuItems as $item) {
     $id=isset($item[4])? 'id="'.$item[4].'"':'';

    switch ($item[0]) {
      case 'heading':
        echo '<li class="nav-heading">'.$item[1].'</li>';
      break;
      
      case 'item':
      $currentURL = rtrim($currentURL, '/'); // Eliminar '/' al final si existe
      $itemURL = rtrim($item[1], '/');       // Eliminar '/' al final si existe
      $isCurrent = ($itemURL === $currentURL) ? "current" : "";
      
        echo '<li class="nav-item">
          <a '.$id.'class="nav-link d-flex '.$isCurrent.'" href="'.$item[1].'">
            <i class="'.$item[2].'"></i>
              <span>'.$item[3].'</span>
          </a>
        </li>';
      break;

      case 'menu':
        echo '<li class="nav-item">
        <a class="nav-link collapsed d-flex " data-bs-target="#'.$item[1].'" data-bs-toggle="collapse" href="#">
        <i class="'.$item[2].'"></i>
        <span>'.$item[3].'</span>
        <i class="gicon-fd ms-auto"></i>
        </a>';
          if (isset($item[4]) && is_array($item[4])) {
            echo '<ul id="'.$item[1].'" class="nav-content collapse " data-bs-parent="#sidebar-nav">';
            buildMenu($item[4]); // Llamada recursiva para construir submenús
            echo '</ul>';
          }
        echo '</li>';        
      break;

      default:
      break;
      }
    }
  }


/*paginacion */
function pagination($total_pages, $page) {    
  echo '<div class="row mt-5" id="pagination">
    <nav  aria-label="all_items_pagination">
        <ul id="all_items_pagination" class="pagination justify-content-end pagination-sm">';
            
           // Primer item
    echo '<li class="page-item ' . (($page == 1) ? 'disabled' : '') . '">';
    echo ($page == 1) ? '<span class="page-link">Anterior</span>' : '<a class="page-link prev" href="#">Anterior</a>';
    echo '</li>';
    // Iterar sobre las páginas
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<li class="page-item ' . (($page == $i) ? 'active' : '') . '">';
        echo ($page == $i) ? '<span class="page-link">' . $i . '</span>' : '<a class="page-link linkeable" href="#">' . $i . '</a>';
        echo '</li>';
    }
    // Último item
    echo '<li class="page-item ' . (($page == $total_pages) ? 'disabled' : '') . '">';
    echo ($page == $total_pages) ? '<span class="page-link">Siguiente</span>' : '<a class="page-link next" href="#">Siguiente</a>';
    echo '</li>';
  
       echo '</ul>
      </nav>
  </div>';

}