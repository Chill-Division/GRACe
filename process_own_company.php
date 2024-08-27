<?php
require_once 'auth.php';
require_once 'config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and validate input (add more validation as needed)
    $companyName = filter_input(INPUT_POST, 'companyName', FILTER_SANITIZE_STRING);
    $companyLicense = filter_input(INPUT_POST, 'companyLicense', FILTER_SANITIZE_STRING);
    $companyAddress = filter_input(INPUT_POST, 'companyAddress', FILTER_SANITIZE_STRING);
    $primaryContactEmail = filter_input(INPUT_POST, 'primaryContactEmail', FILTER_SANITIZE_EMAIL);

    // Basic validation (you can add more robust validation here)
    if (empty($companyName) || empty($companyLicense) || empty($companyAddress) || empty($primaryContactEmail) || !filter_var($primaryContactEmail, FILTER_VALIDATE_EMAIL)) {
        // Handle validation errors (e.g., display error messages, redirect back to the form)
        echo "Invalid input. Please check your data and try again.";
        exit;
    }

    // Get the currently logged-in user's ID from the session
    $userId = $_SESSION['user_id'];

    // Establish database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if a record already exists for this user
    $checkSql = "SELECT id FROM OwnCompany WHERE user_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update existing record
        $updateSql = "UPDATE OwnCompany SET
                        company_name = ?,
                        company_license_number = ?,
                        company_address = ?,
                        primary_contact_email = ?
                        WHERE user_id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssssi", $companyName, $companyLicense, $companyAddress, $primaryContactEmail, $userId);

        if ($stmt->execute()) {
            echo "Company information updated successfully!";
        } else {
            echo "Error updating company information: " . $stmt->error;
        }

    } else {
        // Insert new record
        $insertSql = "INSERT INTO OwnCompany (user_id, company_name, company_license_number, company_address, primary_contact_email)
                        VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("issss", $userId, $companyName, $companyLicense, $companyAddress, $primaryContactEmail);

        if ($stmt->execute()) {
            echo "Company information saved successfully!";
        } else {
            echo "Error saving company information: " . $stmt->error;
        }
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the own_company.php page
    header("Location: own_company.php");
    exit;
}
?>
