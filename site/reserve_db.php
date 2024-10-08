<?php
include('../condb.php');

if (isset($_POST['reserve']) && $_POST['reserve'] == "add") {
    $empID = $_POST["empID"];
    $reserve_date = $_POST["reserve_date"];
    $current_date = date('Y-m-d');

    
    if (strtotime($reserve_date) < strtotime($current_date)) {
        echo "<script type='text/javascript'>";
        echo "window.location = 'reserve.php?reserve_backdate=reserve_backdate'; ";
        echo "</script>";
        exit();
    }

    
    $check_lock_sql = "SELECT EMPID FROM EMPLOYEE WHERE EMPID = :empID AND EMPCOUNTLOCK >= 3";
    $check_lock_stmt = oci_parse($condb, $check_lock_sql);
    oci_bind_by_name($check_lock_stmt, ':empID', $empID);
    oci_execute($check_lock_stmt);

    if ($lock_row = oci_fetch_assoc($check_lock_stmt)) {
        
        echo "<script type='text/javascript'>";
        echo "window.location = 'reserve.php?reserve_locked=reserve_locked'; ";
        echo "</script>";
        exit();
    }

    
    $reserve_duration = $_POST["reserve_duration"];
    $room_id = $_POST["room_id"];
    
    $check_sql = "SELECT RESERVEID FROM RESERVEROOM 
                  WHERE RESERVELWILLDATE = TO_DATE(:reserve_date, 'YYYY-MM-DD')
                  AND RESERVEL_DURATIONID = :reserve_duration 
                  AND RESERVEL_ROOMID = :room_id 
                  AND RESERVEL_BOOKINGSTATUSID = 'STA0000007'";
    
    $check_stmt = oci_parse($condb, $check_sql);
    oci_bind_by_name($check_stmt, ':reserve_date', $reserve_date);
    oci_bind_by_name($check_stmt, ':reserve_duration', $reserve_duration);
    oci_bind_by_name($check_stmt, ':room_id', $room_id);
    oci_execute($check_stmt);

    $row = oci_fetch_assoc($check_stmt);
    
    if ($row) {
        echo "<script type='text/javascript'>";
        echo "window.location = 'reserve.php?reserve_havereserv=reserve_havereserv'; ";
        echo "</script>";
        exit();
    }

    
    $query_reservid = "SELECT RESERVEID FROM (SELECT RESERVEID FROM RESERVEROOM ORDER BY RESERVEID DESC) WHERE ROWNUM = 1";
    $rs_id = oci_parse($condb, $query_reservid);
    oci_execute($rs_id);
    $row = oci_fetch_assoc($rs_id);

    if ($row) {
        $rs_id = $row['RESERVEID'];
        $cha_ID = substr($rs_id, 0, 3);
        $int_ID = substr($rs_id, 3);
        $new_int_ID = str_pad((int)$int_ID + 1, 7, '0', STR_PAD_LEFT);
        $reserv_ID = $cha_ID . $new_int_ID;
    } else {
        $reserv_ID = "RES0000001";
    }

    
    $reserve_roomdetail = $_POST["reserve_roomdetail"];
    $reserve_type = $_POST["reserve_type"];
    if ($reserve_type == "VIP") {
        $reserve_staid = "STA0000006";
    } else {
        $reserve_staid = "STA0000005";
    }
    $reserve_bookingsta = "STA0000007";
    $reserve_QRcode = $reserv_ID . $empID . $reserve_date . $reserve_duration;

    
    $sql = "INSERT INTO RESERVEROOM (
        RESERVEID,
        RESERVELWILLDATE,
        RESERVELDETAIL,
        RESERVELQRCODE,
        RESERVEL_EMPID,
        RESERVEL_DURATIONID,
        RESERVEL_ROOMID,
        RESERVEL_STAID,
        RESERVEL_BOOKINGSTATUSID
    ) VALUES (
        :reserv_ID,
        TO_DATE(:reserve_date, 'YYYY-MM-DD'),
        :reserve_roomdetail,
        :reserve_QRcode,
        :empID,
        :reserve_duration,
        :room_id,
        :reserve_staid,
        :reserve_bookingsta
    )";

    $stmt = oci_parse($condb, $sql);
    oci_bind_by_name($stmt, ':reserv_ID', $reserv_ID);
    oci_bind_by_name($stmt, ':reserve_date', $reserve_date);
    oci_bind_by_name($stmt, ':reserve_roomdetail', $reserve_roomdetail);
    oci_bind_by_name($stmt, ':reserve_QRcode', $reserve_QRcode);
    oci_bind_by_name($stmt, ':empID', $empID);
    oci_bind_by_name($stmt, ':reserve_duration', $reserve_duration);
    oci_bind_by_name($stmt, ':room_id', $room_id);
    oci_bind_by_name($stmt, ':reserve_staid', $reserve_staid);
    oci_bind_by_name($stmt, ':reserve_bookingsta', $reserve_bookingsta);

    $result = oci_execute($stmt, OCI_NO_AUTO_COMMIT);

    if ($result) {
        oci_commit($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'reserve.php?reserve_add=reserve_add'; ";
        echo "</script>";
    } else {
        $e = oci_error($stmt);
        echo "<script type='text/javascript'>";
        echo "alert('Error: " . htmlentities($e['message']) . "');";
        echo "window.location = 'reserve.php?reserve_add_add_error=reserve_add_add_error'; ";
        echo "</script>";
    }
}


// ดึงข้อมูลชั้นจากตึกที่เลือก
if (isset($_POST['building_id'])) {
    $building_id = $_POST['building_id'];
    $query = "SELECT FLOORID AS floorID, FLOORNAME AS floorName FROM FLOOR WHERE BUIID = :building_id";
    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':building_id', $building_id);
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

// ดึงข้อมูลห้องจากชั้นที่เลือก
if (isset($_POST['floor_id'])) {
    $floor_id = $_POST['floor_id'];
    $query = "SELECT  ROOMID AS roomID,
    BUILDING.BUINAME || FLOOR.FLOORNAME||ROOMNAME AS roomName
    FROM ROOM
    INNER JOIN FLOOR ON FLOOR.FLOORID = ROOM.ROOM_FLOORID
    INNER JOIN BUILDING ON FLOOR.BUIID = BUILDING.BUIID
    WHERE ROOM_FLOORID = :floor_id";
    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':floor_id', $floor_id);
    oci_execute($stmt);

    $rooms = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $rooms[] = array(
            "roomID" => $row['ROOMID'],
            "roomName" => $row['ROOMNAME']
        );
    }
    echo json_encode($rooms);
}


if (isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];

    $query = "SELECT 
        reserveID AS reserveID, 
        BUILDING.BUINAME || FLOOR.FLOORNAME||room.ROOMNAME  AS roomName,
        room.roomID AS roomID,
        TO_CHAR(reservelWillDate, 'YYYY-MM-DD') AS start_date, 
        TO_CHAR(duration.DurationStartTime, 'HH24:MI:SS') AS start_time, 
        TO_CHAR(duration.DurationEndTime, 'HH24:MI:SS') AS end_time, 
        reservelDetail AS description
    FROM reserveroom
    INNER JOIN room ON room.roomID = reserveroom.reservel_roomID
    INNER JOIN duration ON duration.durationID = reserveroom.reservel_durationID
    INNER JOIN FLOOR ON FLOOR.FLOORID = ROOM.ROOM_FLOORID
    INNER JOIN BUILDING ON FLOOR.BUIID = BUILDING.BUIID
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000007'
    AND reserveroom.reservel_roomID = :room_id";

    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':room_id', $room_id);
    oci_execute($stmt);

    $events = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $start = $row['START_DATE'] . 'T' . $row['START_TIME'];
        $end = $row['START_DATE'] . 'T' . $row['END_TIME'];

        $events[] = [
            'id' => $row['RESERVEID'],
            'title' => $row['ROOMNAME'],
            'start' => $start,
            'end' => $end,
            'description' => $row['DESCRIPTION'],
        ];
    }
    echo json_encode($events);
}

if (isset($_POST['room_id_room'])) {
    $room_id = $_POST['room_id_room'];

    $query = "SELECT ROOMCAPACITY AS roomcapacity,
        ROOMDETAIL AS roomdetail,
        ROOMID AS ROOMID,
        roomtypt.ROOMTYPNAME AS roomtype
    FROM room
        INNER JOIN roomtypt ON roomtypt.ROOMTYPTID  = room.ROOM_ROOMTYPTID
    WHERE ROOMID = :room_id";

    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':room_id', $room_id);
    oci_execute($stmt);

    $room_details = [];
    if ($row = oci_fetch_assoc($stmt)) {
        $room_details = [
            'roomcapacity' => $row['ROOMCAPACITY'],
            'roomid1' => $row['ROOMID'],
            'roomdetail' => $row['ROOMDETAIL'] ,
            'roomtype' => $row['ROOMTYPE']
        ];
    }
    echo json_encode($room_details);
    exit;
}


?>