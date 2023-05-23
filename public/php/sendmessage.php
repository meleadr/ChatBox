<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
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

$message = $json_obj->text;
$sender = $json_obj->sender; //assuming you pass the sender along with the message
$chatroomId = $json_obj->chatroomId; //assuming you pass the chatroom id along with the message

$stmt = $db->prepare("INSERT INTO messages (content, id_chatroom, id_user) VALUES (:message, :id_chatroom, :sender)");
$stmt->bindParam(':id_chatroom', $chatroomId);
$stmt->bindParam(':sender', $sender);
$stmt->bindParam(':message', $message);

$stmt->execute();

echo json_encode(['status' => 'success', 'message' => 'Message sent']);
?>
