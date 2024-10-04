<?php
include('../condb.php');

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
        room.roomName AS roomName,
        room.roomID AS roomID,
        TO_CHAR(reservelWillDate, 'YYYY-MM-DD') AS start_date, 
        TO_CHAR(duration.DurationStartTime, 'HH24:MI:SS') AS start_time, 
        TO_CHAR(duration.DurationEndTime, 'HH24:MI:SS') AS end_time, 
        reservelDetail AS description
    FROM reserveroom
    INNER JOIN room ON room.roomID = reserveroom.reservel_roomID
    INNER JOIN duration ON duration.durationID = reserveroom.reservel_durationID
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

    $query = "SELECT ROOMCAPACITY AS roomcapacity, ROOMDETAIL AS roomdetail
              FROM room
              WHERE ROOMID = :room_id";

    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':room_id', $room_id);
    oci_execute($stmt);

    $room_details = [];
    if ($row = oci_fetch_assoc($stmt)) {
        $room_details = [
            'roomcapacity' => $row['ROOMCAPACITY'],
            'roomdetail' => $row['ROOMDETAIL']  
        ];
    }
    echo json_encode($room_details);
    exit;
}


?>