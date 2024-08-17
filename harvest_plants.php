<?php
require_once 'config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set the content type to JSON
header('Content-Type: application/json');

// Start output buffering
ob_start();

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Check if it's a POST request
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the JSON data from the request body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Log the received data (for debugging)
        error_log('Received data: ' . print_r($data, true));

        // Check if selectedPlants and action exist in the data
        if (!isset($data['selectedPlants']) || !is_array($data['selectedPlants']) || !isset($data['action'])) {
            throw new Exception('Invalid or missing data');
        }

        $selectedPlantIds = $data['selectedPlants'];
        $action = $data['action'];

        // Basic input validation
        if (empty($selectedPlantIds)) {
            throw new Exception('No plants selected.');
        }

        // Sanitize input data (prevent SQL injection)
        $selectedPlantIds = array_map(function($id) use ($conn) {
            return $conn->real_escape_string($id);
        }, $selectedPlantIds);

        // Set the status based on the action
        $newStatus = ($action === 'harvest') ? 'Harvested' : 'Destroyed';

        // Update the status and date_harvested for the selected plants
        $sql = "UPDATE Plants 
                SET status = '$newStatus', date_harvested = NOW() 
                WHERE id IN (" . implode(',', $selectedPlantIds) . ")";

        // Log the SQL query (for debugging)
        error_log('SQL Query: ' . $sql);

        if ($conn->query($sql) === TRUE) {
            $affectedRows = $conn->affected_rows;
            echo json_encode(['success' => true, 'message' => "Success: $affectedRows plants $action" . 'ed successfully']);
        } else {
            throw new Exception("Error updating database: " . $conn->error);
        }

    } else {
        throw new Exception('Invalid request method');
    }

} catch (Exception $e) {
    // Log the error
    error_log('Error in harvest_plants.php: ' . $e->getMessage());
    
    // Send a JSON error response
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    // Close the database connection if it was opened
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}

// End output buffering and flush
ob_end_flush();
?>
