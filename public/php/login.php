<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

// Define configuration here
$dbhost = 'localhost';
$dbname = 'chatbox';
$dbuser = 'root';
$dbpass = 'root';

try {
    // Create a new PDO instance
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If there is an error in the connection, stop the script and display the error.
    die("Failed to connect to the database: " . $e->getMessage());
}

// Get JSON as a string
$json_str = file_get_contents('php://input');

// Get as an object
$json_obj = json_decode($json_str);

$email = $json_obj->email;
$password = $json_obj->password;

$stmt = $db->prepare("SELECT * FROM users WHERE mail = :email");
$stmt->bindParam(':email', $email);

$stmt->execute();

if($stmt->rowCount() > 0){
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($password == $user['password']){
        // Correct Password
        echo json_encode(['status' => 'success', 'message' => 'Login successful', 'token' => $email, 'id_user' => $user['id_user']]);
    }else{
        // Wrong Password
        echo json_encode(['status' => 'error', 'message' => 'Wrong password']);
    }
}else{
    // Email not registered
    echo json_encode(['status' => 'error', 'message' => 'Email not registered']);
}

?>
