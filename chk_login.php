
<?php 
// echo "<pre>";
// print_r($_POST);
// echo "<pre>";
// exit();

session_start();
if($_POST['user_username']==null){
  $_SESSION['erroruser']="erroruser";
  Header("Location: index.php");
}
elseif($_POST['user_password']==null){
  $_SESSION['errorpass']="errorpass";
  Header("Location: index.php");
}
elseif(isset($_POST['user_username'])){
          include("condb.php");

          $user_username = mysqli_real_escape_string($condb,$_POST['user_username']);
          $user_password = mysqli_real_escape_string($condb,sha1($_POST['user_password']));

          $chk = trim($user_username) OR trim($user_password);
          if($chk==''){
            $_SESSION['error']="error";
              echo "<script> window.location='index.php'</script>";
            }
            else{
                      $sql="SELECT * FROM employee
                      INNER JOIN role on role.roleID = employee.emp_roleID
                      INNER JOIN department on department.depID = employee.emp_depID
                      INNER JOIN status on status.staID = employee.emp_stalD
                      WHERE empUserName='".$user_username."' 
                      AND empPassword='".$user_password."'";
                      $result = mysqli_query($condb,$sql);
                    
                      if(mysqli_num_rows($result)==1){
                          $row = mysqli_fetch_array($result);
                            $_SESSION["userEmpID"] = $row["empID"];
                            $_SESSION["userEmpFname"] = $row["empFname"];
                            $_SESSION["userEmpLname"] = $row["empLname"];
                            $_SESSION["userRoleName"] = $row["roleName"];
                            $_SESSION["userRoleaccess"] = $row["roleaccess"];
                              Header("Location: site/");           
                      }else{
                        $_SESSION['error']="error";
                        echo "<script> window.location='index.php'</script>";
                      }
            }//close else chk trim
            //exit();
}else{
      Header("Location: index.php"); //user & user_password incorrect back to login again
}
?>