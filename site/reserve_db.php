<?php
include('../condb.php');

// ตรวจสอบว่ามีการส่ง building_id มาหรือไม่
if (isset($_POST['building_id'])) {
    $building_id = $_POST['building_id'];

    // ดึงข้อมูลชั้นจากตึกที่เลือก
    $query = "SELECT FLOORID AS floorID, FLOORNAME AS floorName FROM FLOOR WHERE BUIID = :building_id";
    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':building_id', $building_id);
    oci_execute($stmt);

    $floors = [];
    while ($row = oci_fetch_assoc($stmt)) {
        // ตรวจสอบว่าค่าถูกดึงมาอย่างถูกต้อง
        $floors[] = array(
            "floorID" => $row['FLOORID'],
            "floorName" => $row['FLOORNAME']
        );
    }

    // ส่งข้อมูลกลับไปในรูปแบบ JSON
    echo json_encode($floors);
}

// ตรวจสอบว่ามีการส่ง floor_id มาหรือไม่
if (isset($_POST['floor_id'])) {
    $floor_id = $_POST['floor_id'];

    // ดึงข้อมูลห้องจากชั้นที่เลือก
    $query = "SELECT ROOMID AS roomID, ROOMNAME AS roomName FROM ROOM WHERE ROOM_FLOORID = :floor_id";
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

    // ส่งข้อมูลกลับไปในรูปแบบ JSON
    echo json_encode($rooms);
}

if (isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];

    // ดึงข้อมูลการจองที่เกี่ยวข้องกับห้องที่เลือก
    $query = "SELECT 
                reserveID AS reserveID, 
                reservelWillDate AS start_date, 
                duration.DurationStartTime AS start_time, 
                duration.DurationEndTime AS end_time, 
                reservelDetail AS description 
              FROM reserveroom
              INNER JOIN duration ON duration.durationID = reserveroom.reservel_durationID
              WHERE reserveroom.reservel_roomID = :room_id";

    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':room_id', $room_id);
    oci_execute($stmt);

    $events = [];

    while ($row = oci_fetch_assoc($stmt)) {
        $start = $row['START_DATE'] . 'T' . str_replace('.', ':', substr($row['START_TIME'], 11, 8));
        $end = $row['START_DATE'] . 'T' . str_replace('.', ':', substr($row['END_TIME'], 11, 8));

        $events[] = [
            'id' => $row['RESERVEID'],
            'title' => 'จองแล้ว',
            'start' => $start,
            'end' => $end,
            'description' => $row['DESCRIPTION']
        ];
    }

    echo json_encode($events);
}
?>