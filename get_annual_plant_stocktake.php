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

    $plantStocktakeData = [];

    foreach ($genetics as $genetic) {
        // Calculate start amount (plants existing at the beginning of the year)
        $startAmountSql = "SELECT COUNT(*) AS startAmount 
                           FROM Plants 
                           WHERE genetics_id = :geneticsId 
                           AND date_created < :startDate";
        $startAmountStmt = $pdo->prepare($startAmountSql);
        $startAmountStmt->bindParam(':geneticsId', $genetic['id'], PDO::PARAM_INT);
        $startAmountStmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $startAmountStmt->execute();
        $startAmountResult = $startAmountStmt->fetch();
        $startAmount = $startAmountResult['startAmount'] ? $startAmountResult['startAmount'] : 0;

        // Calculate 'In' (plants received during the year)
        $inSql = "SELECT COUNT(*) AS inCount 
                  FROM Plants 
                  WHERE genetics_id = :geneticsId 
                  AND date_created BETWEEN :startDate AND :endDate";
        $inStmt = $pdo->prepare($inSql);
        $inStmt->bindParam(':geneticsId', $genetic['id'], PDO::PARAM_INT);
        $inStmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $inStmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $inStmt->execute();
        $inResult = $inStmt->fetch();
        $inCount = $inResult['inCount'] ? $inResult['inCount'] : 0;

        // Calculate 'Out' (plants sent out during the year)
        $outSql = "SELECT COUNT(*) AS outCount 
                   FROM Plants 
                   WHERE genetics_id = :geneticsId 
                   AND status IN ('Sent', 'Harvested')
                   AND date_harvested BETWEEN :startDate AND :endDate"; // Assuming 'date_harvested' is used for sending out
        $outStmt = $pdo->prepare($outSql);
        $outStmt->bindParam(':geneticsId', $genetic['id'], PDO::PARAM_INT);
        $outStmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $outStmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $outStmt->execute();
        $outResult = $outStmt->fetch();
        $outCount = $outResult['outCount'] ? $outResult['outCount'] : 0;

        // Calculate 'Destroyed' (plants destroyed during the year)
        $destroyedSql = "SELECT COUNT(*) AS destroyedCount 
                         FROM Plants 
                         WHERE genetics_id = :geneticsId 
                         AND status = 'Destroyed' 
                         AND date_harvested BETWEEN :startDate AND :endDate"; // Assuming 'date_harvested' is also used for destroying
        $destroyedStmt = $pdo->prepare($destroyedSql);
        $destroyedStmt->bindParam(':geneticsId', $genetic['id'], PDO::PARAM_INT);
        $destroyedStmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $destroyedStmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $destroyedStmt->execute();
        $destroyedResult = $destroyedStmt->fetch();
        $destroyedCount = $destroyedResult['destroyedCount'] ? $destroyedResult['destroyedCount'] : 0;

        // Calculate 'End' (plants remaining at the end of the year)
        $endAmount = $startAmount + $inCount - $outCount - $destroyedCount;

        $plantStocktakeData[] = [
            'geneticsName' => $genetic['name'],
            'startAmount' => $startAmount,
            'in' => $inCount,
            'out' => $outCount,
            'destroyed' => $destroyedCount,
            'end' => $endAmount
        ];
    }

    // Send data as JSON
    header('Content-Type: application/json');
    echo json_encode($plantStocktakeData);
} catch (PDOException $e) {
    // Handle errors gracefully
    http_response_code(500); 
    echo json_encode(['error' => 'Database error']);
}
?>
