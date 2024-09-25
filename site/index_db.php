<?php
include('../condb.php');
if (isset($_POST['listReserve']) && $_POST['listReserve'] == "cancel") {

        $reserve_ID = mysqli_real_escape_string($condb, $_POST["reservID"]);
        $reserve_DetialCancel = mysqli_real_escape_string($condb, $_POST["reserve_DtlCancle"]);
        $reserve_Status = mysqli_real_escape_string($condb, "STA0000008");

        $query = "UPDATE reserveroom
                SET reservel_BookingstatusID = '$reserve_Status'
                WHERE reserveID = '$reserve_ID'";

        $sql = "INSERT INTO reserveidtailcancel (
            RDC_reserveID,
            Detail
        ) VALUES (
            '$reserve_ID',
            '$reserve_DetialCancel'
        )";
        
        $result = mysqli_query($condb, $query) or die("Error in query: $query " . mysqli_error($condb) . "<br>$query");
        $resultinsertDetial = mysqli_query($condb, $sql) or die("Error in query: $sql " . mysqli_error($condb) . "<br>$sql");

        if ($result && $resultinsertDetial) {
            echo "<script type='text/javascript'>";
            echo "window.location = 'index.php?reserve_cancle=reserve_cancle'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "window.location = 'employee.php?mem_add_error=mem_add_error'; ";
            echo "</script>";
        }
}
?>