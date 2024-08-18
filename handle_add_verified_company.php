<?php require_once 'auth.php'; ?>
<?php
require_once 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") { // Opening brace for the 'if' block
    // Get data from the form
    $companyName = $_POST['companyName'];
    $licenseNumber = $_POST['licenseNumber'];
    $address = $_POST['address'];
    $contactName = $_POST['contactName'];
    $contactEmail = $_POST['contactEmail'];
    $contactPhone = $_POST['contactPhone'];

    // Basic input validation
    if (empty($companyName) || empty($licenseNumber) || empty($address) || empty($contactName) || empty($contactEmail) || empty($contactPhone)) {
        echo "Error: All fields are required.";
        exit();
    }

    // Sanitize input data (prevent SQL injection)
    $companyName = $conn->real_escape_string($companyName);
    $licenseNumber = $conn->real_escape_string($licenseNumber);
    $address = $conn->real_escape_string($address);
    $contactName = $conn->real_escape_string($contactName);
    $contactEmail = $conn->real_escape_string($contactEmail);
    $contactPhone = $conn->real_escape_string($contactPhone);

    // Prepare and execute SQL query to insert data
    $sql = "INSERT INTO Companies (name, license_number, address, primary_contact_name, primary_contact_email, primary_contact_phone)
            VALUES ('$companyName', '$licenseNumber', '$address', '$contactName', '$contactEmail', '$contactPhone')";

    if ($conn->query($sql) === TRUE) {
        echo "Success: New company added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} // Closing brace for the 'if' block - this was missing

$conn->close();
?>
