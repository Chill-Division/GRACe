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

    // Fetch all genetics
    $geneticsStmt = $pdo->query("SELECT id, name FROM Genetics");
    $genetics = $geneticsStmt->fetchAll();

    $plantData = [];

    foreach ($genetics as $genetic) {
        // Count the number of plants with 'Growing' status for each genetic
        $sql = "SELECT COUNT(*) AS plantCount 
                FROM Plants 
                WHERE genetics_id = :geneticsId 
                AND status = 'Growing'";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':geneticsId', $genetic['id'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        $plantData[] = [
            'geneticsName' => $genetic['name'],
            'plantCount' => $result['plantCount']
        ];
    }

    // Send data as JSON
    header('Content-Type: application/json');
    echo json_encode($plantData);
} catch (PDOException $e) {
    // Handle errors gracefully
    http_response_code(500); 
    echo json_encode(['error' => 'Database error']);
}
?>
