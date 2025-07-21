<?php
require_once 'db_connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Log raw input for debugging
file_put_contents('php_debug.log', "\n---
" . date('Y-m-d H:i:s') . " - Received JSON Input: " . $json . "\n", FILE_APPEND);

// Validate input
if (!isset($data['table_type']) || !isset($data['data']) || !is_array($data['data'])) {
    $errorMessage = 'Invalid input data';
    file_put_contents('php_debug.log', date('Y-m-d H:i:s') . " - Error: " . $errorMessage . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    exit;
}

$table_type = $data['table_type'];
$expenditure_data = $data['data'];

file_put_contents('php_debug.log', date('Y-m-d H:i:s') . " - Table Type: " . $table_type . "\n", FILE_APPEND);
file_put_contents('php_debug.log', date('Y-m-d H:i:s') . " - Data to process: " . json_encode($expenditure_data) . "\n", FILE_APPEND);

try {
    // Begin transaction
    $conn->begin_transaction();

    // Update each row in the expenditure table
    foreach ($expenditure_data as $row) {
        $year = $row['year'];
        $balance = $row['balance'];
        $interest = $row['interest'];
        $int_paid = $row['int_paid'];
        $grant = $row['grant'];
        $total = $row['total'];
        $expenditure = $row['expenditure'];
        $new_balance = $row['new_balance'];

        // Update the expenditure table
        // Note: The 'recurring_type' column should ideally be passed in the incoming data
        // and potentially matched to 'non_recurring' or 'recurring' from the JS side.
        // For now, we use $table_type from the input which is 'non_recurring' or 'recurring'
        $sql = "UPDATE expenditure 
                SET balance = ?, 
                    interest = ?, 
                    int_paid = ?, 
                    grant = ?, 
                    total = ?, 
                    expenditure = ?, 
                    new_balance = ? 
                WHERE year = ? AND recurring_type = ?";
        
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Log the parameters being bound
        file_put_contents('php_debug.log', date('Y-m-d H:i:s') . " - Binding params for year $year ($table_type): " .
            "balance=$balance, interest=$interest, int_paid=$int_paid, grant=$grant, total=$total, expenditure=$expenditure, new_balance=$new_balance\n", FILE_APPEND);

        $stmt->bind_param("ddddddds", 
            $balance, 
            $interest, 
            $int_paid, 
            $grant, 
            $total, 
            $expenditure, 
            $new_balance, 
            $year,
            $table_type
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating row for year $year: " . $stmt->error);
        }
        file_put_contents('php_debug.log', date('Y-m-d H:i:s') . " - Row updated successfully for year $year\n", FILE_APPEND);
    }

    // Commit transaction
    $conn->commit();
    file_put_contents('php_debug.log', date('Y-m-d H:i:s') . " - Transaction committed successfully.\n", FILE_APPEND);
    echo json_encode(['status' => 'success', 'message' => 'Data updated successfully']);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $errorMessage = $e->getMessage();
    file_put_contents('php_debug.log', date('Y-m-d H:i:s') . " - Database Error: " . $errorMessage . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
}

$conn->close();
?> 