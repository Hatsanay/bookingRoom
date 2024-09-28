<?php 
    // error_reporting(0);
    // $condb = mysqli_connect("localhost","root","","bookingroomdb") 
    // or die ("Error :".mysqli_error($condb));
    // mysqli_query($condb,"SET NAMES UTF8");
    // date_default_timezone_set('Asia/Bangkok');

    $db_username = "db671036";
    $db_password = "10247";
    $db_connection_string = "//203.188.54.7:1521/database";
    putenv('NLS_LANG=THAI_THAILAND.UTF8');
    $condb = oci_connect($db_username, $db_password, $db_connection_string);
?>
