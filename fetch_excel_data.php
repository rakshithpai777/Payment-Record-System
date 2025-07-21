<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    // Fetch data from 2L table
    $sql_2l = "SELECT 
        year,
        balance_start,
        interest,
        int_paid,
        grant_amount,
        total,
        expenditure,
        balance_end
    FROM data_2L
    ORDER BY year";

    $result_2l = $conn->query($sql_2l);
    $data_2l = array();
    while ($row = $result_2l->fetch_assoc()) {
        $data_2l[] = $row;
    }

    // Fetch data from 10L table
    $sql_10l = "SELECT 
        year,
        balance_start,
        interest,
        int_paid,
        grant_amount,
        total,
        expenditure,
        balance_end
    FROM data_10L
    ORDER BY year";

    $result_10l = $conn->query($sql_10l);
    $data_10l = array();
    while ($row = $result_10l->fetch_assoc()) {
        $data_10l[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'data_2l' => $data_2l,
        'data_10l' => $data_10l
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 