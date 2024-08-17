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
    $geneticsId = $_POST['geneticsName'];
    $weight = $_POST['weight'];
    $transactionType = $_POST['transactionType'];
    $reason = $_POST['reason'];
    $otherReason = isset($_POST['otherReason']) ? $_POST['otherReason'] : null;
    $companyId = isset($_POST['companyId']) ? $_POST['companyId'] : null;

    // Basic input validation
    if (empty($geneticsId) || empty($weight) || empty($transactionType) || empty($reason)) {
        header("Location: record_dry_weight.html?error=All fields are required");
        exit();
    }

    // If the reason is 'Other', ensure 'otherReason' is provided
    if ($reason === 'Other' && empty($otherReason)) {
        header("Location: record_dry_weight.html?error=Please provide the 'Other' reason");
        exit();
    }

    // Validate company selection for 'Testing' and 'Send external' reasons
    if ($transactionType === 'Subtract' && ($reason === 'Testing' || $reason === 'Send external') && empty($companyId)) {
        header("Location: record_dry_weight.html?error=Please select a company for Testing or Send external transactions");
        exit();
    }

    // Sanitize input data
    $geneticsId = $conn->real_escape_string($geneticsId);
    $reason = $conn->real_escape_string($reason);
    $otherReason = $conn->real_escape_string($otherReason);
    $companyId = $companyId ? $conn->real_escape_string($companyId) : null;

    // Adjust weight based on transaction type
    if ($transactionType === 'Subtract') {
        $weight = -$weight;
    }

    // Use the appropriate reason based on the selection
    $finalReason = ($reason === 'Other') ? $otherReason : $reason;

    // Insert into Flower table
    $sql = "INSERT INTO Flower (genetics_id, weight, transaction_type, transaction_date, reason, company_id) 
            VALUES (?, ?, ?, NOW(), ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idsss", $geneticsId, $weight, $transactionType, $finalReason, $companyId);

    if ($stmt->execute()) {
        header("Location: record_dry_weight.html?success=Flower transaction recorded successfully");
    } else {
        header("Location: record_dry_weight.html?error=Error recording transaction: " . $conn->error);
    }

    $stmt->close();
}

$conn->close();
?>
