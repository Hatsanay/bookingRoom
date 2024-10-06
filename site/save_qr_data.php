<?php
include('../condb.php'); // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reserveID = $_POST['reserveID'];
    $empID = $_POST['empID'];
    $reserveDate = $_POST['reserveDate'];
    $durationID = $_POST['durationID'];

    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
    if (!$condb) {
        $m = oci_error();
        echo "<script>alert('Error connecting to the database: " . htmlentities($m['message']) . "');</script>";
        exit;
    }

    // ตรวจสอบว่าพนักงานมีสิทธิ์ในการเข้าใช้ห้องจริงหรือไม่
    $sql = "SELECT * FROM RESERVEROOM 
            WHERE RESERVEID = :reserveID 
            AND RESERVEL_EMPID = :empID 
            AND RESERVELWILLDATE = TO_DATE(:reserveDate, 'YYYY-MM-DD')
            AND RESERVEL_DURATIONID = :durationID 
            AND RESERVEL_BOOKINGSTATUSID = 'STA0000007'";  // ตรวจสอบสถานะการจอง

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
        // ดึง ID ล่าสุดจากฐานข้อมูล ACCESSROOM
        $query_empId = "SELECT ACCESSID FROM (SELECT ACCESSID FROM ACCESSROOM ORDER BY ACCESSID DESC) WHERE ROWNUM = 1";
        $rs_id = oci_parse($condb, $query_empId); 
        oci_execute($rs_id);
        $row_id = oci_fetch_assoc($rs_id);

        // กำหนดค่า ID ใหม่
        if ($row_id) {
            $last_accessID = $row_id['ACCESSID'];        // ดึง ACCESSID ล่าสุด
            $cha_ID = substr($last_accessID, 0, 3);      // แยกตัวอักษร (ACC)
            $int_ID = substr($last_accessID, 3);         // แยกตัวเลข
            $new_int_ID = str_pad((int)$int_ID + 1, 7, '0', STR_PAD_LEFT); // เพิ่ม 1 และเติมเลข 0 ด้านซ้าย
            $accessID = $cha_ID . $new_int_ID;           // รวมตัวอักษรและตัวเลข
        } else {
            $accessID = "ACC0000001";                    // เริ่มต้นที่ ACC0000001 หากไม่มีข้อมูล
        }

        // บันทึกการเข้าใช้ห้องลงใน ACCESSROOM
        $sql_insert = "INSERT INTO ACCESSROOM (ACCESSID, ACCESSDATE, ACCESS_EMPID, ACCESS_RESERVEID) 
                       VALUES (:accessID, SYSDATE, :empID, :reserveID)";
        $stmt_insert = oci_parse($condb, $sql_insert);
        oci_bind_by_name($stmt_insert, ':accessID', $accessID);
        oci_bind_by_name($stmt_insert, ':empID', $empID);
        oci_bind_by_name($stmt_insert, ':reserveID', $reserveID);

        // อัปเดตสถานะการจองใน RESERVEROOM
        $sql_update = "UPDATE RESERVEROOM 
                       SET RESERVEL_BOOKINGSTATUSID = :reservel_BookingstatusID
                       WHERE RESERVEID = :reserveID";
        $stmt_update = oci_parse($condb, $sql_update);
        $reservel_BookingstatusID = "STA0000011"; // สถานะใหม่
        oci_bind_by_name($stmt_update, ':reservel_BookingstatusID', $reservel_BookingstatusID);
        oci_bind_by_name($stmt_update, ':reserveID', $reserveID);

        // Execute การบันทึกและการอัปเดต
        $result = oci_execute($stmt_insert, OCI_NO_AUTO_COMMIT);
        $result2 = oci_execute($stmt_update, OCI_NO_AUTO_COMMIT);

        if ($result && $result2) {
            // Commit ข้อมูล
            oci_commit($condb);
            // เปลี่ยนเส้นทางกลับไปยังหน้า index.php
            echo "<script type='text/javascript'>";
            echo "alert('Data saved successfully.');";
            echo "window.location = 'index.php?access_add=access_add'; ";
            echo "</script>";
        } else {
            // หากมีข้อผิดพลาดในการบันทึกหรืออัปเดต
            $e_insert = oci_error($stmt_insert);
            $e_update = oci_error($stmt_update);
            echo "alert('Error inserting data: " . htmlentities($e_insert['message']) . " or updating data: " . htmlentities($e_update['message']) . "');";
;
        }
    } else {
        // พนักงานไม่มีสิทธิ์เข้าใช้ห้อง หรือข้อมูลไม่ถูกต้อง
        echo "alert('ไม่มีสิทธิ์เข้าใช้ห้องนี้หรือข้อมูลไม่ถูกต้อง.');";
    }

    // Free statement และปิดการเชื่อมต่อฐานข้อมูล
    oci_free_statement($stmt);
    oci_free_statement($stmt_insert);
    oci_free_statement($stmt_update);
    oci_close($condb);
}
?>
