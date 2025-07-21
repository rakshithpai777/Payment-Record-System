<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No record ID provided'
    ]);
    exit;
}

$id = $_GET['id'];

try {
    // Debug connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    $stmt = $conn->prepare("SELECT * FROM payments WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Record not found'
        ]);
        exit;
    }
    
    $record = $result->fetch_assoc();
    
    echo json_encode([
        'status' => 'success',
        'data' => $record
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_payment_details.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 