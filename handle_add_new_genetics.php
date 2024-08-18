<?php require_once 'auth.php'; ?>
<?php
require_once 'config.php'; // Include your database configuration

// Create connection using the variables from config.php
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $geneticsName = $_POST['geneticsName'];
    $breeder = $_POST['breeder']; // Optional, might be empty
    $geneticLineage = $_POST['geneticLineage']; // Optional, might be empty

    // Basic input validation (you can add more checks as needed)
    if (empty($geneticsName)) {
        // Redirect with error message and submitted data
        $data = json_encode($_POST); 
        header("Location: add_new_genetics.php?error=Genetics name is required&data=$data");
        exit();
    }

    // Sanitize input data (prevent SQL injection)
    $geneticsName = $conn->real_escape_string($geneticsName);
    $breeder = $conn->real_escape_string($breeder);
    $geneticLineage = $conn->real_escape_string($geneticLineage);

    // Prepare and execute SQL query to insert data
    $sql = "INSERT INTO Genetics (name, breeder, genetic_lineage) 
            VALUES ('$geneticsName', '$breeder', '$geneticLineage')";

    if ($conn->query($sql) === TRUE) {
        // Redirect with success message
        header("Location: add_new_genetics.php?success=New genetics added successfully");
        exit();
    } else {
        // Redirect with error message and submitted data
        $data = json_encode($_POST); // Encode submitted data as JSON
        header("Location: add_new_genetics.php?error=Error adding genetics&data=$data");
        exit();
    }
}

// Close the connection at the end of the script
$conn->close();
?>
