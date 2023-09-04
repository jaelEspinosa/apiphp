<?php   
 header('Access-Control-Allow-Origin: *');
 header("Access-Control-Allow-Headers: X-API-KEY,Origin,X-Requested-With, Content-Type, Accept,Access-Control-Request-Method,Access-Request-Headers,Authorization");
 header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
 header('content-type: application/json; charset=utf-8');
 header('HTTP/1.1 200 OK');

 //** */ MANEJAR LAS SOLICITUDES DE SUBIR ARCHIVOS

 if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $json = file_get_contents('php://input'); //RECIBE EL JSON DE ANGULAR

    $params = json_decode($json); // DECODIFICA EL JSON Y LO  GUARDA EN LA VARIABLE
   
    $nombreArchivo = $params->nombreArchivo;
    $archivo = $params->base64textString;
    $archivo = base64_decode($archivo);
   
    $filePath = $_SERVER['DOCUMENT_ROOT']."/views/img/".$nombreArchivo;
    file_put_contents($filePath, $archivo);
   
    class Result {
       public $resultado;
       public $mensaje;
    }
   
  
   
   header("HTTP/1.1 200 OK");
   echo json_encode(['mensaje'=>'archivo guardado con éxito'], JSON_UNESCAPED_UNICODE);
 }
 

//** */ MANEJAR LAS SOLICITUDES DE ELIMINACION DE ARCHIVOS

if ($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    
    //Obtener el nombre del archivo a borrar en la url
    $nombreArchivoEliminar = $_GET['nombreArchivoEliminar'];
    
    //Verificar si el archivo existe antes de intentar eliminarlo.
    $filePathEliminar = $_SERVER['DOCUMENT_ROOT']."/views/img/".$nombreArchivoEliminar;
   
   if(file_exists($filePathEliminar)){
     
    //El archivo existe y lo intenta eliminar
      if(unlink($filePathEliminar)){
        // el archivo se ha eliminado
        header("HTTP/1.1 200 OK");
        echo json_encode(['mensaje'=>'Registro se actualizó con éxito'], JSON_UNESCAPED_UNICODE);
      }else{
        // Error al eliminar el archivo
        header("HTTP/1.1 400 Bad request");
        echo json_encode(['mensaje'=>'Error al eliminar el archivo'], JSON_UNESCAPED_UNICODE);
      }
   }else{
    
    //El archivo no existe
    header("HTTP/1.1 404 Bad request");
    echo json_encode(['mensaje'=>'Archivo no encontrado'], JSON_UNESCAPED_UNICODE);
   }
}

