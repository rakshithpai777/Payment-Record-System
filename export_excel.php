<?php
require_once 'db_connect.php';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="payments_export.xls"');
header('Cache-Control: max-age=0');

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build the query
$query = "SELECT p.*, c.name as category_name 
          FROM payments p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1=1";
$params = array();

if (!empty($category)) {
    $query .= " AND p.category_id = ?";
    $params[] = $category;
}

if (!empty($date_from)) {
    $query .= " AND p.payment_date >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $query .= " AND p.payment_date <= ?";
    $params[] = $date_to;
}

$query .= " ORDER BY p.payment_date DESC";

// Execute query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$result = $stmt->get_result();

// Output Excel content
echo '<table border="1">';
echo '<tr>
        <th>Date</th>
        <th>Category</th>
        <th>Amount</th>
        <th>Description</th>
        <th>Payment Method</th>
        <th>Status</th>
      </tr>';

while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . date('d-m-Y', strtotime($row['payment_date'])) . '</td>';
    echo '<td>' . htmlspecialchars($row['category_name']) . '</td>';
    echo '<td>' . number_format($row['amount'], 2) . '</td>';
    echo '<td>' . htmlspecialchars($row['description']) . '</td>';
    echo '<td>' . htmlspecialchars($row['payment_method']) . '</td>';
    echo '<td>' . htmlspecialchars($row['status']) . '</td>';
    echo '</tr>';
}

echo '</table>';
?> 