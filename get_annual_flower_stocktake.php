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

    // Get the selected year from the query parameters
    $selectedYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y') - 1; // Default to previous year if not provided

    // Calculate the start and end dates for the selected year
    $startDate = $selectedYear . '-01-01';
    $endDate = $selectedYear . '-12-31';

    // Fetch all genetics
    $geneticsStmt = $pdo->query("SELECT id, name FROM Genetics");
    $genetics = $geneticsStmt->fetchAll();

    $flowerStocktakeData = [];

    foreach ($genetics as $genetic) {
        // Calculate start weight (flower weight existing at the beginning of the year)
        $startWeightSql = "SELECT SUM(weight) AS startWeight 
                           FROM Flower 
                           WHERE genetics_id = :geneticsId 
                           AND transaction_date < :startDate";
        $startWeightStmt = $pdo->prepare($startWeightSql);
        $startWeightStmt->bindParam(':geneticsId', $genetic['id'], PDO::PARAM_INT);
        $startWeightStmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $startWeightStmt->execute();
        $startWeightResult = $startWeightStmt->fetch();
        $startWeight = $startWeightResult['startWeight'] ? $startWeightResult['startWeight'] : 0;

        // Calculate 'In' (flower weight added during the year)
        $inSql = "SELECT SUM(weight) AS inWeight 
                  FROM Flower 
                  WHERE genetics_id = :geneticsId 
                  AND transaction_type = 'Add'
                  AND transaction_date BETWEEN :startDate AND :endDate";
        $inStmt = $pdo->prepare($inSql);
        $inStmt->bindParam(':geneticsId', $genetic['id'], PDO::PARAM_INT);
        $inStmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $inStmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $inStmt->execute();
        $inResult = $inStmt->fetch();
        $inWeight = $inResult['inWeight'] ? $inResult['inWeight'] : 0;

        // Calculate 'Out' (flower weight subtracted during the year)
        $outSql = "SELECT SUM(weight) AS outWeight 
                   FROM Flower 
                   WHERE genetics_id = :geneticsId 
                   AND transaction_type = 'Subtract'
                   AND transaction_date BETWEEN :startDate AND :endDate";
        $outStmt = $pdo->prepare($outSql);
        $outStmt->bindParam(':geneticsId', $genetic['id'], PDO::PARAM_INT);
        $outStmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $outStmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $outStmt->execute();
        $outResult = $outStmt->fetch();
        $outWeight = $outResult['outWeight'] ? abs($outResult['outWeight']) : 0; // Use absolute value for subtracted weight

        // Calculate 'Destroyed' (flower weight destroyed during the year)
        $destroyedSql = "SELECT SUM(weight) AS destroyedWeight 
                         FROM Flower 
                         WHERE genetics_id = :geneticsId 
                         AND transaction_type = 'Subtract'
                         AND reason = 'Destroy'
                         AND transaction_date BETWEEN :startDate AND :endDate";
        $destroyedStmt = $pdo->prepare($destroyedSql);
        $destroyedStmt->bindParam(':geneticsId', $genetic['id'], PDO::PARAM_INT);
        $destroyedStmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $destroyedStmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $destroyedStmt->execute();
        $destroyedResult = $destroyedStmt->fetch();
        $destroyedWeight = $destroyedResult['destroyedWeight'] ? abs($destroyedResult['destroyedWeight']) : 0;

        // Calculate 'End' (flower weight remaining at the end of the year)
        $endWeight = $startWeight + $inWeight - $outWeight - $destroyedWeight;

    // Only add the row if at least one value is non-zero
    if ($startWeight > 0 || $inWeight > 0 || $outWeight > 0 || $destroyedWeight > 0 || $endWeight > 0) {
        $flowerStocktakeData[] = [
            'geneticsName' => $genetic['name'],
            'startWeight' => intval($startWeight),
            'in' => intval($inWeight),
            'out' => intval($outWeight),
            'destroyed' => intval($destroyedWeight),
            'end' => intval($endWeight)
        ];
    }
}
    // Send data as JSON
    header('Content-Type: application/json');
    echo json_encode($flowerStocktakeData);
} catch (PDOException $e) {
    // Handle errors gracefully
    http_response_code(500); 
    echo json_encode(['error' => 'Database error']);
}
?>
