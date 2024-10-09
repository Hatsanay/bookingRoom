<?php 
$active = "report";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$query = "
SELECT 
  EMPLOYEE.EMPFNAME || ' ' || EMPLOYEE.EMPLNAME AS EMPLOYEE_NAME,
  COUNT(RESERVEROOM.RESERVEID) AS TOTAL_RESERVATIONS,
  SUM(CASE WHEN RESERVEROOM.RESERVEL_BOOKINGSTATUSID = 'STA0000011' THEN 1 ELSE 0 END) AS TOTAL_USED,
  SUM(CASE WHEN RESERVEROOM.RESERVEL_BOOKINGSTATUSID = 'STA0000008' THEN 1 ELSE 0 END) AS TOTAL_CANCELED,
  SUM(CASE WHEN RESERVEROOM.RESERVEL_BOOKINGSTATUSID = 'STA0000007' THEN 1 ELSE 0 END) AS TOTAL_RESERVE
FROM 
EMPLOYEE
  LEFT JOIN 
  RESERVEROOM ON EMPLOYEE.EMPID = RESERVEROOM.RESERVEL_EMPID
  GROUP BY 
  EMPLOYEE.EMPFNAME, EMPLOYEE.EMPLNAME
  ORDER BY 
  EMPLOYEE.EMPFNAME, EMPLOYEE.EMPLNAME";

$result = oci_parse($condb, $query);
oci_execute($result);
?>

<br>
<!-- Main content -->
<section class="content">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">รายงานการจองและการใช้งานจริง</h3>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>ชื่อพนักงาน</th>
                                        <th>จำนวนการจองทั้งหมด</th>
                                        <th>จำนวนการจองที่ยังไม่ใช้งาน</th>
                                        <th>จำนวนที่ใช้งานจริง</th>
                                        <th>จำนวนที่ยกเลิก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = oci_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>{$row['EMPLOYEE_NAME']}</td>";
                                        echo "<td>{$row['TOTAL_RESERVATIONS']}</td>";
                                        echo "<td>{$row['TOTAL_RESERVE']}</td>";
                                        echo "<td>{$row['TOTAL_USED']}</td>";
                                        echo "<td>{$row['TOTAL_CANCELED']}</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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