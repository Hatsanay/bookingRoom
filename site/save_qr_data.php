<?php
include('../condb.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reserveID = $_POST['reserveID'];
    $empID = $_POST['empID'];
    $loggedInEmpID = $_POST['loggedInEmpID']; // รหัสพนักงานที่เข้าสู่ระบบ
    $reserveDate = $_POST['reserveDate'];
    $durationID = $_POST['durationID'];

    if (!$condb) {
        $m = oci_error();
        echo "<script>alert('Error connecting to the database: " . htmlentities($m['message']) . "');</script>";
        exit;
    }

    // ตรวจสอบว่ารหัสพนักงานจาก QR ตรงกับรหัสพนักงานที่เข้าสู่ระบบหรือไม่
    if ($empID !== $loggedInEmpID) {
        echo "<script>alert('รหัสพนักงานไม่ตรงกัน คุณไม่มีสิทธิ์เข้าใช้ห้องนี้.');</script>";
        exit;
    }

    // ตรวจสอบการจอง
    $sql = "SELECT * FROM RESERVEROOM 
            WHERE RESERVEID = :reserveID 
            AND RESERVEL_EMPID = :empID 
            AND RESERVELWILLDATE = TO_DATE(:reserveDate, 'YYYY-MM-DD')
            AND RESERVEL_DURATIONID = :durationID 
            AND RESERVEL_BOOKINGSTATUSID = 'STA0000007'";

    $stmt = oci_parse($condb, $sql);
    oci_bind_by_name($stmt, ':reserveID', $reserveID);
    oci_bind_by_name($stmt, ':empID', $empID);
    oci_bind_by_name($stmt, ':reserveDate', $reserveDate);
    oci_bind_by_name($stmt, ':durationID', $durationID);
    
    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        echo "<script>alert('Error executing query: " . htmlentities($e['message']) . "');</script>";
        exit;
    }

    $row = oci_fetch_assoc($stmt);

    if ($row) {
        // ตรวจสอบการเข้าใช้ห้อง (ACCESSROOM)
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
        oci_bind_by_name($stmt_insert, ':accessID', $accessID);
        oci_bind_by_name($stmt_insert, ':empID', $empID);
        oci_bind_by_name($stmt_insert, ':reserveID', $reserveID);

        // อัปเดตสถานะการจอง
        $sql_update = "UPDATE RESERVEROOM 
                       SET RESERVEL_BOOKINGSTATUSID = :reservel_BookingstatusID
                       WHERE RESERVEID = :reserveID";
        $stmt_update = oci_parse($condb, $sql_update);
        $reservel_BookingstatusID = "STA0000011"; 
        oci_bind_by_name($stmt_update, ':reservel_BookingstatusID', $reservel_BookingstatusID);
        oci_bind_by_name($stmt_update, ':reserveID', $reserveID);

        $result = oci_execute($stmt_insert, OCI_NO_AUTO_COMMIT);
        $result2 = oci_execute($stmt_update, OCI_NO_AUTO_COMMIT);

        if ($result && $result2) {
            oci_commit($condb);
            echo "<script>alert('การบันทึกข้อมูลสำเร็จ'); window.location = 'index.php?access_add=access_add';</script>";
        } else {
            $e_insert = oci_error($stmt_insert);
            $e_update = oci_error($stmt_update);
            echo "<script>alert('Error inserting data: " . htmlentities($e_insert['message']) . " or updating data: " . htmlentities($e_update['message']) . "');</script>";
        }
    } else {
        echo "<script>alert('ไม่มีสิทธิ์เข้าใช้ห้องนี้หรือข้อมูลไม่ถูกต้อง.');</script>";
    }

    oci_free_statement($stmt);
    oci_free_statement($stmt_insert);
    oci_free_statement($stmt_update);
    oci_close($condb);
}
?>
