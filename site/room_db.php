<?php
include('../condb.php'); // เชื่อมต่อกับฐานข้อมูล
// ฟังก์ชันสำหรับเพิ่มห้องใหม่
if (isset($_POST['room']) && $_POST['room'] == "add") {
    // ดึง ID ห้องล่าสุดและสร้าง ID ใหม่
    $query_roomID = "SELECT roomID FROM (SELECT roomID FROM room ORDER BY roomID DESC) WHERE ROWNUM = 1";
    $rs_id = oci_parse($condb, $query_roomID);
    oci_execute($rs_id);
    $row = oci_fetch_assoc($rs_id);

    if ($row) {
        $rs_id = $row['ROOMID'];
        $cha_ID = substr($rs_id, 0, 3); // ตัวอักษร ROM
        $int_ID = substr($rs_id, 3);    // ตัวเลขของ ID
        $new_int_ID = str_pad((int)$int_ID + 1, 7, '0', STR_PAD_LEFT); // เพิ่มเลข ID และเติม 0 ให้ครบ 7 หลัก
        $room_ID = $cha_ID . $new_int_ID; // สร้าง ID ใหม่
    } else {
        $room_ID = "ROM0000001"; // ถ้าไม่มีห้องใดในระบบ ให้เริ่มต้นที่ ROM0000001
    }
    // รับค่าจากฟอร์ม และป้องกัน SQL Injection
    $roomName = trim($_POST["roomName"]);
    $roomCapacity = $_POST["roomCapacity"];
    $roomDetail = $_POST["roomDetail"];
    $room_floor = $_POST["room_floor"];
    $room_roomtypeID = $_POST["room_roomtypeID"];
    $room_status = "STA0000003";
    $room_empID = $_POST["room_empID"];
    // SQL สำหรับเพิ่มห้องใหม่
    $sql = "INSERT INTO ROOM (
        roomID,
        roomName,
        roomCapacity,
        roomDetail,
        room_floorID,
        room_roomtyptID,
        room_staID,
        room_empID
    ) VALUES (
        :room_ID,
        :roomName,
        :roomCapacity,
        :roomDetail,
        :room_floor,
        :room_roomtypeID,
        :room_status,
        :room_empID
    )";

    $stmt = oci_parse($condb, $sql);
    oci_bind_by_name($stmt, ':room_ID', $room_ID);
    oci_bind_by_name($stmt, ':roomName', $roomName);
    oci_bind_by_name($stmt, ':roomCapacity', $roomCapacity);
    oci_bind_by_name($stmt, ':roomDetail', $roomDetail);
    oci_bind_by_name($stmt, ':room_floor', $room_floor);
    oci_bind_by_name($stmt, ':room_roomtypeID', $room_roomtypeID);
    oci_bind_by_name($stmt, ':room_status', $room_status);
    oci_bind_by_name($stmt, ':room_empID', $room_empID);

    $result = oci_execute($stmt, OCI_NO_AUTO_COMMIT);

    if ($result) {
        oci_commit($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'room.php?room_add=room_add'; ";
        echo "</script>";
    } else {
        $e = oci_error($stmt);
        echo "<script type='text/javascript'>";
        echo "alert('Error: " . htmlentities($e['message']) . "');";
        echo "window.location = 'room.php?room_add_error=room_add_error'; ";
        echo "</script>";
    }
}

// ฟังก์ชันสำหรับดึงข้อมูลห้องประชุมเพื่อแก้ไข
if (isset($_GET['id'])) {
    $roomID = $_GET['id'];
    $query = "SELECT roomID AS \"roomID\" , 
        roomName AS \"roomName\", 
        roomCapacity AS \"roomCapacity\", 
        roomDetail AS \"roomDetail\", 
        room_floorID AS \"room_floorID\", 
        room_roomtyptID AS \"room_roomtyptID\",
        room_empID AS \"room_empID\",
        room_staID AS \"room_staID\",
        floor.floorName AS \"floorName\",
        building.BuiID AS \"BuiID\"
    FROM room
        INNER JOIN floor ON room.room_floorID = floor.floorID
        INNER JOIN building ON floor.BuiID = building.BuiID
    WHERE roomID = :roomID";
    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':roomID', $roomID); //ใช้ oci_bind_by_name() เพื่อผูกค่าของตัวแปร $roleID เข้ากับพารามิเตอร์ :roleID ในคำสั่ง SQL.
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    
    echo json_encode($row); //ใช้ json_encode() เพื่อแปลงข้อมูลใน $row เป็นรูปแบบ JSON แล้วส่งออกไปยัง client
    exit();
}


if (isset($_POST['building'])) {
    $building = $_POST['building'];
    $query = "SELECT FLOORID AS floorID, FLOORNAME AS floorName FROM FLOOR WHERE BUIID = :building";
    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':building', $building);
    oci_execute($stmt);

    $floors = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $floors[] = array(
            "floorID" => $row['FLOORID'],
            "floorName" => $row['FLOORNAME']
        );
    }
    echo json_encode($floors);
}



// ฟังก์ชันสำหรับแก้ไขห้องประชุม
if (isset($_POST['room']) && $_POST['room'] == 'Edit') {
    $roomID = $_POST['roomID'];
    $roomName = trim($_POST["roomName"]);
    $roomCapacity = $_POST["roomCapacity"];
    $roomDetail = $_POST["roomDetail"];
    // $room_floor = $_POST["room_floor2"];
    $room_roomtypeID = $_POST["room_roomtypeID2"];
    $room_status = $_POST["room_status2"];;
    $room_empID = $_POST["room_empID2"];


    $query = "UPDATE ROOM 
    SET
        roomName = :roomName,
        roomCapacity = :roomCapacity,
        roomDetail = :roomDetail,
        -- room_floorID = :room_floor2,
        room_roomtyptID = :room_roomtypeID2,
        room_staID = :room_status2,
        room_empID = :room_empID2
        WHERE roomID = :roomID";
        
     $stmt = oci_parse($condb, $query);

    oci_bind_by_name($stmt, ':roomID', $roomID);
    oci_bind_by_name($stmt, ':roomName', $roomName);
    oci_bind_by_name($stmt, ':roomCapacity', $roomCapacity);
    oci_bind_by_name($stmt, ':roomDetail', $roomDetail);
    // oci_bind_by_name($stmt, ':room_floor2', $room_floor);
    oci_bind_by_name($stmt, ':room_roomtypeID2', $room_roomtypeID);
    oci_bind_by_name($stmt, ':room_status2', $room_status);
    oci_bind_by_name($stmt, ':room_empID2', $room_empID);

    // ตรวจสอบผลลัพธ์ของการบันทึกข้อมูล
    $result = oci_execute($stmt, OCI_NO_AUTO_COMMIT);

    if ($result) {
        oci_commit($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'room.php?room_edit=room_edit'; ";
        echo "</script>";
    } else {
        $e = oci_error($stmt);
        echo "<script type='text/javascript'>";
        echo "alert('Error: " . htmlentities($e['message']) . "');";
        echo "window.location = 'room.php?room_edit_error=room_edit_error'; ";
        echo "</script>";
    }
}
?>