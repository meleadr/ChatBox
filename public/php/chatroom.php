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

// List of users
$users = $json_obj->users;

try {
    // Begin a transaction
    $db->beginTransaction();

    // Create a new chatroom
    $stmt = $db->prepare("INSERT INTO chatrooms (name) VALUES (:name)");
	$stmt->bindParam(':name', $json_obj->name);
    $stmt->execute();

    // Get the ID of the chatroom that was just created
    $chatroomId = $db->lastInsertId();

    // Add each user to the chatroom
    $stmt = $db->prepare("INSERT INTO chatroom_access (id_chatroom, id_user) VALUES (:chatroomId, :userId)");
    foreach($users as $userId) {
        $stmt->execute([
            'chatroomId' => $chatroomId,
            'userId' => $userId
        ]);
    }

    // Commit the transaction
    $db->commit();

    // Send back the chatroom ID
    echo json_encode(['status' => 'success', 'chatroomId' => $chatroomId]);

} catch(PDOException $e) {
    // If something went wrong, rollback the transaction
    $db->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
