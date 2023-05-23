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

$stmt = $db->prepare("SELECT id_user, mail FROM users WHERE mail != :email");
$stmt->bindParam(':email', $_GET['email']);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'users' => $users]);
?>
