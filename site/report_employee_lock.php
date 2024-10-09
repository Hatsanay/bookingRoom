<?php 
$active = "report";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$query_department = "SELECT DEPID, DEPNAME FROM DEPARTMENT";
$rs_department = oci_parse($condb, $query_department);
oci_execute($rs_department);

$selected_department = isset($_POST['selected_department']) ? $_POST['selected_department'] : '';

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
                                <select id="selected_department" name="selected_department" class="form-control" required>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">ค้นหา</button>
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

                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>ชื่อพนักงาน</th>
                                        <th>แผนก</th>
                                        <th>จำนวนครั้งที่ถูก Lock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    if ($selected_department) {
                                        $query = "
                                        SELECT
                                            EMPLOYEE.EMPFNAME || ' ' || EMPLOYEE.EMPLNAME AS EMPLOYEE_NAME,
                                            DEPARTMENT.DEPNAME AS DEPARTMENT_NAME,
                                            COUNT(LOCK_TABLE.LOCKID) AS TOTAL_LOCKS
                                        FROM 
                                            EMPLOYEE
                                            INNER JOIN DEPARTMENT ON DEPARTMENT.DEPID = EMPLOYEE.EMP_DEPID
                                            LEFT JOIN LOCK_TABLE ON EMPLOYEE.EMPID = LOCK_TABLE.LOCK_EMPID
                                        WHERE 
                                            DEPARTMENT.DEPID = :selected_department
                                        GROUP BY 
                                            EMPLOYEE.EMPFNAME, EMPLOYEE.EMPLNAME, DEPARTMENT.DEPNAME
                                        HAVING 
                                            COUNT(LOCK_TABLE.LOCKID) > 0
                                        ORDER BY 
                                            EMPLOYEE.EMPFNAME, EMPLOYEE.EMPLNAME";

                                        $result = oci_parse($condb, $query);
                                        oci_bind_by_name($result, ':selected_department', $selected_department);
                                        oci_execute($result);

                                        $hasData = false;
                                        while ($row = oci_fetch_assoc($result)) {
                                            $hasData = true;
                                            echo "<tr>";
                                            echo "<td>{$row['EMPLOYEE_NAME']}</td>";
                                            echo "<td>{$row['DEPARTMENT_NAME']}</td>";
                                            echo "<td>{$row['TOTAL_LOCKS']}</td>";
                                            echo "</tr>";
                                        }


                                        if (!$hasData) {
                                            echo "<tr><td colspan='3'>ไม่มีข้อมูลสำหรับแผนกที่เลือก</td></tr>";
                                        }
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
<!-- /.content -->

<?php include('footer.php'); ?>
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
</script>
