<?php 
include('../condb.php');
// echo "<pre>";
// print_r($_POST);
// print_r($_FILES);
// echo "</pre>";
// exit();


if (isset($_POST['member']) && $_POST['member']=="add") {

    $mem_sid = mysqli_real_escape_string($condb, $_POST["mem_sid"]);
    $mem_cid = mysqli_real_escape_string($condb, $_POST["mem_cid"]);
    $userEmpFname = mysqli_real_escape_string($condb, $_POST["userEmpFname"]);
    $mem_dep_id = mysqli_real_escape_string($condb, $_POST["dep_id"]);
    $mem_password = mysqli_real_escape_string($condb, sha1($_POST["mem_cid"]));
    $mem_username = mysqli_real_escape_string($condb, $_POST["mem_sid"]);

    $sql = "INSERT INTO member
    (
        mem_sid,
        mem_cid,
        userEmpFname,
        mem_dep_id,
        mem_password,
        mem_username
    )
    VALUES
    (
        '$mem_sid',
        '$mem_cid',
        '$userEmpFname',
        '$mem_dep_id',
        '$mem_password',
        '$mem_username'
    )";

    $result = mysqli_query($condb, $sql) or die("Error in query: $sql " . mysqli_error($condb) . "<br>$sql");

    if ($result) {
        echo "<script type='text/javascript'>";
        echo "window.location = 'list_mem.php?mem_add=mem_add'; ";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "window.location = 'list_mem.php?mem_add_error=mem_add_error'; ";
        echo "</script>";
    }



} elseif (isset($_POST['member']) && $_POST['member']=="edit") {

    $userEmpID = mysqli_real_escape_string($condb, $_POST["userEmpID"]);
    $mem_sid = mysqli_real_escape_string($condb, $_POST["mem_sid"]);
    $mem_cid = mysqli_real_escape_string($condb, $_POST["mem_cid"]);
    $userEmpFname = mysqli_real_escape_string($condb, $_POST["userEmpFname"]);
    $mem_dep_id = mysqli_real_escape_string($condb, $_POST["dep_id"]);
    $mem_username = mysqli_real_escape_string($condb, $_POST["mem_sid"]);


    $sql = "UPDATE member SET 
    mem_sid = '$mem_sid',
    mem_cid = '$mem_cid',
    userEmpFname = '$userEmpFname',
    mem_dep_id = '$mem_dep_id',
    mem_username = '$mem_username'
    WHERE userEmpID='$userEmpID'" ;

    $result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");
	

    if($result){
    echo "<script type='text/javascript'>";
    echo "window.location = 'list_mem.php?mem_edit=mem_edit'; ";
    echo "</script>";
    }else{
    }
    


}elseif (isset($_GET['member']) && $_GET['member']=="del"){ 

	$userEmpID  = mysqli_real_escape_string($condb,$_GET["userEmpID"]);
	$sql ="UPDATE member SET mem_status = '2' WHERE userEmpID= $userEmpID";
	$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");
	
	if($result){
	echo "<script type='text/javascript'>";
	echo "window.location = 'list_mem.php?mem_del=mem_del'; ";
	echo "</script>";
    }

}elseif (isset($_GET['member']) && $_GET['member']=="status"){ 

	$userEmpID  = mysqli_real_escape_string($condb,$_GET["userEmpID"]);
    $mem_status  = mysqli_real_escape_string($condb,$_GET["mem_status"]);
	$sql ="UPDATE member SET mem_status = $mem_status WHERE member.userEmpID= $userEmpID";
	$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");
	
	if($result){
	echo "<script type='text/javascript'>";
	echo "window.location = 'list_mem.php?mem_status=mem_status'; ";
	echo "</script>";
    }

} elseif (isset($_POST['member']) && $_POST['member']=="rePass") {

    $userEmpID = mysqli_real_escape_string($condb, $_POST["userEmpID"]);
    $query_mem = "SELECT * FROM member WHERE userEmpID = $userEmpID;";

    $rs_mem = mysqli_query($condb, $query_mem);
    $row=mysqli_fetch_array($rs_mem);
    $mem_password=  sha1($row['mem_cid']) ;

    $sql = "UPDATE member SET 
    mem_password = '$mem_password'
    WHERE userEmpID='$userEmpID'" ;

    $result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");
	

    if($result){
    echo "<script type='text/javascript'>";
    echo "window.location = 'list_mem.php?admin_rePass=admin_rePass'; ";
    echo "</script>";
    }
    
} elseif (isset($_POST['member']) && $_POST['member']=="editPass") {

    $userEmpID = mysqli_real_escape_string($condb, $_POST["userEmpID"]);
    $old_password_input = sha1(mysqli_real_escape_string($condb, $_POST["mem_pass"]));
    $new_password = sha1(mysqli_real_escape_string($condb, $_POST["mem_newpass"]));
    $confirm_new_password = sha1(mysqli_real_escape_string($condb, $_POST["mem_newpass2"]));

    $query_mem = "SELECT * FROM member WHERE userEmpID = $userEmpID;";
    $rs_mem = mysqli_query($condb, $query_mem);
    $row = mysqli_fetch_array($rs_mem);
    $current_password = $row['mem_password'];


    if ($old_password_input != $current_password) {
        echo "<script>alert('รหัสผ่านเก่าไม่ถูกต้อง'); window.history.back();</script>";
        exit();
    }

    if ($new_password != $confirm_new_password) {
        echo "<script>alert('รหัสผ่านใหม่ไม่ตรงกัน'); window.history.back();</script>";
        exit();
    }

    $sql = "UPDATE member SET 
    mem_password = '$new_password'
    WHERE userEmpID = '$userEmpID'";

    $result = mysqli_query($condb, $sql) or die("Error in query: $sql " . mysqli_error($condb) . "<br>$sql");

    if ($result) {
        echo "<script type='text/javascript'>";
        echo "window.location = 'profile.php?mem_rePass=mem_rePass'; ";
        echo "</script>";
    }
    
}

?>