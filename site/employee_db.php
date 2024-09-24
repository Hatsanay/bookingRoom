<?php
include('../condb.php');

if (isset($_POST['employee']) && $_POST['employee'] == "add") {
    
    $query_empId = "SELECT
    empID
FROM 
    employee
    ORDER BY empID DESC LIMIT 1
    ;" ;
$rs_id = mysqli_query($condb, $query_empId);
$row = mysqli_fetch_assoc($rs_id);


if ($row) {
    $rs_id = $row['empID'];
    $cha_ID = substr($rs_id, 0, 3);
    $int_ID = substr($rs_id, 3);
    $new_int_ID = str_pad((int)$int_ID + 1, 7, '0', STR_PAD_LEFT);
    $emp_ID = $cha_ID . $new_int_ID;
} else {
    $emp_ID = "EMP0000001";
}

    $emp_Fname = mysqli_real_escape_string($condb, $_POST["emp_Fname"]);
    $emp_Lname = mysqli_real_escape_string($condb, $_POST["emp_Lname"]);
    // $emp_Username = mysqli_real_escape_string($condb, $_POST["emp_Username"]);
    // $emp_Password = mysqli_real_escape_string($condb, sha1($_POST["emp_Password"]));
    $emp_Username = mysqli_real_escape_string($condb, $emp_ID);
    $emp_Password = mysqli_real_escape_string($condb, sha1($emp_ID));
    $emp_gender = mysqli_real_escape_string($condb, $_POST["emp_gender"]);
    $emp_bDate = mysqli_real_escape_string($condb, $_POST["emp_bDate"]);
    $emp_Phone = mysqli_real_escape_string($condb, $_POST["emp_Phone"]);
    $empCountLock = "0";
    $emp_department = mysqli_real_escape_string($condb, $_POST["emp_department"]);
    $emp_role = mysqli_real_escape_string($condb, $_POST["emp_role"]);
    $emp_stalD = "STA0000001";
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
    ) VALUES (
        '$emp_ID',
        '$emp_Fname',
        '$emp_Lname',
        '$emp_Username',
        '$emp_Password',
        '$emp_gender',
        '$emp_bDate',
        '$emp_Phone',
        '$empCountLock',
        '$emp_department',
        '$emp_role',
        '$emp_stalD'
    )";

    $result = mysqli_query($condb, $sql) or die("Error in query: $sql " . mysqli_error($condb) . "<br>$sql");

    if ($result) {
        echo "<script type='text/javascript'>";
        echo "window.location = 'employee.php?employee_add=employee_add'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "window.location = 'employee.php?mem_add_error=mem_add_error'; ";
        echo "</script>";
    }

}

if (isset($_GET['id'])) {
    $empID = $_GET['id'];
    $query = "SELECT empID, empFname, empLname, empGender, empPhone, emp_roleID, emp_depID, emp_stalD ,empBdate
              FROM employee WHERE empID = '$empID'";
    $result = mysqli_query($condb, $query);
    $row = mysqli_fetch_assoc($result);
    
    echo json_encode($row);
    exit();
}






if ($_POST['employee'] == 'Edit') {
    $empPassword = $_POST['emp_Password'];
    if($empPassword != ""){
        $empID = $_POST['empID'];
        $empFname = $_POST['emp_Fname'];
        $empLname = $_POST['emp_Lname'];
        $emp_PasswordHash = mysqli_real_escape_string($condb, sha1($empPassword));
        $empGender = $_POST['emp_gender'];
        $empbdate = $_POST['emp_bDate2'];
        $empPhone = $_POST['emp_Phone'];
        $empDepartment = $_POST['emp_department2'];
        $empRole = $_POST['emp_role2'];
        $empStatus = $_POST['emp_status2'];

        $query = "UPDATE employee 
                SET empFname = '$empFname', empLname = '$empLname', empPassword = '$emp_PasswordHash', empGender = '$empGender', empBdate = '$empbdate', empPhone = '$empPhone', emp_roleID = '$empRole', emp_depID = '$empDepartment' , emp_stalD = '$empStatus'
                WHERE empID = '$empID'";
        
        $result = mysqli_query($condb, $query) or die("Error in query: $query " . mysqli_error($condb) . "<br>$query");
        if ($result) {
            echo "<script type='text/javascript'>";
            echo "window.location = 'employee.php?employee_edit=employee_edit'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "window.location = 'employee.php?mem_add_error=mem_add_error'; ";
            echo "</script>";
        }
    }else{
        $empID = $_POST['empID'];
        $empFname = $_POST['emp_Fname'];
        $empLname = $_POST['emp_Lname'];
        $empGender = $_POST['emp_gender'];
        $empbdate = $_POST['emp_bDate2'];
        $empPhone = $_POST['emp_Phone'];
        $empDepartment = $_POST['emp_department2'];
        $empRole = $_POST['emp_role2'];
        $empStatus = $_POST['emp_status2'];

        $query = "UPDATE employee 
                SET empFname = '$empFname', empLname = '$empLname', empGender = '$empGender', empBdate = '$empbdate', empPhone = '$empPhone', emp_roleID = '$empRole', emp_depID = '$empDepartment' , emp_stalD = '$empStatus'
                WHERE empID = '$empID'";
        
        $result = mysqli_query($condb, $query) or die("Error in query: $query " . mysqli_error($condb) . "<br>$query");
        if ($result) {
            echo "<script type='text/javascript'>";
            echo "window.location = 'employee.php?employee_edit=employee_edit'; ";
            echo "</script>";
        } else {
            echo "<script type='text/javascript'>";
            echo "window.location = 'employee.php?mem_add_error=mem_add_error'; ";
            echo "</script>";
        }
    }

    
}



?>