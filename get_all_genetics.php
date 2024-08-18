<?php require_once 'auth.php'; ?>
<?php
require_once 'config.php';

try {
    // Create PDO connection
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);

    // Check if status filter is provided
    $statusFilter = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : null;

    // Build the SQL query with optional status filter
    $sql = "SELECT 
                G.name AS geneticsName, 
                DATEDIFF(CURDATE(), P.date_created) AS age,
                P.status 
            FROM 
                Plants P 
            JOIN 
                Genetics G ON P.genetics_id = G.id ";

    if ($statusFilter) {
        $sql .= "WHERE P.status = :status ";
    }

    $sql .= "ORDER BY age ASC"; 

    $stmt = $pdo->prepare($sql); // Prepare the statement

    if ($statusFilter) {
        $stmt->bindParam(':status', $statusFilter, PDO::PARAM_STR);
    }

    $stmt->execute(); 
    $geneticsData = $stmt->fetchAll();

    // Send data as JSON
    header('Content-Type: application/json');
    echo json_encode($geneticsData);
} catch (PDOException $e) {
    // Handle errors gracefully
    http_response_code(500); 
    echo json_encode(['error' => 'Database error']);
}
?>
