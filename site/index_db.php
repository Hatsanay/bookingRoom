<?php
include('../condb.php');
if (isset($_POST['listReserve']) && $_POST['listReserve'] == "cancel") {

    // if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //     echo "Form was submitted!<br>";
    //     print_r($_POST); // ดูข้อมูลที่ถูกส่งมา
    //     exit();
    // }
    // echo "Received reserveID: " . $reserve_ID . "<br>";
    // echo "Received Cancel Detail: " . $reserve_DetialCancel;


    $reserve_ID = $_POST["reservID"];
    $reserve_DetialCancel = $_POST["reserve_DtlCancle"];
    $reserve_Status = "STA0000008";

    $query = "UPDATE reserveroom
              SET reservel_BookingstatusID = :reservel_BookingstatusID
              WHERE reserveID = :reserveID";
    $result = oci_parse($condb, $query);
    oci_bind_by_name($result, ':reservel_BookingstatusID', $reserve_Status);
    oci_bind_by_name($result, ':reserveID', $reserve_ID);


    $sql = "INSERT INTO reserveidtailcancel (
        RDC_reserveID,
        DETAIL_TEXT
    ) VALUES (
        :reserve_ID,
        :reserve_DetialCancel
    )";
    $resultinsertDetial = oci_parse($condb, $sql);
    oci_bind_by_name($resultinsertDetial, ':reserve_ID', $reserve_ID);
    oci_bind_by_name($resultinsertDetial, ':reserve_DetialCancel', $reserve_DetialCancel);


    $result1 = oci_execute($result, OCI_NO_AUTO_COMMIT);
    $result2 = oci_execute($resultinsertDetial, OCI_NO_AUTO_COMMIT);


    if ($result1 && $result2) {
        oci_commit($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'index.php?reserve_cancle=reserve_cancle'; ";
        echo "</script>";
    } else {
        $e = oci_error($result1 ? $resultinsertDetial : $result);
        oci_rollback($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'index.php?reserve_cancle_error=reserve_cancle_error'; ";
        echo "</script>";
    }
}
?>
