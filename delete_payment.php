<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No record ID provided'
    ]);
    exit;
}

$id = $_POST['id'];

try {
    // First, get the file path if it exists
    $stmt = $conn->prepare("SELECT file_name FROM payments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    
    // Delete the record
    $stmt = $conn->prepare("DELETE FROM payments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        // If there was a file, delete it
        if ($record && $record['file_name']) {
            $file_path = 'uploads/' . $record['file_name'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Record deleted successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Record not found or already deleted'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 