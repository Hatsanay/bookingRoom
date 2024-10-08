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
    exit;
}

// ดึงข้อมูลห้องจากชั้นที่เลือก
if (isset($_POST['floor_id'])) {
    $floor_id = $_POST['floor_id'];
    $query = "SELECT ROOMID AS roomID, 
    BuiName ||  floorName  || roomName AS roomName 
    FROM ROOM
    INNER JOIN floor ON room.room_floorID = floor.floorID
    INNER JOIN building ON floor.BuiID = building.BuiID
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
    exit;
}
?>
