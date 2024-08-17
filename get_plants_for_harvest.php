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

    // Fetch plants data with genetics name, age, and status (filter for 'Growing' status)
    $sql = "SELECT 
                P.id,
                G.name AS geneticsName, 
                DATEDIFF(CURDATE(), P.date_created) AS age,
                P.status 
            FROM 
                Plants P 
            JOIN 
                Genetics G ON P.genetics_id = G.id 
            WHERE 
                P.status = 'Growing' 
            ORDER BY 
                age ASC"; 

    $stmt = $pdo->query($sql);
    $plantsData = $stmt->fetchAll();

    // Send data as JSON
    header('Content-Type: application/json');
    echo json_encode($plantsData);
} catch (PDOException $e) {
    // Handle errors gracefully
    http_response_code(500); 
    echo json_encode(['error' => 'Database error']);
}
?>
