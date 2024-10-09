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

$query_department = "SELECT DEPID, DEPNAME FROM DEPARTMENT";
$rs_department = oci_parse($condb, $query_department);
oci_execute($rs_department);

$selected_department = isset($_POST['selected_department']) ? $_POST['selected_department'] : '';

$employee_names = [];
$lock_counts = [];
$total_locks_for_department = 0;

if ($selected_department) {
    $query_total_locks = "
        SELECT COUNT(LOCK_TABLE.LOCKID) AS TOTAL_LOCKS
        FROM 
            EMPLOYEE
            INNER JOIN LOCK_TABLE ON EMPLOYEE.EMPID = LOCK_TABLE.LOCK_EMPID
        WHERE 
            EMPLOYEE.EMP_DEPID = :selected_department";
    
    $stmt_total_locks = oci_parse($condb, $query_total_locks);
    oci_bind_by_name($stmt_total_locks, ':selected_department', $selected_department);
    oci_execute($stmt_total_locks);
    $row_total_locks = oci_fetch_assoc($stmt_total_locks);
    $total_locks_for_department = $row_total_locks['TOTAL_LOCKS'] ?? 0;

    $query = "
        SELECT
            EMPLOYEE.EMPFNAME || ' ' || EMPLOYEE.EMPLNAME AS EMPLOYEE_NAME,
            COUNT(LOCK_TABLE.LOCKID) AS TOTAL_LOCKS
        FROM 
            EMPLOYEE
            INNER JOIN LOCK_TABLE ON EMPLOYEE.EMPID = LOCK_TABLE.LOCK_EMPID
        WHERE 
            EMPLOYEE.EMP_DEPID = :selected_department
        GROUP BY 
            EMPLOYEE.EMPFNAME, EMPLOYEE.EMPLNAME
        HAVING 
            COUNT(LOCK_TABLE.LOCKID) > 0
        ORDER BY 
            EMPLOYEE.EMPFNAME, EMPLOYEE.EMPLNAME";

    $stmt = oci_parse($condb, $query);
    oci_bind_by_name($stmt, ':selected_department', $selected_department);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $employee_names[] = $row['EMPLOYEE_NAME'];
        $lock_counts[] = (int)$row['TOTAL_LOCKS'];
    }
}
?>

<br>
<!-- Main content -->
<section class="content">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">รายงานสถิติการถูก Lock</h3>
        </div>

        <div class="card-body">
            <div class="container-fluid">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="selected_department">เลือกแผนก:</label>
                                <select id="selected_department" name="selected_department" class="form-control"
                                    required>
                                    <option value="">เลือกแผนก</option>
                                    <?php
                                    while ($row = oci_fetch_assoc($rs_department)) {
                                        $selected = ($selected_department == $row['DEPID']) ? 'selected' : '';
                                        echo "<option value='{$row['DEPID']}' {$selected}>{$row['DEPNAME']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fas fa-search" style="margin-right: 8px;"></i>ค้นหา
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-success form-control" onclick="exportPDF()">
                                <i class="fas fa-file-pdf" style="margin-right: 8px;"></i>Export to PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <?php if ($selected_department): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h5>ยอดรวมการถูกล็อกสำหรับแผนก:
                            <strong><?php echo number_format($total_locks_for_department); ?> ครั้ง</strong>
                        </h5>
                    </div>
                </div>
                <?php endif; ?>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="table-tab" data-toggle="tab" href="#table-content"
                            role="tab">ตาราง</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="chart-tab" data-toggle="tab" href="#chart-content" role="tab">กราฟ</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="table-content" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-bordered table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th>ชื่อพนักงาน</th>
                                                <th>จำนวนครั้งที่ถูก Lock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($employee_names)) {
                                                foreach ($employee_names as $index => $name) {
                                                    echo "<tr>";
                                                    echo "<td>{$name}</td>";
                                                    echo "<td>{$lock_counts[$index]}</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='2'>ไม่มีข้อมูลสำหรับแผนกที่เลือก</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="chart-content" role="tabpanel">
                        <canvas id="lockChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function() {
    $(".datatable").DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
    });
});

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

        pdf.save('employee_lock_report.pdf');
    });
}


$(document).ready(function() {
    var ctx = document.getElementById('lockChart').getContext('2d');
    var lockChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($employee_names); ?>,
            datasets: [{
                label: 'จำนวนการถูก Lock',
                data: <?php echo json_encode($lock_counts); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>