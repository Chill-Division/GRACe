<?php require_once 'auth.php'; ?>
<?php
require_once 'config.php';
session_start(); // Start the session

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $firstName = $_POST['firstName'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Basic input validation 
    if (empty($firstName) || empty($surname) || empty($username) || empty($password)) {
        // Redirect with error message and submitted data
        $data = json_encode($_POST);
        header("Location: add_new_user.php?error=All fields are required&data=$data");
        exit();
    }

    // Sanitize input data (prevent SQL injection)
    $firstName = $conn->real_escape_string($firstName);
    $surname = $conn->real_escape_string($surname);
    $username = $conn->real_escape_string($username);

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute SQL query to insert data
    $sql = "INSERT INTO Users (first_name, surname, username, password) 
            VALUES ('$firstName', '$surname', '$username', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        // Redirect with success message
        header("Location: add_new_user.php?success=New user added successfully");
        exit();
    } else {
        // Handle database insertion error (e.g., duplicate username)
        $data = json_encode($_POST);
        header("Location: add_new_user.php?error=Error adding user: " . $conn->error . "&data=$data");
        exit();
    }
}

$conn->close();
?>
