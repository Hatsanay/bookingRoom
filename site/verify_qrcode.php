<?php
include('condb.php');

// รับข้อมูล QR Code ที่ส่งมา
$data = json_decode(file_get_contents('php://input'), true);
$qrcode = $data['qrcode'];

// ตรวจสอบข้อมูล QR Code กับฐานข้อมูล
$query = "SELECT * FROM reserveroom WHERE RESERVELQRCODE = :qrcode";
$stmt = oci_parse($condb, $query);
oci_bind_by_name($stmt, ':qrcode', $qrcode);
oci_execute($stmt);

// ตรวจสอบผลลัพธ์
if ($row = oci_fetch_assoc($stmt)) {
    // ถ้า QR Code ถูกต้อง
    echo json_encode(['status' => 'success']);
} else {
    // ถ้า QR Code ไม่ถูกต้อง
    echo json_encode(['status' => 'error']);
}

oci_free_statement($stmt);
oci_close($condb);
?>
