<?php 
$active = "report";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
 
if (substr($permistion, 8, 1) != "1") {
    session_destroy();
    header("Location: ../logout.php");
    exit();
}

?>

<br>
<!-- Main content -->
<section class="content">

    <div class="card-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card bg-info">
                        <div class="card-body">
                            <h5 class="card-title">รายงานการใช้งานห้อง Meeting</h5>
                            <p class="card-text">เปรียบเทียบในแต่ละวัน ในเดือนที่เลือก ในห้องที่เลือก</p>
                            <a href="report_meeting_usage.php" class="btn btn-light">ดูรายงาน</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card bg-success">
                        <div class="card-body">
                            <h5 class="card-title">รายงานการจองและการใช้งานจริง</h5>
                            <p class="card-text">เปรียบเทียบกับการใช้งานจริงและการยกเลิก</p>
                            <a href="report_booking_vs_usage.php" class="btn btn-light">ดูรายงาน</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">รายงานสถิติการถูก Lock</h5>
                            <p class="card-text">ดูจำนวนการถูก Lock ของพนักงานแต่ละคน</p>
                            <a href="report_employee_lock.php" class="btn btn-light">ดูรายงาน</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





</section>
<!-- /.content -->



<?php include('footer.php'); ?>
<script>
$(function() {
    $(".datatable").DataTable();
    $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
    });
});
</script>