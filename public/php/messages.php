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
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Failed to connect to the database: " . $e->getMessage());
}

$chatroomId = $_GET['chatroomId'];

try {
    $stmt = $db->prepare("SELECT * FROM messages WHERE id_chatroom = :chatroomId");
    $stmt->bindParam(':chatroomId', $chatroomId);
    $stmt->execute();
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'messages' => $messages]);
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch messages: ' . $e->getMessage()]);
}
?>
