<?php
// app/models/admin/b/BModel.php
// Modelo para el manejo de imágenes

class MModel {

  private $thumbnailSizes = array(
    'xsmall' => array('width' => 50, 'height' => 50),
    'small' => array('width' => 150, 'height' => 150),
    'medium' => array('width' => 500, 'height' => 500),
//'banner' => array('width' => 1500, 'height' => 500),
  );

  public function addMedias($businessId) {
    $disk_capacity = $this->disk_capacity($businessId);
    $total_space=$this->getTotalSpace($businessId);
      if ($total_space['totalSizeInMB'] >=$disk_capacity['disk_capacity']) {
        $response = array('success' => false, 'message' => 'overPlan');    
      } else{
          $response = $this->save_files($businessId);
        }
    $disk_capacity = $this->disk_capacity($businessId);
    $total_space=$this->getTotalSpace($businessId);
     $response['total_space'] = $total_space['totalSizeInMB'];
     $response['disk_capacity'] = $disk_capacity['disk_capacity'];   
    return $response;
  }




public function save_files($businessId) {

  $businessFolder =  ABSPATH . 'uploads/'.$businessId;

  if (!file_exists($businessFolder)) {
    mkdir($businessFolder, 0777, true);  
  }

// Obtener el archivo subido
  $file = $_FILES['file'];
  $originalFileName=pathinfo($file['name']);
  $originalFileName=$originalFileName['filename'];

// Limpiar el nombre del archivo
  $cleanedFileName = preg_replace("/[^a-zA-Z0-9-_\.]/", "-", $file['name']);
  $cleanedFileName = strtolower($cleanedFileName);

// Verificar si ya existe un archivo con el mismo nombre
  $baseFileName = pathinfo($cleanedFileName, PATHINFO_FILENAME);
  $extension = pathinfo($cleanedFileName, PATHINFO_EXTENSION);
  $counter = 1;
  while (file_exists($businessFolder . '/' . $cleanedFileName)) {
    $cleanedFileName = $baseFileName . '-' . $counter . '.' . $extension;
    $counter++;
  }

// Ruta completa para la imagen original
  $originalFilePath = $businessFolder . '/' . $cleanedFileName;
  $urlFile2DB= 'uploads/'.$businessId. '/' .$cleanedFileName;

// Guardar en BD
  $response= $this->insertIntoMedias($businessId, $urlFile2DB, $originalFileName);

  if ($response['status']=='success') {
    move_uploaded_file($file['tmp_name'], $originalFilePath);
    $this->resizeAndCropImage($businessFolder, $originalFilePath, $cleanedFileName, $extension);
    $response = array('status' => 'success', 'message' => 'addOK');
  }
  return $response;
}

private function insertIntoMedias($businessId, $urlFile2DB, $originalFileName) {
  try { 
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare("
      INSERT INTO medias 
      (media_url, business_id, name) 
      VALUES (:media_url, :business_id, :name)
      ");
    $stmt->bindParam(':media_url', $urlFile2DB);
    $stmt->bindParam(':business_id', $businessId);
    $stmt->bindParam(':name', $originalFileName);
    $stmt->execute();
    $response=array('status' => 'success', 'message' => 'salvado en db'); 
  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
    return $response;
  }

}

private function resizeAndCropImage($businessFolder, $originalFilePath, $cleanedFileName,$extension){
  foreach ($this->thumbnailSizes as $sizeName => $sizeInfo) {
    $sizeWidth = $sizeInfo['width'];
    $sizeHeight = $sizeInfo['height'];

    $thumbnailFileName = str_replace('.' . $extension, '-' . $sizeName . '.' . $extension, $cleanedFileName);
    $thumbnailFilePath = $businessFolder . '/' . $thumbnailFileName;

    // Redimensionar y recortar la imagen
    $this->ejResizeAndCropImage($originalFilePath, $thumbnailFilePath, $sizeWidth, $sizeHeight, $extension);
  }
}


// Función para redimensionar y recortar imágenes
public function ejResizeAndCropImage($sourceFilePath, $targetFilePath, $newWidth, $newHeight, $extension) {
  error_reporting(E_ERROR | E_PARSE);
  list($width, $height) = getimagesize($sourceFilePath);

// Calcular nuevas dimensiones manteniendo la relación de aspecto
  $ratioWidth = $width / $newWidth;
  $ratioHeight = $height / $newHeight;

  if ($ratioWidth > $ratioHeight) {
    $cropWidth = $newWidth * $ratioHeight;
    $cropHeight = $newHeight * $ratioHeight;
  } else {
    $cropWidth = $newWidth * $ratioWidth;
    $cropHeight = $newHeight * $ratioWidth;
  }

  $cropX = ($width - $cropWidth) / 2;
  $cropY = ($height - $cropHeight) / 2;

// Crear una imagen en blanco con las nuevas dimensiones
  $newImage = imagecreatetruecolor($newWidth, $newHeight);
// Habilitar soporte de transparencia para PNG
  if ($extension == 'png') {
    imagealphablending($newImage, false);
    imagesavealpha($newImage, true);
  }

// Cargar la imagen original
  if ($extension == 'jpg' || $extension == 'jpeg') {
    $sourceImage = imagecreatefromjpeg($sourceFilePath);
  } elseif ($extension == 'png') {
    $sourceImage = imagecreatefrompng($sourceFilePath);
  }

// Redimensionar y recortar la imagen al centro
  imagecopyresampled($newImage, $sourceImage, 0, 0, $cropX, $cropY, $newWidth, $newHeight, $cropWidth, $cropHeight);

// Guardar la imagen redimensionada y recortada en el archivo de destino
  if ($extension == 'jpg' || $extension == 'jpeg') {
imagejpeg($newImage, $targetFilePath, 90); // Guardar como JPEG con calidad 90%
} elseif ($extension == 'png') {
imagepng($newImage, $targetFilePath, 9, PNG_ALL_FILTERS); // Guardar como PNG con transparencia
}

// Liberar memoria
imagedestroy($sourceImage);
imagedestroy($newImage);
error_reporting(E_ALL);
}



public function getTotalSpace($businessId) {
$businessFolder =  ABSPATH . 'uploads/'.$businessId;
if (is_dir($businessFolder)) {
    // La carpeta existe, ahora puedes obtener la lista de archivos en la carpeta
    $files = scandir($businessFolder);
    // Inicializar una variable para el tamaño total
    $totalSizeInBytes = 0;
    $totalItems=0;
// Iterar sobre los archivos en la carpeta
foreach ($files as $file) {
    // Excluir directorios y archivos especiales
    if ($file != "." && $file != ".." && is_file($businessFolder . '/' . $file)) {
        // Obtener el tamaño del archivo y sumarlo al total
        $totalSizeInBytes += filesize($businessFolder . '/' . $file);
        $totalItems= $totalItems +1;
    }
}
 // Convertir el tamaño total a MB
$totalSizeInMB = $totalSizeInBytes / (1024 * 1024);
$totalSizeInMB = round($totalSizeInMB, 2);
$response=array('status' => 'success', 'totalSizeInMB'=>$totalSizeInMB,'totalItems'=>$totalItems);
}
else {
  $response=array('status' => 'success', 'totalSizeInMB'=>0,'totalItems'=>0); ; 
}
return $response;
}
public function disk_capacity($businessId){

    try {
      $pdo = getPDOInstance();
      $stmt = $pdo->prepare("SELECT disk_capacity FROM memberships WHERE business_id = :business_id");
      $stmt->bindParam(':business_id', $businessId);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      //var_dump($result);
      if ($result) {
        $response=array('status' => 'success', 'disk_capacity'=>$result['disk_capacity']); 
      }
      else{
        //q voy a responder si no encuentra 
      }
    }
    catch (PDOException $e) {
      $response = array('status' => 'error', 'message' => $e->getCode());
    }
    finally {
      $pdo = null;
      return $response;
    }
  }







public function getMediasList($businessId) {
  $items_per_page = 18;
  $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
  $offset = ($page - 1) * $items_per_page;
  try {
    $pdo = getPDOInstance();
    $stmt = $pdo->prepare("
      SELECT media_id, media_url
      FROM medias 
      WHERE business_id = :business_id
      ORDER BY created_at DESC 
      LIMIT :offset, :items_per_page" 
    );
    $stmt->bindValue(':business_id', $businessId, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Consulta para obtener el número total de páginas
    $stmt =  $pdo->prepare("
      SELECT COUNT(*) AS total_medias
      FROM medias
      WHERE business_id = :business_id");

    $stmt->bindValue(':business_id', $businessId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_items = $result['total_medias'];
    $total_pages = ceil($total_items / $items_per_page);

    if ($rows) {
      $response = array('status' => 'success', 'message' => '', 'rows' => $rows, 'total_pages' => $total_pages, 'page' => $page);
      return $response;
    } else {
      $response = array('success' => false, 'message' => 'noMedias');
    }
  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }finally {
    $pdo = null;
  }
  return $response; 

}

 

 


public  function getMediaDetails($itemID) {
  try {
    $pdo = getPDOInstance();

    $sql = "SELECT * FROM medias WHERE media_id = :media_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':media_id', $itemID);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($row[0])) {
      $mediaInfo= $this->getMediaInformation(realpath( ABSPATH . $row[0]['media_url']));
      $fechaFormateada = date('d-M-Y', strtotime($row[0]['created_at']));
      $row[0]['created_at']=$fechaFormateada;
      $row[0]['mediaInfo']=$mediaInfo;
      $response = array('status' => 'success','row'=>$row[0]);
    }else{
      $response = array('success' => false, 'message' => 'noMedia');
    }
  }
  catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }

  return $response;
}



public function getMediaInformation($ruta) {
  $file_size = getimagesize($ruta);
  $file_weight = round(filesize($ruta) / 1024, 2);
  $mediaInfo=[
    'file_info'=>$file_size,
    'file_weight'=>$file_weight,
  ];
  return $mediaInfo;
}


public function delMedia($media_id) {
   try {
      $pdo = getPDOInstance();
      $stmt = $pdo->prepare("SELECT media_url FROM medias WHERE media_id = ?");
      $stmt->bindValue(1, $media_id);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if (isset($row['media_url'])) { // Preparamos la consulta para eliminar el elemento
      $stmt = $pdo->prepare("DELETE FROM medias WHERE media_id = ?");
      $stmt->bindValue(1, $media_id);
      $stmt->execute();

      $this->del_files($row['media_url']);
        $disk_capacity = $this->disk_capacity($_POST['businessID']);
        $total_space=$this->getTotalSpace($_POST['businessID']);
       
        $response = array('status' => 'success', 'message' => 'delMediaOk');
         $response['total_space'] = $total_space['totalSizeInMB'];
        $response['disk_capacity'] = $disk_capacity['disk_capacity'];
       }else{
      $response = array('success' => false, 'message' => 'noMedia');
        }
   }
   catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;  
}

private function del_files($mediaURL){

$filePath = realpath( ABSPATH . $mediaURL);

if (file_exists($filePath)) {
unlink($filePath);
   }
// Obtener el nombre del archivo sin la ruta
$fileName = basename($filePath);
// Obtener la extensión del archivo
$extension = pathinfo($fileName, PATHINFO_EXTENSION);
// Eliminar las miniaturas
foreach ($this->thumbnailSizes as $key => $value) {
  $sizeName=$key;
   $thumbnailFileName = str_replace('.' . $extension, '-' . $sizeName . '.' . $extension, $fileName);
    $thumbnailFilePath = str_replace($fileName, $thumbnailFileName, $filePath);
     if (file_exists($thumbnailFilePath)) {
      unlink($thumbnailFilePath);
      }
   }  
}

public function editMedia($media_id){

  try {
      $pdo = getPDOInstance();

      $stmt = $pdo->prepare("SELECT media_id FROM medias WHERE media_id = :media_id");
      $stmt->bindParam(':media_id', $media_id, PDO::PARAM_INT);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (isset($row)) {
        //, alt_text = :alt_text
      $stmt = $pdo->prepare("UPDATE medias SET name = :name WHERE media_id = :media_id");
      $stmt->bindParam(':media_id', $media_id);
      $name = sanatize($_POST['img_name']);
      $stmt->bindParam(':name', $name);
      //$stmt->bindParam(':alt_text', $_POST['alt_text']);
      $stmt->execute();
      $response = array('status' => 'success', 'message' => 'updated');
      } else{
        $response = array('success' => false, 'message' => 'noMedia');
      }
   }
   catch (PDOException $e) {
    $response = array('status' => 'error', 'message' => $e->getCode());
  }
  finally {
    $pdo = null;
  }
  return $response;

}



/*fin de la clase*/
}
