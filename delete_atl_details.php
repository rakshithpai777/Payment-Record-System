<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;

    if ($id) {
        try {
            $sql = "DELETE FROM atl_details WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("Database prepare error: " . $conn->error);
            }

            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) {
                throw new Exception("Database execute error: " . $stmt->error);
            }

            echo json_encode([
                "status" => "success",
                "message" => "Record deleted successfully."
            ]);

        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Error deleting record: " . $e->getMessage()
            ]);
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No ID provided for deletion."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>