<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$data = [];

try {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Fetch single record
        $sql = "SELECT id, school_name, sanction_no, udise, fund_date, inauguration_date, principal_name_phone, atl_code, atl_pswd, gem_code, govt_email, pfms, pfms_op, pfms_ap, atl_incharge_phone, dashbrd_pswd, gem_pswd, govt_mail_pswd, pfms_pswd, op_pswd, ap_pswd, reg_email, reg_phone, bharatkosh, bkosh_password, atl_vendor, vcomment, created_at FROM atl_details WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $data[] = $result->fetch_assoc(); // Fetch only one row
        } else {
            throw new Exception("Error fetching data for ID: " . $conn->error);
        }
        $stmt->close();
    } else {
        // Fetch all records
        $sql = "SELECT id, school_name, sanction_no, udise, fund_date, inauguration_date, principal_name_phone, atl_code, atl_pswd, gem_code, govt_email, pfms, pfms_op, pfms_ap, atl_incharge_phone, dashbrd_pswd, gem_pswd, govt_mail_pswd, pfms_pswd, op_pswd, ap_pswd, reg_email, reg_phone, bharatkosh, bkosh_password, atl_vendor, vcomment, created_at FROM atl_details ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        } else {
            throw new Exception("Error fetching data: " . $conn->error);
        }
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode([
    'status' => 'success',
    'data' => $data
]);
?>