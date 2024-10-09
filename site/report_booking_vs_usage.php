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
HAVING 
  COUNT(RESERVEROOM.RESERVEID) > 0
ORDER BY 
  EMPLOYEE.EMPFNAME, EMPLOYEE.EMPLNAME";

$result = oci_parse($condb, $query);
oci_execute($result);

$totalUsedSum = 0;
$totalCanceledSum = 0;
while ($row = oci_fetch_assoc($result)) {
    $totalUsedSum += $row['TOTAL_USED'];
    $totalCanceledSum += $row['TOTAL_CANCELED'];
    $data[] = $row;
}

oci_execute($result);
?>
<br>
<section class="content">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">รายงานการจองและการใช้งานจริง</h3>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-success" onclick="exportPDF()">
                            <i class="fas fa-file-pdf" style="margin-right: 8px;"></i>Export to PDF
                        </button>
                    </div>
                </div>
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="table-tab" data-toggle="tab" href="#table" role="tab"
                            aria-controls="table" aria-selected="true">ตาราง</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="chart-tab" data-toggle="tab" href="#chart" role="tab"
                            aria-controls="chart" aria-selected="false">กราฟ</a>
                    </li>
                </ul>
                <div class="tab-content" id="reportTabsContent">
                    <div class="tab-pane fade show active" id="table" role="tabpanel" aria-labelledby="table-tab">
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
                                            foreach ($data as $row) {
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
                    <div class="tab-pane fade" id="chart" role="tabpanel" aria-labelledby="chart-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <canvas id="reservationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function exportPDF() {
    const {
        jsPDF
    } = window.jspdf;
    const element = document.querySelector('.datatable');

    html2canvas(element).then((canvas) => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('l', 'mm', 'a4');
        const imgWidth = 295;
        const pageHeight = 210;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;
        let position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save('reservation_report.pdf');
    });
}

$(function() {
    $(".datatable").DataTable();

    const ctx = document.getElementById('reservationChart').getContext('2d');
    const reservationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['จำนวนที่ใช้งานจริง', 'จำนวนที่ยกเลิก'],
            datasets: [{
                label: 'จำนวน',
                data: [<?php echo $totalUsedSum; ?>, <?php echo $totalCanceledSum; ?>],
                backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }
        }
    });
});
</script>