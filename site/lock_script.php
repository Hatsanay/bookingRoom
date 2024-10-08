<?php
include('../condb.php');

// ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลสำเร็จหรือไม่
if (!$condb) {
    die("Connection failed: " . oci_error());
}

// ดึงข้อมูลพนักงานที่ไม่มาใช้งานตามเงื่อนไขที่กำหนด โดยดึงเฉพาะเวลาจาก DURATIONENDTIME ที่หมดเวลาแล้วเท่านั้น
$query = "SELECT EMPLOYEE.EMPID, EMPLOYEE.EMPCOUNTLOCK, RESERVEROOM.RESERVEID, 
                 RESERVEROOM.RESERVELWILLDATE, 
                 TO_CHAR(DURATION.DURATIONENDTIME, 'HH24:MI:SS') AS DURATIONENDTIME
          FROM EMPLOYEE
          INNER JOIN RESERVEROOM ON EMPLOYEE.EMPID = RESERVEROOM.RESERVEL_EMPID
          INNER JOIN DURATION ON RESERVEROOM.RESERVEL_DURATIONID = DURATION.DURATIONID
          WHERE RESERVEROOM.RESERVEL_BOOKINGSTATUSID = 'STA0000007'
          AND TRUNC(RESERVEROOM.RESERVELWILLDATE) <= TRUNC(SYSDATE)
          AND TO_DATE(RESERVEROOM.RESERVELWILLDATE || ' ' || TO_CHAR(DURATION.DURATIONENDTIME, 'HH24:MI:SS'), 'DD-MM-YYYY HH24:MI:SS') < SYSDATE";

$result = oci_parse($condb, $query);
if (!oci_execute($result)) {
    $e = oci_error($result);
    die("Error executing query: " . $e['message']);
}

while ($row = oci_fetch_assoc($result)) {
    $empID = $row['EMPID'];
    $currentLockCount = $row['EMPCOUNTLOCK'];
    $reserveID = $row['RESERVEID'];
    $willDate = $row['RESERVELWILLDATE'];
    $durationEndTime = $row['DURATIONENDTIME']; // เวลาสิ้นสุดในรูปแบบ 'HH24:MI:SS'

    // สร้างเวลาสิ้นสุดการจองเป็น timestamp โดยรวมวันที่จาก willDate กับเวลาจาก durationEndTime
    $durationEndTimeUnix = strtotime($willDate . ' ' . $durationEndTime); 
    $currentTime = time();

    // ตรวจสอบเฉพาะรายการที่เวลาสิ้นสุดการจองอยู่ในอดีต (เวลาปัจจุบันเกินจากเวลาสิ้นสุดการจอง)
    if ($durationEndTimeUnix < $currentTime) {
        // เพิ่มจำนวนครั้งที่ไม่มาใช้งาน
        $currentLockCount++;

        // อัปเดต EMPCOUNTLOCK ใน EMPLOYEE
        $updateQuery = "UPDATE EMPLOYEE SET EMPCOUNTLOCK = :currentLockCount WHERE EMPID = :empID";
        $updateStmt = oci_parse($condb, $updateQuery);
        oci_bind_by_name($updateStmt, ':currentLockCount', $currentLockCount);
        oci_bind_by_name($updateStmt, ':empID', $empID);

        if (!oci_execute($updateStmt)) {
            $e = oci_error($updateStmt);
            echo "Error updating EMPLOYEE: " . $e['message'] . "<br>";
            continue;
        }

        // อัปเดตสถานะการจองใน RESERVEROOM เฉพาะกรณีที่การจองหมดเวลาเข้าใช้งานแล้ว
        $updateReserv = "UPDATE RESERVEROOM 
                         SET RESERVEL_BOOKINGSTATUSID = 'STA0000012' 
                         WHERE RESERVEID = :reserveID 
                         AND RESERVEL_BOOKINGSTATUSID = 'STA0000007'"; 
        $stmt2 = oci_parse($condb, $updateReserv);
        oci_bind_by_name($stmt2, ':reserveID', $reserveID);

        if (!oci_execute($stmt2)) {
            $e = oci_error($stmt2);
            echo "Error updating RESERVEROOM: " . $e['message'] . "<br>";
        }

        // ถ้าครบ 3 ครั้ง ให้ทำการล็อก
        if ($currentLockCount >= 3) {
            // ดึง LOCKID ล่าสุด
            $query_empId = "SELECT LOCKID FROM (SELECT LOCKID FROM LOCK_TABLE ORDER BY LOCKID DESC) WHERE ROWNUM = 1";
            $rs_id = oci_parse($condb, $query_empId);
            oci_execute($rs_id);
            $lockRow = oci_fetch_assoc($rs_id);

            if ($lockRow) {
                $rs_id = $lockRow['LOCKID'];
                $cha_ID = substr($rs_id, 0, 4);
                $int_ID = substr($rs_id, 4);
                $new_int_ID = str_pad((int)$int_ID + 1, 6, '0', STR_PAD_LEFT);
                $lockID = $cha_ID . $new_int_ID;
            } else {
                $lockID = "LOCK000001";
            }

            // บันทึกข้อมูลล็อกใน LOCK_TABLE
            $lockDate = date('d-m-Y');
            $lockStatus = 'STA0000009';

            $insertLockQuery = "INSERT INTO LOCK_TABLE (LOCKID, LOCKDATE, LOCK_EMPID, LOCK_STAID) 
                                VALUES (:lockID, TO_DATE(:lockDate, 'DD-MM-YYYY'), :empID, :lockStatus)";
            $insertLockStmt = oci_parse($condb, $insertLockQuery);
            oci_bind_by_name($insertLockStmt, ':lockID', $lockID);
            oci_bind_by_name($insertLockStmt, ':lockDate', $lockDate);
            oci_bind_by_name($insertLockStmt, ':empID', $empID);
            oci_bind_by_name($insertLockStmt, ':lockStatus', $lockStatus);

            if (!oci_execute($insertLockStmt)) {
                $e = oci_error($insertLockStmt);
                echo "Error inserting into LOCK_TABLE: " . $e['message'] . "<br>";
            }
        }
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
oci_close($condb);
?>
