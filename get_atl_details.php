<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            throw new Exception("No record ID provided");
        }

        $sql = "SELECT * FROM atl_details WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }

        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Database execute error: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $record = $result->fetch_assoc();

        if (!$record) {
            throw new Exception("Record not found");
        }

        // Success response
        echo json_encode([
            "status" => "success",
            "data" => $record
        ]);

    } catch (Exception $e) {
        // Error response
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    } finally {
        // Close connections
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    // Not a GET request
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Only GET requests are allowed."
    ]);
} 