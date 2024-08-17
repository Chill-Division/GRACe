<?php
require_once 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $plantCount = $_POST['plantCount'];
    $geneticsId = $_POST['geneticsName'];

    // Basic input validation 
    if (empty($plantCount) || empty($geneticsId)) {
        // Redirect with error message and submitted data
        $data = json_encode($_POST);
        header("Location: receive_genetics.html?error=Plant count and genetics name are required&data=$data");
        exit();
    }

    // Sanitize input data (prevent SQL injection)
    $geneticsId = $conn->real_escape_string($geneticsId);

    // Insert into Plants table directly (no GrowSeasons insertion)
    for ($i = 0; $i < $plantCount; $i++) {
        $sql = "INSERT INTO Plants (genetics_id, status, date_created) 
                VALUES ('$geneticsId', 'Growing', NOW())";

        if (!$conn->query($sql)) {
            // Handle Plants insertion error
            $data = json_encode($_POST);
            header("Location: receive_genetics.html?error=Error inserting plants: " . $conn->error . "&data=$data");
            exit();
        }
    }

    // Redirect with success message
    header("Location: receive_genetics.html?success=Genetics received successfully");
    exit();
}

$conn->close();
?>
