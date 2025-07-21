<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    // Debug: Check payment records
    $debug_sql = "SELECT date, amount, type, recurring_type FROM payments WHERE type = 'Credit' ORDER BY date";
    $debug_result = $conn->query($debug_sql);
    error_log("Credit payments found: " . $debug_result->num_rows);
    while ($row = $debug_result->fetch_assoc()) {
        error_log("Credit payment: Date=" . $row['date'] . ", Amount=" . $row['amount'] . ", Type=" . $row['type'] . ", Recurring=" . $row['recurring_type']);
    }

    // Function to get expenditure data for a specific recurring type
    function getExpenditureData($conn, $recurringType) {
        // Debug log
        error_log("Processing data for recurring type: " . $recurringType);
        
        $sql = "SELECT 
                    YEAR(date) as year,
                    SUM(CASE WHEN type = 'Credit' THEN amount ELSE 0 END) as grant_amount,
                    SUM(CASE WHEN type = 'Debit' THEN amount ELSE 0 END) as expenditure,
                    SUM(CASE WHEN type = 'Interest' THEN amount ELSE 0 END) as interest,
                    SUM(CASE WHEN type = 'Charges' THEN amount ELSE 0 END) as charges
                FROM payments 
                WHERE recurring_type = ?
                GROUP BY YEAR(date)
                ORDER BY year ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $recurringType);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        $previous_balance = 0;
        
        while ($row = $result->fetch_assoc()) {
            $year = $row['year'];
            $grant = floatval($row['grant_amount']);
            $expenditure = floatval($row['expenditure']);
            $interest = floatval($row['interest']);
            $charges = floatval($row['charges']);
            
            // Debug log
            error_log("Year: $year, Grant: $grant, Expenditure: $expenditure, Interest: $interest, Charges: $charges");
            
            // Calculate totals and balance
            $total = $previous_balance + $grant + $interest - $charges;
            $balance = $total - $expenditure;
            
            $data[] = [
                'year' => $year,
                'balance' => $previous_balance,
                'interest' => $interest,
                'int_paid' => $charges,
                'grant' => $grant,
                'total' => $total,
                'expenditure' => $expenditure,
                'new_balance' => $balance
            ];
            
            $previous_balance = $balance;
            
            // Debug log
            error_log("Calculated - Total: $total, New Balance: $balance");
        }
        
        return $data;
    }

    // Get data for both recurring types
    $nonRecurringData = getExpenditureData($conn, 'Non-Recurring');
    $recurringData = getExpenditureData($conn, 'Recurring');
    
    // Debug log
    error_log("Non-Recurring Data: " . json_encode($nonRecurringData));
    error_log("Recurring Data: " . json_encode($recurringData));
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'non_recurring' => $nonRecurringData,
            'recurring' => $recurringData
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_expenditure_data.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 