<?php   

include 'conexion.php';

/*=============================================
CORS
=============================================*/
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
   
   header('Access-Control-Allow-Origin: *');
   header("Access-Control-Allow-Headers: X-API-KEY,Origin,X-Requested-With, Content-Type, Accept,
           Access-Control-Request-Method,Access-Request-Headers,Authorization");
   header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
   header('content-type: application/json; charset=utf-8');
   header('HTTP/1.1 200 OK');
   die();
}

$json = file_get_contents('php://input'); //RECIBE LOS DATOS EN JSON DESDE ANGULAR

$params = json_decode($json); //DECODIFICA EL JSON Y LO GUARDA EN UNA VARIABLE

$pdo = new Conexion(); //ESTABLECE CONEXION CON UNA NUEVA INSTANCIA


//OBTENER TODOS LOS DATOS Y OBTENER UN SOLO DATO POR ID

if($_SERVER['REQUEST_METHOD'] == 'GET'){
   
    if(isset($_GET['id'])){
        
        $sql =$pdo->prepare("SELECT * FROM contacto WHERE id=:id");
        $sql -> bindValue(':id',$_GET['id']);
        $sql -> execute();
        $sql -> setFetchMode(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;

    }else{

        $sql =$pdo->prepare("SELECT * FROM contacto");
        $sql ->execute();
        $sql ->setFetchMode(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
        
    }
}

//REGISTRAR DATOS
 
if($_SERVER['REQUEST_METHOD'] == 'POST'){

     // obtener los valores obligatorios de los campos
     $nombre = $params ->nombre;
     $telefono = $params ->telefono;
     $email = $params ->email;

    // Validar campos
    if (empty($nombre) || strlen($nombre) < 4){
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['mensaje' => 'El nombre debe contener al menos 4 caracteres'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (empty($telefono) || strlen($telefono) < 6){
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['mensaje' => 'El telefono debe contener al menos 6 caracteres'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['mensaje' => 'El email NO tiene formato válido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

     // Verificar si el correo electrónico ya existe en la base de datos
     $email = $params->email;
     $checkSql = "SELECT id FROM contacto WHERE email = :email";
     $checkStmt = $pdo->prepare($checkSql);
     $checkStmt->bindValue(':email', $email);
     $checkStmt->execute();
 
     if ($checkStmt->fetch()) {
         // El correo electrónico ya existe en la base de datos, mostrar un mensaje de error
         header("HTTP/1.1 400 Bad Request");
         echo json_encode(['mensaje' => 'Este email ya está en uso'], JSON_UNESCAPED_UNICODE);
         exit;
     }

        $sql = "INSERT INTO contacto (nombre, telefono, email, imagen) VALUES (:nombre, :telefono, :email, :imagen)";
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindValue(':nombre', $params -> nombre);
        $stmt -> bindValue(':telefono', $params -> telefono);
        $stmt -> bindValue(':email', $params -> email);
        $stmt -> bindValue(':imagen', $params -> imagen);
        $stmt -> execute();
        $idPost = $pdo -> lastInsertId();
        
     
        if ($idPost){
            header("HTTP/1.1 200 OK");
            echo json_encode(['mensaje'=>'Registro agregado con exito']);
            exit;
        }
}
  

// Actualizar Registro
if($_SERVER['REQUEST_METHOD'] == 'PUT'){

    $sql = "UPDATE contacto SET nombre=:nombre, telefono=:telefono, email =:email, imagen=:imagen WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindValue(':nombre', $params->nombre);
    $stmt -> bindValue(':telefono', $params->telefono);
    $stmt -> bindValue(':email', $params->email);
    $stmt -> bindValue(':imagen', $params->imagen);
    $stmt -> bindValue(':id', $_GET['id']);
    $stmt -> execute();

    header("HTTP/1.1 200 OK");
    echo json_encode(['mensaje'=>'Registro se actualizó con éxito'], JSON_UNESCAPED_UNICODE);
    exit;

}