<?php
require_once 'db_connect.php';

// Function to format date from database to dd-mm-yyyy
function formatDate($date) {
    return date('d-m-Y', strtotime($date));
}

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$vendor = isset($_GET['vendor']) ? $_GET['vendor'] : '';

// Build the query
$sql = "SELECT * FROM payments WHERE 1=1";
$params = array();
$types = "";

if (!empty($category)) {
    $sql .= " AND category LIKE ?";
    $params[] = "%$category%";
    $types .= "s";
}

if (!empty($date_from)) {
    $sql .= " AND date >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $sql .= " AND date <= ?";
    $params[] = $date_to;
    $types .= "s";
}

if (!empty($vendor)) {
    $sql .= " AND vendor LIKE ?";
    $params[] = "%$vendor%";
    $types .= "s";
}

$sql .= " ORDER BY date DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Payments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .filters {
            margin-bottom: 20px;
            padding: 15px;
            background: #f1f1f1;
            border-radius: 5px;
        }
        .filters input, .filters select {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .search-btn {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-btn:hover {
            background-color: #0056b3;
        }
        .excel-btn {
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        .excel-btn:hover {
            background-color: #218838;
        }
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            margin-right: 5px;
        }
        .edit-btn {
            background-color: #28a745;
        }
        .edit-btn:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Records</h2>
        
        <div class="filters">
            <form method="GET">
                <input type="text" name="category" placeholder="Category" value="<?php echo htmlspecialchars($category); ?>">
                <input type="date" name="date_from" placeholder="Date From" value="<?php echo htmlspecialchars($date_from); ?>">
                <input type="date" name="date_to" placeholder="Date To" value="<?php echo htmlspecialchars($date_to); ?>">
                <input type="text" name="vendor" placeholder="Vendor" value="<?php echo htmlspecialchars($vendor); ?>">
                <button type="submit" class="search-btn">Search</button>
                <button type="button" class="excel-btn" onclick="window.location.href = 'excel.html';">Generate UC</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Recurring Type</th>
                    <th>Slab</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Vendor</th>
                    <th>Type</th>
                    <th>Reference No</th>
                    <th>Balance</th>
                    <th>Comment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['recurring_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['slab']); ?></td>
                    <td><?php echo formatDate($row['date']); ?></td>
                    <td><?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['vendor']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['reference_no']); ?></td>
                    <td><?php echo number_format($row['balance'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['comment']); ?></td>
                    <td>
                        <a href="edit_payment.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        <a href="delete_payment.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>