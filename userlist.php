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

try {
    $stmt = $db->prepare("SELECT mail FROM users");
    $stmt->execute();
    $usernames = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    header('Content-Type: application/json');
    echo json_encode($usernames);
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
