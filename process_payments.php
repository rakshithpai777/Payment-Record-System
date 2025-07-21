<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

require_once 'db_connect.php';

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Log the received data
error_log("Received data: " . print_r($data, true));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Debug information array
        $debug_info = [];
        
        // Get form data with type casting
        $id = isset($data['id']) ? intval($data['id']) : null;
        $category = trim($data['category'] ?? '');
        $recurring_type = trim($data['recurring_type'] ?? 'Non-Recurring');
        $slab = trim($data['slab'] ?? '');
        $date = trim($data['date'] ?? date('Y-m-d'));
        $amount = floatval($data['amount'] ?? 0);
        $vendor = trim($data['vendor'] ?? '');
        $type = trim($data['type'] ?? 'Credit');
        $reference_no = trim($data['reference_no'] ?? '');
        $balance = floatval($data['balance'] ?? 0);
        $comment = trim($data['comment'] ?? '');

        // Add debug info
        $debug_info['processed_data'] = [
            'id' => $id,
            'category' => $category,
            'recurring_type' => $recurring_type,
            'slab' => $slab,
            'date' => $date,
            'amount' => $amount,
            'vendor' => $vendor,
            'type' => $type,
            'reference_no' => $reference_no,
            'balance' => $balance,
            'comment' => $comment
        ];

        // Validate required fields
        $missing_fields = [];
        if (empty($category)) $missing_fields[] = 'Category';
        if (empty($slab)) $missing_fields[] = 'Slab';
        if (empty($vendor)) $missing_fields[] = 'Vendor';
        if (empty($reference_no)) $missing_fields[] = 'Reference No';

        if (!empty($missing_fields)) {
            throw new Exception("Required fields cannot be empty: " . implode(', ', $missing_fields));
        }

        // Validate numeric fields
        if (!is_numeric($amount) || $amount < 0) {
            throw new Exception("Amount must be a positive number");
        }
        if (!is_numeric($balance)) {
            throw new Exception("Balance must be a number");
        }

        // Update existing record
        $sql = "UPDATE payments SET 
                category = ?, 
                recurring_type = ?, 
                slab = ?, 
                date = ?, 
                amount = ?, 
                vendor = ?, 
                type = ?, 
                reference_no = ?, 
                balance = ?, 
                comment = ? 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }

        $stmt->bind_param("ssssdsssdssi", 
            $category, 
            $recurring_type, 
            $slab, 
            $date, 
            $amount, 
            $vendor, 
            $type, 
            $reference_no, 
            $balance, 
            $comment,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Database execute error: " . $stmt->error);
        }

        // Get the updated record
        $select_sql = "SELECT * FROM payments WHERE id = ?";
        $select_stmt = $conn->prepare($select_sql);
        $select_stmt->bind_param("i", $id);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        $updated_record = $result->fetch_assoc();

        // Success response
        echo json_encode([
            "status" => "success",
            "message" => "Payment record updated successfully",
            "data" => $updated_record
        ]);

    } catch (Exception $e) {
        error_log("Error in process_payments.php: " . $e->getMessage());
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage(),
            "debug_info" => $debug_info ?? []
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Only POST requests are allowed."
    ]);
}