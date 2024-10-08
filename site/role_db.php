<?php
include('../condb.php');

if (isset($_POST['role']) && $_POST['role'] == "add") {

    $query_empId = "SELECT roleID FROM (SELECT roleID FROM role ORDER BY roleID DESC) WHERE ROWNUM = 1";
    $rs_id = oci_parse($condb, $query_empId);
    oci_execute($rs_id);
    $row = oci_fetch_assoc($rs_id);
    
    if ($row) {
        $rs_id = $row['ROLEID']; // Oracle จะคืนชื่อคีย์เป็นตัวพิมพ์ใหญ่
        $cha_ID = substr($rs_id, 0, 3); // 3 ตัวแรก
        $int_ID = substr($rs_id, 3); // หมายเลขที่เหลือ
        $new_int_ID = str_pad((int)$int_ID + 1, 7, '0', STR_PAD_LEFT); // เพิ่ม 1 และเติมศูนย์หน้า
        $role_ID = $cha_ID . $new_int_ID; // สร้าง roleID ใหม่
    } else {
        $role_ID = "ROL0000001"; // กรณีไม่มีข้อมูล
    }
    
    // รับค่าจากฟอร์ม
    $role_name = $_POST["role_name"];
    $role_staID = "STA0000003"; // สถานะ
    
    $check_query = "SELECT COUNT(*) AS ROLECOUNT FROM role WHERE roleName = :role_name";
    $stmt_check = oci_parse($condb, $check_query);
    oci_bind_by_name($stmt_check, ':role_name', $role_name);
    oci_execute($stmt_check);
    $row_check = oci_fetch_assoc($stmt_check);

    if ($row_check['ROLECOUNT'] > 0) {
        // ถ้า roleName ซ้ำ
        echo "<script type='text/javascript'>";
        echo "window.location = 'role.php?role_add_error=role_add_error'; ";
        echo "</script>";
    } else {

    $role_access1 = !empty($_POST["indexchecked"]) ? $_POST["indexchecked"] : '0';
    $role_access2 = !empty($_POST["reservechecked"]) ? $_POST["reservechecked"] : '0';
    $role_access3 = !empty($_POST["approvechecked"]) ? $_POST["approvechecked"] : '0';
    $role_access4 = !empty($_POST["lockEmpchecked"]) ? $_POST["lockEmpchecked"] : '0';
    $role_access5 = !empty($_POST["cancelchecked"]) ? $_POST["cancelchecked"] : '0';
    $role_access6 = !empty($_POST["employeechecked"]) ? $_POST["employeechecked"] : '0';
    $role_access7 = !empty($_POST["rolechecked"]) ? $_POST["rolechecked"] : '0';
    $role_access8 = !empty($_POST["roomchecked"]) ? $_POST["roomchecked"] : '0';
    $role_access9 = !empty($_POST["reportchecked"]) ? $_POST["reportchecked"] : '0';

    // รวมค่าทั้งหมดเข้าด้วยกัน
    $role_access = $role_access1 . $role_access2 . $role_access3 . 
                   $role_access4 . $role_access5 . $role_access6 . 
                   $role_access7 . $role_access8 . $role_access9;

   if ($role_access === '000000000') {
        echo "<script type='text/javascript'>";
        echo "window.location = 'role.php?role_add_error=role_add_error'; ";
        echo "</script>";
        } else {             
    // สร้างคำสั่ง SQL สำหรับ Oracle
    $sql = "INSERT INTO role (roleID, roleName, roleaccess, role_staID)
            VALUES ('$role_ID', '$role_name', '$role_access', '$role_staID')";

    // ทำการส่งคำสั่ง SQL
    $result = oci_parse($condb, $sql);
    $exec = oci_execute($result);
    
    if ($exec) {
        echo "<script type='text/javascript'>";
        echo "window.location = 'role.php?role_add=role_add'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "window.location = 'role.php?role_add_error=role_add_error'; ";
        echo "</script>";
    }
}
}
}
if (isset($_GET['id'])) {
    $roleID = $_GET['id'];
    $query = "SELECT roleID AS \"roleID\", rolename AS \"rolename\", roleaccess AS \"roleaccess\", role_staID AS \"role_staID\"
              FROM role WHERE roleID = :roleID";
    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':roleID', $roleID); //ใช้ oci_bind_by_name() เพื่อผูกค่าของตัวแปร $roleID เข้ากับพารามิเตอร์ :roleID ในคำสั่ง SQL.
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);

    echo json_encode($row); //ใช้ json_encode() เพื่อแปลงข้อมูลใน $row เป็นรูปแบบ JSON แล้วส่งออกไปยัง client
    exit();
}


if (isset($_POST['role']) && $_POST['role'] == 'Edit') {
    $roleID = $_POST['roleID'];
    $rolename = trim($_POST['role_name']); //ตัดค่าว่าง
    //$roleaccess = $_POST['role_access2'];
    $role_staID = $_POST['role_status2'];

    $check_query = "SELECT COUNT(*) AS ROLECOUNT FROM role WHERE roleName = :role_name";
    $stmt_check = oci_parse($condb, $check_query);
    oci_bind_by_name($stmt_check, ':role_name', $role_name);
    oci_execute($stmt_check);
    $row_check = oci_fetch_assoc($stmt_check);

    if ($row_check['ROLECOUNT'] > 0) {
        // ถ้า roleName ซ้ำ
        echo "<script type='text/javascript'>";
        echo "window.location = 'role.php?role_addSame_error=duplicate_role'; ";
        echo "</script>";
    }else{
    $role_access1 = !empty($_POST["indexchecked"]) ? $_POST["indexchecked"] : '0';
    $role_access2 = !empty($_POST["reservechecked"]) ? $_POST["reservechecked"] : '0';
    $role_access3 = !empty($_POST["approvechecked"]) ? $_POST["approvechecked"] : '0';
    $role_access4 = !empty($_POST["lockEmpchecked"]) ? $_POST["lockEmpchecked"] : '0';
    $role_access5 = !empty($_POST["cancelchecked"]) ? $_POST["cancelchecked"] : '0';
    $role_access6 = !empty($_POST["employeechecked"]) ? $_POST["employeechecked"] : '0';
    $role_access7 = !empty($_POST["rolechecked"]) ? $_POST["rolechecked"] : '0';
    $role_access8 = !empty($_POST["roomchecked"]) ? $_POST["roomchecked"] : '0';
    $role_access9 = !empty($_POST["reportchecked"]) ? $_POST["reportchecked"] : '0';

    // รวมค่าทั้งหมดเข้าด้วยกัน
    $role_access = $role_access1 . $role_access2 . $role_access3 . 
                   $role_access4 . $role_access5 . $role_access6 . 
                   $role_access7 . $role_access8 . $role_access9;

    if ($role_access === '00000000') {
        echo "<script type='text/javascript'>";
        echo "window.location = 'role.php?role_addNoaccess_error=no_access'; ";
        echo "</script>";
        }  else{ 

    $query = "UPDATE role 
            SET rolename = :rolename, roleaccess = :roleaccess, role_staID = :role_staID
            WHERE roleID = :roleID";
    $stmt = oci_parse($condb, $query);
 

    oci_bind_by_name($stmt, ':rolename', $rolename);
    oci_bind_by_name($stmt, ':roleaccess', $role_access);
    oci_bind_by_name($stmt, ':role_staID', $role_staID);
    oci_bind_by_name($stmt, ':roleID', $roleID);


    $result = oci_execute($stmt, OCI_NO_AUTO_COMMIT);

    if ($result) {
        oci_commit($condb);
        echo "<script type='text/javascript'>";
        echo "window.location = 'role.php?role_edit=role_edit'; ";
        echo "</script>";
    } else {
        $e = oci_error($stmt);
        echo "<script type='text/javascript'>";
        echo "alert('Error: " . htmlentities($e['message']) . "');";
        echo "window.location = 'role.php?role_edit_error=role_edit_error'; ";
        echo "</script>";
    }
}
}
}
?>