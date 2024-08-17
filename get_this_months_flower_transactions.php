<?php
require_once 'config.php';

// Enable error reporting for debugging (optional, remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Create PDO connection
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);

    // Calculate the start and end dates for this month
    $startDate = date('Y-m-01'); // First day of the current month
    $endDate = date('Y-m-t'); // Last day of the current month

    // Log the date range being queried
    error_log("Querying for dates between $startDate and $endDate");

    // Fetch flower transaction data for this month
    $sql = "SELECT 
                G.name AS geneticsName, 
                F.weight,
                F.transaction_date,
                F.reason,
                C.name AS companyName,
                C.address AS companyAddress 
            FROM 
                Flower F
            JOIN 
                Genetics G ON F.genetics_id = G.id
            LEFT JOIN 
                Companies C ON F.company_id = C.id 
            WHERE
                F.transaction_type = 'Subtract'
                AND F.reason IN ('Testing', 'Send external')
                AND F.transaction_date BETWEEN :startDate AND :endDate
            ORDER BY 
                F.transaction_date DESC"; 

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
    $stmt->execute();
    $transactionData = $stmt->fetchAll();

    // Data verification
    if (empty($transactionData)) {
        error_log("No data retrieved from the database for the date range $startDate to $endDate.");
        echo json_encode(['warning' => 'No data found for the given criteria']);
    } else {
        // Log the number of records retrieved
        error_log("Retrieved " . count($transactionData) . " records from the database.");

        // Send data as JSON
        echo json_encode($transactionData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    // Handle errors gracefully
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Handle any other unexpected errors
    error_log("Unexpected error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred']);
}

// Ensure no additional output
exit();
?>
