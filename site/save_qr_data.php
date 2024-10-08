<?php
// เปิดการแสดงผลข้อผิดพลาดทั้งหมดเพื่อช่วย debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// กำหนด Content-Type เป็น JSON และเข้ารหัสเป็น UTF-8
header('Content-Type: application/json; charset=utf-8');

include('../condb.php');

// ตั้งค่า Timezone ให้ PHP
date_default_timezone_set('Asia/Bangkok');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reserveID = $_POST['reserveID'];
    $empID = $_POST['empID'];
    $loggedInEmpID = $_POST['loggedInEmpID']; // รหัสพนักงานที่เข้าสู่ระบบ

    if (!$condb) {
        $m = oci_error();
        echo json_encode(['status' => 'error', 'message' => 'Error connecting to the database: ' . htmlentities($m['message'])]);
        exit;
    }

    // ตรวจสอบว่ารหัสพนักงานจาก QR ตรงกับรหัสพนักงานที่เข้าสู่ระบบหรือไม่
    if ($empID !== $loggedInEmpID) {
        echo json_encode(['status' => 'error', 'message' => 'รหัสพนักงานไม่ตรงกัน คุณไม่มีสิทธิ์เข้าใช้ห้องนี้']);
        exit;
    }

    // ตรวจสอบการจองและดึงข้อมูลวันที่และช่วงเวลา
    $sql = "SELECT TO_CHAR(RESERVELWILLDATE, 'YYYY-MM-DD') AS RESERVE_DATE, 
                   TO_CHAR(DURATIONSTARTTIME, 'HH24:MI:SS') AS STARTTIME, 
                   TO_CHAR(DURATIONENDTIME, 'HH24:MI:SS') AS ENDTIME 
            FROM RESERVEROOM 
            INNER JOIN DURATION ON DURATION.DURATIONID = RESERVEROOM.RESERVEL_DURATIONID 
            WHERE RESERVEID = :reserveID 
            AND RESERVEL_EMPID = :empID 
            AND RESERVEL_BOOKINGSTATUSID = 'STA0000007'";

    $stmt = oci_parse($condb, $sql);
    oci_bind_by_name($stmt, ':reserveID', $reserveID);
    oci_bind_by_name($stmt, ':empID', $empID);

    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        echo json_encode(['status' => 'error', 'message' => 'Error executing query: ' . htmlentities($e['message'])]);
        exit;
    }

    $row = oci_fetch_assoc($stmt);

    if ($row) {
        // ตรวจสอบเวลาและแปลงเป็น DateTime
        $currentDateTime = new DateTime(); // เวลาปัจจุบัน
        $reserveDate = DateTime::createFromFormat('Y-m-d', $row['RESERVE_DATE']); 
        $startTime = DateTime::createFromFormat('H:i:s', $row['STARTTIME']);
        $endTime = DateTime::createFromFormat('H:i:s', $row['ENDTIME']);
        
        // นำเวลาของการจองมารวมกับวันที่ที่จอง
        $startDateTime = new DateTime($reserveDate->format('Y-m-d') . ' ' . $startTime->format('H:i:s'));
        $endDateTime = new DateTime($reserveDate->format('Y-m-d') . ' ' . $endTime->format('H:i:s'));

        // Debug แสดงเวลาเพื่อดูความแตกต่าง
        error_log("Current DateTime: " . $currentDateTime->format('Y-m-d H:i:s'));
        error_log("Start DateTime: " . $startDateTime->format('Y-m-d H:i:s'));
        error_log("End DateTime: " . $endDateTime->format('Y-m-d H:i:s'));

        // ตรวจสอบวันที่และเวลาว่าอยู่ในช่วงที่จองหรือไม่
        if ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) {
            // บันทึกการเข้าใช้ห้องและอัปเดตสถานะการจอง
            $query_empId = "SELECT ACCESSID FROM (SELECT ACCESSID FROM ACCESSROOM ORDER BY ACCESSID DESC) WHERE ROWNUM = 1";
            $rs_id = oci_parse($condb, $query_empId);
            oci_execute($rs_id);
            $row_id = oci_fetch_assoc($rs_id);

            if ($row_id) {
                $last_accessID = $row_id['ACCESSID'];
                $cha_ID = substr($last_accessID, 0, 3);
                $int_ID = substr($last_accessID, 3);
                $new_int_ID = str_pad((int)$int_ID + 1, 7, '0', STR_PAD_LEFT);
                $accessID = $cha_ID . $new_int_ID;
            } else {
                $accessID = "ACC0000001";
            }

            // บันทึกการเข้าใช้ห้อง
            $sql_insert = "INSERT INTO ACCESSROOM (ACCESSID, ACCESSDATE, ACCESS_EMPID, ACCESS_RESERVEID) 
                           VALUES (:accessID, SYSDATE, :empID, :reserveID)";
            $stmt_insert = oci_parse($condb, $sql_insert);
            if ($stmt_insert) {
                oci_bind_by_name($stmt_insert, ':accessID', $accessID);
                oci_bind_by_name($stmt_insert, ':empID', $empID);
                oci_bind_by_name($stmt_insert, ':reserveID', $reserveID);

                // อัปเดตสถานะการจอง
                $sql_update = "UPDATE RESERVEROOM 
                               SET RESERVEL_BOOKINGSTATUSID = :reservel_BookingstatusID
                               WHERE RESERVEID = :reserveID";
                $stmt_update = oci_parse($condb, $sql_update);
                $reservel_BookingstatusID = "STA0000011"; // เปลี่ยนสถานะการจอง
                oci_bind_by_name($stmt_update, ':reservel_BookingstatusID', $reservel_BookingstatusID);
                oci_bind_by_name($stmt_update, ':reserveID', $reserveID);

                $result = oci_execute($stmt_insert, OCI_NO_AUTO_COMMIT);
                $result2 = oci_execute($stmt_update, OCI_NO_AUTO_COMMIT);

                if ($result && $result2) {
                    oci_commit($condb);
                    echo json_encode(['status' => 'success', 'message' => 'การบันทึกข้อมูลสำเร็จ', 'startTime' => $row['STARTTIME'], 'endTime' => $row['ENDTIME']]);
                } else {
                    $e_insert = oci_error($stmt_insert);
                    $e_update = oci_error($stmt_update);
                    echo json_encode(['status' => 'error', 'message' => 'Error inserting data: ' . htmlentities($e_insert['message']) . ' or updating data: ' . htmlentities($e_update['message'])]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error preparing insert statement']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเข้าใช้ห้องได้เพราะไม่อยู่ในช่วงเวลาที่จอง']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ไม่มีสิทธิ์เข้าใช้ห้องนี้หรือข้อมูลไม่ถูกต้อง']);
    }

    if (isset($stmt)) oci_free_statement($stmt);
    if (isset($stmt_insert)) oci_free_statement($stmt_insert);
    if (isset($stmt_update)) oci_free_statement($stmt_update);

    oci_close($condb);
}
?>
