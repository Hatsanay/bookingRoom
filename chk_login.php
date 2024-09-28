<?php 
session_start();
function redirect($url) {
    header("Location: $url");
    exit();
}

if (empty($_POST['user_username'])) {
    $_SESSION['erroruser'] = "กรุณากรอกชื่อผู้ใช้";
    redirect("index.php");
}

if (empty($_POST['user_password'])) {
    $_SESSION['errorpass'] = "กรุณากรอกรหัสผ่าน";
    redirect("index.php");
}

include("condb.php");

$user_username = trim($_POST['user_username']);
$user_password = sha1(trim($_POST['user_password']));

$sql = "SELECT empID, empFname, empLname, roleName, roleaccess 
        FROM employee 
        INNER JOIN role ON role.roleID = employee.emp_roleID 
        INNER JOIN department ON department.depID = employee.emp_depID 
        INNER JOIN status ON status.staID = employee.emp_stalD 
        WHERE empUserName = :user_username AND empPassword = :user_password";

$result = oci_parse($condb, $sql);
oci_bind_by_name($result, ':user_username', $user_username);
oci_bind_by_name($result, ':user_password', $user_password);
oci_execute($result);

if ($row = oci_fetch_assoc($result)) {
    $_SESSION["userEmpID"] = $row["EMPID"];
    $_SESSION["userEmpFname"] = $row["EMPFNAME"];
    $_SESSION["userEmpLname"] = $row["EMPLNAME"];
    $_SESSION["userRoleName"] = $row["ROLENAME"];
    $_SESSION["userRoleaccess"] = $row["ROLEACCESS"];

    redirect("site/");
} else {
    $_SESSION['error'] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    redirect("index.php");
}

oci_free_statement($result);
oci_close($condb);
?>
