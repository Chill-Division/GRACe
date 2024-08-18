<?php
require_once 'config.php';
require_once 'auth.php';

header('Content-Type: application/json');

// Function to send JSON response
function sendJsonResponse($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(false, 'You need to be logged in to perform this action');
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    sendJsonResponse(false, 'Connection failed: ' . $conn->connect_error);
}

// Check if the request is an AJAX POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Get the JSON data from the request body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(false, 'Invalid JSON data received');
    }
    
    // Get the username to delete from the AJAX request
    $usernameToDelete = $data['username'] ?? '';

    if (empty($usernameToDelete)) {
        sendJsonResponse(false, 'No username provided for deletion');
    }

    // Prevent self-deletion 
    $currentUserQuery = "SELECT username FROM Users WHERE id = " . $_SESSION['user_id'];
    $currentUserResult = $conn->query($currentUserQuery);
    $currentUsername = $currentUserResult->fetch_assoc()['username'];

    if ($usernameToDelete == $currentUsername) {
        sendJsonResponse(false, 'You cannot delete your own account');
    }

    // Sanitize input data 
    $usernameToDelete = $conn->real_escape_string($usernameToDelete);

    // Delete the user from the database based on username
    $sql = "DELETE FROM Users WHERE username = '$usernameToDelete'";

    if ($conn->query($sql) === TRUE) {
        sendJsonResponse(true, 'User deleted successfully');
    } else {
        sendJsonResponse(false, 'Error deleting user: ' . $conn->error);
    }
} else {
    sendJsonResponse(false, 'Invalid request method');
}

$conn->close();
?>
