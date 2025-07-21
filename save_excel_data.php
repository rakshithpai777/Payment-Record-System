<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('No data received');
    }

    // Start transaction
    $conn->begin_transaction();

    // Update 2L table
    if (isset($data['data_2l'])) {
        $stmt_2l = $conn->prepare("UPDATE data_2L SET 
            balance_start = ?,
            interest = ?,
            int_paid = ?,
            grant_amount = ?,
            total = ?,
            expenditure = ?,
            balance_end = ?
            WHERE year = ?");

        foreach ($data['data_2l'] as $row) {
            $stmt_2l->bind_param("ddddddds",
                $row['balance_start'],
                $row['interest'],
                $row['int_paid'],
                $row['grant_amount'],
                $row['total'],
                $row['expenditure'],
                $row['balance_end'],
                $row['year']
            );
            $stmt_2l->execute();
        }
    }

    // Update 10L table
    if (isset($data['data_10l'])) {
        $stmt_10l = $conn->prepare("UPDATE data_10L SET 
            balance_start = ?,
            interest = ?,
            int_paid = ?,
            grant_amount = ?,
            total = ?,
            expenditure = ?,
            balance_end = ?
            WHERE year = ?");

        foreach ($data['data_10l'] as $row) {
            $stmt_10l->bind_param("ddddddds",
                $row['balance_start'],
                $row['interest'],
                $row['int_paid'],
                $row['grant_amount'],
                $row['total'],
                $row['expenditure'],
                $row['balance_end'],
                $row['year']
            );
            $stmt_10l->execute();
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Data saved successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollback();
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 