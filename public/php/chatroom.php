<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

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

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str);

$users = $json_obj->users;
sort($users);

try {
	$userCount = count($users);
    $stmt = $db->prepare("SELECT id_chatroom, COUNT(id_user) as user_count FROM chatroom_access WHERE id_user IN (".implode(',', $users).") GROUP BY id_chatroom HAVING user_count = :userCount");
    $stmt->bindParam(':userCount', $userCount);
    $stmt->execute();

    $existingChatrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $chatroomId = null;

    foreach ($existingChatrooms as $chatroom) {
        $stmt = $db->prepare("SELECT id_user FROM chatroom_access WHERE id_chatroom = :chatroomId");
        $stmt->bindParam(':chatroomId', $chatroom['id_chatroom']);
        $stmt->execute();
        
        $chatroomUsers = $stmt->fetchAll(PDO::FETCH_COLUMN);
        sort($chatroomUsers);
        
        if ($users == $chatroomUsers) {
            $chatroomId = $chatroom['id_chatroom'];
            break;
        }
    }

    if (!$chatroomId) {
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO chatrooms (name) VALUES (:name)");
        $stmt->bindParam(':name', $json_obj->name);
        $stmt->execute();
        $chatroomId = $db->lastInsertId();
        $stmt = $db->prepare("INSERT INTO chatroom_access (id_chatroom, id_user) VALUES (:chatroomId, :userId)");
        
        foreach($users as $userId) {
            $stmt->execute([
                'chatroomId' => $chatroomId,
                'userId' => $userId
            ]);
        }

        $db->commit();
    }

    echo json_encode(['status' => 'success', 'chatroomId' => $chatroomId]);
} catch(PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
