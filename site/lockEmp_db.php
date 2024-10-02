<?php
include('../condb.php');
if (isset($_POST['lock']) && $_POST['lock'] == "emplock") {
    $emplock_ID = $_POST["lockID"];
    $empID_ID = $_POST["empID"];
    $emplockCount = 0;
    $lockStatus = "STA0000010";

    $query = "UPDATE EMPLOYEE
              SET EMPCOUNTLOCK = :emplockCount
              WHERE EMPID = :empID_ID";
    $update = oci_parse($condb, $query);
    oci_bind_by_name($update, ':empID_ID', $empID_ID);
    oci_bind_by_name($update, ':emplockCount', $emplockCount);

    $query2 = "UPDATE LOCK_TABLE
              SET LOCK_STAID = :lockStatus
              WHERE LOCKID = :emplock_ID";
    $update2 = oci_parse($condb, $query2);
    oci_bind_by_name($update2, ':lockStatus', $lockStatus);
    oci_bind_by_name($update2, ':emplock_ID', $emplock_ID);

    $result1 = oci_execute($update, OCI_NO_AUTO_COMMIT);
    $result2 = oci_execute($update2, OCI_NO_AUTO_COMMIT);

    if ($result1 && $result2) {
        oci_commit($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'lockEmp.php?emplock_unlock=emplock_unlock'; ";
        echo "</script>";
    } else {
        $e = oci_error($result1 ? $resultinsertDetial : $result);
        oci_rollback($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'lockEmp.php?emplock_unlock_error=emplock_unlock_error'; ";
        echo "</script>";
    }
}
?>