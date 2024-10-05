<?php
include('../condb.php');

if (isset($_POST['employee']) && $_POST['employee'] == "add") {

    $query_empId = "SELECT empID FROM (SELECT empID FROM employee ORDER BY empID DESC) WHERE ROWNUM = 1";
    $rs_id = oci_parse($condb, $query_empId);
    oci_execute($rs_id);
    $row = oci_fetch_assoc($rs_id);

    if ($row) {
        $rs_id = $row['EMPID'];
        $cha_ID = substr($rs_id, 0, 3);
        $int_ID = substr($rs_id, 3);
        $new_int_ID = str_pad((int)$int_ID + 1, 7, '0', STR_PAD_LEFT);
        $emp_ID = $cha_ID . $new_int_ID;
    } else {
        $emp_ID = "EMP0000001";
    }


    $emp_Fname = trim($_POST["emp_Fname"]);
    $emp_Lname = trim($_POST["emp_Lname"]);
    $emp_Username = $emp_ID;
    $emp_Password = sha1($emp_ID);
    $emp_gender = $_POST["emp_gender"];
    $emp_bDate = $_POST["emp_bDate"];
    $emp_Phone = $_POST["emp_Phone"];
    $empCountLock = "0";
    $emp_department = $_POST["emp_department"];
    $emp_role = $_POST["emp_role"];
    $emp_stalD = "STA0000001";
    // $emp_lockstalD = "STA0000010";


    $sql = "INSERT INTO employee (
        empID,
        empFname,
        empLname,
        empUserName,
        empPassword,
        empGender,
        empBdate,
        empPhone,
        empCountLock,
        emp_depID,
        emp_roleID,
        emp_stalD
        -- EMP_LOCKSTAID
    ) VALUES (
        :emp_ID,
        :emp_Fname,
        :emp_Lname,
        :emp_Username,
        :emp_Password,
        :emp_gender,
        TO_DATE(:emp_bDate, 'YYYY-MM-DD'),
        :emp_Phone,
        :empCountLock,
        :emp_department,
        :emp_role,
        :emp_stalD
        -- :emp_lockstalD
    )";

    $stmt = oci_parse($condb, $sql);
    oci_bind_by_name($stmt, ':emp_ID', $emp_ID);
    oci_bind_by_name($stmt, ':emp_Fname', $emp_Fname);
    oci_bind_by_name($stmt, ':emp_Lname', $emp_Lname);
    oci_bind_by_name($stmt, ':emp_Username', $emp_Username);
    oci_bind_by_name($stmt, ':emp_Password', $emp_Password);
    oci_bind_by_name($stmt, ':emp_gender', $emp_gender);
    oci_bind_by_name($stmt, ':emp_bDate', $emp_bDate);
    oci_bind_by_name($stmt, ':emp_Phone', $emp_Phone);
    oci_bind_by_name($stmt, ':empCountLock', $empCountLock);
    oci_bind_by_name($stmt, ':emp_department', $emp_department);
    oci_bind_by_name($stmt, ':emp_role', $emp_role);
    oci_bind_by_name($stmt, ':emp_stalD', $emp_stalD);
    // oci_bind_by_name($stmt, ':emp_lockstalD', $emp_lockstalD);

    $result = oci_execute($stmt, OCI_NO_AUTO_COMMIT);

    if ($result) {
        oci_commit($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'employee.php?employee_add=employee_add'; ";
        echo "</script>";
    } else {
        $e = oci_error($stmt);
        echo "<script type='text/javascript'>";
        echo "alert('Error: " . htmlentities($e['message']) . "');";
        echo "window.location = 'employee.php?employee_add_error=employee_add_error'; ";
        echo "</script>";
    }
}

if (isset($_GET['id'])) {
    $empID = $_GET['id'];
    $query = "SELECT empID AS \"empID\", empFname AS \"empFname\", empLname AS \"empLname\", empGender AS \"empGender\", empPhone AS \"empPhone\", emp_roleID AS \"emp_roleID\", emp_depID AS \"emp_depID\", emp_stalD AS \"emp_stalD\", TO_CHAR(empBdate, 'YYYY-MM-DD') AS \"empBdate\"
              FROM employee WHERE empID = :empID";
    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':empID', $empID);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);

    if ($row) {
        echo json_encode($row);  // ตรวจสอบว่าข้อมูลถูกดึงมาหรือไม่
    } else {
        echo json_encode(['error' => 'ไม่พบข้อมูล']);
    }
    exit();
}


if (isset($_POST['employee']) && $_POST['employee'] == 'Edit') {
    $empID = $_POST['empID'];
    $empFname = trim($_POST['emp_Fname']);
    $empLname = trim($_POST['emp_Lname']);
    $empGender = $_POST['emp_gender'];
    $empbdate = $_POST['emp_bDate2'];
    $empPhone = $_POST['emp_Phone'];
    $empDepartment = $_POST['emp_department2'];
    $empRole = $_POST['emp_role2'];
    $empStatus = $_POST['emp_status2'];

    if (!empty($_POST['emp_Password'])) {
        $empPassword = sha1(trim($_POST['emp_Password']));
        $query = "UPDATE employee 
                SET empFname = :empFname, empLname = :empLname, empPassword = :empPassword, empGender = :empGender, empBdate = TO_DATE(:empbdate, 'YYYY-MM-DD'), empPhone = :empPhone, emp_roleID = :empRole, emp_depID = :empDepartment, emp_stalD = :empStatus
                WHERE empID = :empID";
        $stmt = oci_parse($condb, $query);
        oci_bind_by_name($stmt, ':empPassword', $empPassword);
    } else {
        $query = "UPDATE employee 
                SET empFname = :empFname, empLname = :empLname, empGender = :empGender, empBdate = TO_DATE(:empbdate, 'YYYY-MM-DD'), empPhone = :empPhone, emp_roleID = :empRole, emp_depID = :empDepartment, emp_stalD = :empStatus
                WHERE empID = :empID";
        $stmt = oci_parse($condb, $query);
    }

    oci_bind_by_name($stmt, ':empFname', $empFname);
    oci_bind_by_name($stmt, ':empLname', $empLname);
    oci_bind_by_name($stmt, ':empGender', $empGender);
    oci_bind_by_name($stmt, ':empbdate', $empbdate);
    oci_bind_by_name($stmt, ':empPhone', $empPhone);
    oci_bind_by_name($stmt, ':empRole', $empRole);
    oci_bind_by_name($stmt, ':empDepartment', $empDepartment);
    oci_bind_by_name($stmt, ':empStatus', $empStatus);
    oci_bind_by_name($stmt, ':empID', $empID);

    $result = oci_execute($stmt, OCI_NO_AUTO_COMMIT);

    if ($result) {
        oci_commit($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'employee.php?employee_edit=employee_edit'; ";
        echo "</script>";
    } else {
        $e = oci_error($stmt);
        echo "<script type='text/javascript'>";
        echo "alert('Error: " . htmlentities($e['message']) . "');";
        echo "window.location = 'employee.php?employee_edit_error=employee_edit_error'; ";
        echo "</script>";
    }
}
?>