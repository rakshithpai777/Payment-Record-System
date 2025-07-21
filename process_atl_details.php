<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Debug information array
        $debug_info = [];
        
        // Log raw POST data
        $debug_info['raw_post'] = file_get_contents('php://input');
        $debug_info['$_POST'] = $_POST;
        
        // Get form data
        $id = $_POST['id'] ?? null; // Get ID for update, if present
        $school_name = trim($_POST['school_name'] ?? '');
        $sanction_no = trim($_POST['sanction_no'] ?? '');
        $udise = trim($_POST['udise'] ?? '');
        $fund_date = !empty($_POST['fund_date']) ? $_POST['fund_date'] : NULL;
        $inauguration_date = !empty($_POST['inauguration_date']) ? $_POST['inauguration_date'] : NULL;
        $principal_name_phone = trim($_POST['principal_name_phone'] ?? '');
        $atl_code = trim($_POST['atl_code'] ?? '');
        $atl_pswd = trim($_POST['atl_pswd'] ?? '');
        $gem_code = trim($_POST['gem_code'] ?? '');
        $govt_email = trim($_POST['govt_email'] ?? '');
        $pfms = trim($_POST['pfms'] ?? '');
        $pfms_op = trim($_POST['pfms_op'] ?? '');
        $pfms_ap = trim($_POST['pfms_ap'] ?? '');
        $atl_incharge_phone = trim($_POST['atl_incharge_phone'] ?? '');
        $dashbrd_pswd = trim($_POST['dashbrd_pswd'] ?? '');
        $gem_pswd = trim($_POST['gem_pswd'] ?? '');
        $govt_mail_pswd = trim($_POST['govt_mail_pswd'] ?? '');
        $pfms_pswd = trim($_POST['pfms_pswd'] ?? '');
        $op_pswd = trim($_POST['op_pswd'] ?? '');
        $ap_pswd = trim($_POST['ap_pswd'] ?? '');
        $reg_email = trim($_POST['reg_email'] ?? '');
        $reg_phone = trim($_POST['reg_phone'] ?? '');
        $bharatkosh = trim($_POST['bharatkosh'] ?? '');
        $bkosh_password = trim($_POST['bkosh_password'] ?? '');
        $atl_vendor = trim($_POST['atl_vendor'] ?? '');
        $vcomment = trim($_POST['vcomment'] ?? '');

        // Add debug info
        $debug_info['processed_data'] = [
            'id' => $id,
            'school_name' => $school_name,
            'sanction_no' => $sanction_no,
            'udise' => $udise,
            'fund_date' => $fund_date,
            'inauguration_date' => $inauguration_date,
            'principal_name_phone' => $principal_name_phone,
            'atl_code' => $atl_code,
            'atl_pswd' => $atl_pswd,
            'gem_code' => $gem_code,
            'govt_email' => $govt_email,
            'pfms' => $pfms,
            'pfms_op' => $pfms_op,
            'pfms_ap' => $pfms_ap,
            'atl_incharge_phone' => $atl_incharge_phone,
            'dashbrd_pswd' => $dashbrd_pswd,
            'gem_pswd' => $gem_pswd,
            'govt_mail_pswd' => $govt_mail_pswd,
            'pfms_pswd' => $pfms_pswd,
            'op_pswd' => $op_pswd,
            'ap_pswd' => $ap_pswd,
            'reg_email' => $reg_email,
            'reg_phone' => $reg_phone,
            'bharatkosh' => $bharatkosh,
            'bkosh_password' => $bkosh_password,
            'atl_vendor' => $atl_vendor,
            'vcomment' => $vcomment
        ];

        if ($id) {
            // Update existing record
            $sql = "UPDATE atl_details SET
                school_name = ?, sanction_no = ?, udise = ?, fund_date = ?, inauguration_date = ?,
                principal_name_phone = ?, atl_code = ?, atl_pswd = ?, gem_code = ?, govt_email = ?,
                pfms = ?, pfms_op = ?, pfms_ap = ?, atl_incharge_phone = ?, dashbrd_pswd = ?,
                gem_pswd = ?, govt_mail_pswd = ?, pfms_pswd = ?, op_pswd = ?, ap_pswd = ?,
                reg_email = ?, reg_phone = ?, bharatkosh = ?, bkosh_password = ?, atl_vendor = ?, vcomment = ?
                WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $debug_info['prepare_error'] = $conn->error;
                throw new Exception("Database prepare error: " . $conn->error);
            }

            $stmt->bind_param("ssssssssssssssssssssssssssi", 
                $school_name, $sanction_no, $udise, $fund_date, $inauguration_date,
                $principal_name_phone, $atl_code, $atl_pswd, $gem_code, $govt_email,
                $pfms, $pfms_op, $pfms_ap, $atl_incharge_phone, $dashbrd_pswd,
                $gem_pswd, $govt_mail_pswd, $pfms_pswd, $op_pswd, $ap_pswd,
                $reg_email, $reg_phone, $bharatkosh, $bkosh_password, $atl_vendor, $vcomment,
                $id
            );

            if (!$stmt->execute()) {
                $debug_info['execute_error'] = $stmt->error;
                throw new Exception("Database execute error: " . $stmt->error);
            }

            if ($stmt->affected_rows === 0) {
                throw new Exception("No record was updated. ID: " . $id . " may not exist.");
            }

            $message = "ATL details updated successfully!";

        } else {
            // Check for duplicates before inserting
            $check_sql = "SELECT id FROM atl_details WHERE atl_code = ? OR school_name = ?";
            $check_stmt = $conn->prepare($check_sql);
            if (!$check_stmt) {
                $debug_info['prepare_error'] = $conn->error;
                throw new Exception("Database prepare error: " . $conn->error);
            }
            
            $check_stmt->bind_param("ss", $atl_code, $school_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                throw new Exception("A record with this ATL Code or School Name already exists.");
            }
            $check_stmt->close();

            // Insert new record
            $sql = "INSERT INTO atl_details (
                school_name, sanction_no, udise, fund_date, inauguration_date,
                principal_name_phone, atl_code, atl_pswd, gem_code, govt_email,
                pfms, pfms_op, pfms_ap, atl_incharge_phone, dashbrd_pswd,
                gem_pswd, govt_mail_pswd, pfms_pswd, op_pswd, ap_pswd,
                reg_email, reg_phone, bharatkosh, bkosh_password, atl_vendor, vcomment
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $debug_info['prepare_error'] = $conn->error;
                throw new Exception("Database prepare error: " . $conn->error);
            }

            $stmt->bind_param("ssssssssssssssssssssssssss", 
                $school_name, $sanction_no, $udise, $fund_date, $inauguration_date,
                $principal_name_phone, $atl_code, $atl_pswd, $gem_code, $govt_email,
                $pfms, $pfms_op, $pfms_ap, $atl_incharge_phone, $dashbrd_pswd,
                $gem_pswd, $govt_mail_pswd, $pfms_pswd, $op_pswd, $ap_pswd,
                $reg_email, $reg_phone, $bharatkosh, $bkosh_password, $atl_vendor, $vcomment
            );

            if (!$stmt->execute()) {
                $debug_info['execute_error'] = $stmt->error;
                throw new Exception("Database execute error: " . $stmt->error);
            }

            $message = "ATL details added successfully!";
        }

        // Success response
        echo json_encode([
            "status" => "success",
            "message" => $message,
            "debug_info" => $debug_info
        ]);

    } catch (Exception $e) {
        // Error response
        echo json_encode([
            "status" => "error",
            "message" => "Error: " . $e->getMessage(),
            "debug_info" => $debug_info ?? []
        ]);
    } finally {
        // Close connections
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    // Not a POST request
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Only POST requests are allowed."
    ]);
}
?>