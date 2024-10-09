<?php 
$active = "lockEmp";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (substr($permistion, 3, 1) != "1") {
    session_destroy();
    header("Location: ../logout.php");
    exit();
}

$emp_ID = $_SESSION['userEmpID'];

//ข้อมูลพนักงานที่โดน lock
$query_emplock = "SELECT 
empID AS \"empID\",
LOCK_TABLE.LOCKID AS \"lockID\",
empFname || ' ' || empLname AS \"fullname\",
empGender AS \"empGender\",
empPhone AS \"empPhone\",
role.roleName AS \"emprole\",
EXTRACT(YEAR FROM SYSDATE) - EXTRACT(YEAR FROM empBdate) AS \"age\",
empBdate AS \"empBdate\",
department.depName AS \"empdepartment\",
LOCK_TABLE.LOCKDATE AS \"lockdate\"
FROM
employee
INNER JOIN role ON role.roleID = employee.emp_roleID
INNER JOIN department ON department.depID = employee.emp_depID
INNER JOIN status ON status.staID = employee.emp_stalD
INNER JOIN LOCK_TABLE ON LOCK_TABLE.LOCK_EMPID = employee.EMPID

WHERE LOCK_TABLE.LOCK_STAID = 'STA0000009'";
$rs_emplock = oci_parse($condb, $query_emplock);
oci_execute($rs_emplock);

//ข้อมูลพนักงานที่เคยโดน lock
$query_emplockHistory = "SELECT 
employee.empID AS \"empID\",
LOCK_TABLE.LOCKID AS \"lockID\",
employee.empFname || ' ' || empLname AS \"fullname\",
employee.empGender AS \"empGender\",
employee.empPhone AS \"empPhone\",
role.roleName AS \"emprole\",
EXTRACT(YEAR FROM SYSDATE) - EXTRACT(YEAR FROM empBdate) AS \"age\",
 employee.empBdate AS \"empBdate\",
department.depName AS \"empdepartment\",
LOCK_TABLE.LOCKDATE AS \"lockdate\",
ls.STANAME AS \"lockstatus\"
FROM 
LOCK_TABLE
INNER JOIN EMPLOYEE ON EMPLOYEE.EMPID = LOCK_TABLE.LOCK_EMPID
INNER JOIN role ON role.roleID = employee.emp_roleID
INNER JOIN department ON department.depID = employee.emp_depID
INNER JOIN status ON status.staID = employee.emp_stalD
INNER JOIN status ls ON ls.staID = LOCK_TABLE.LOCK_STAID
--    INNER JOIN LOCK_TABLE ON LOCK_TABLE.LOCK_EMPID = employee.EMPID
WHERE LOCK_TABLE.LOCK_STAID = 'STA0000010'";
$rs_emplockHistory = oci_parse($condb, $query_emplockHistory);
oci_execute($rs_emplockHistory);

?>

<br>
<!-- Main content -->
<section class="content">

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">ล็อกพนักงาน
                <button class="btn btn-danger btn-edit" data-id="<?php echo $row_reserve['reserveID']; ?>"
                    data-toggle="modal" data-target="#reserveCancelModal">
                    <i class="fas fa-lock"></i> ประวัติการปลดล็อกพนักงาน
                </button>
            </h3>
        </div>

        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body table-responsive p-0">
                            <table id="example1" class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr class="danger">
                                        <th>No.</th>
                                        <th>รหัสการล็อก</th>
                                        <th>ชื่อ-สกุล</th>
                                        <th>เพศ</th>
                                        <th>อายุ</th>
                                        <th>เบอร์โทร</th>
                                        <th>ตำแหน่ง</th>
                                        <th>แผนก</th>
                                        <th>วันที่ล็อก</th>
                                        <th>ปลดล็อก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $l = 0;
                                    while ($row_emplock = oci_fetch_assoc($rs_emplock)) { ?>
                                    <tr>
                                        <td><?php echo ++$l; ?></td>
                                        <td><?php echo $row_emplock['lockID']; ?></td>
                                        <td><?php echo $row_emplock['fullname']; ?></td>
                                        <td>
                                          <?php
                                            if($row_emplock['empGender']=="M"){
                                              echo "ชาย";
                                            }else{
                                                echo "หญิง";
                                            }
                                          ?>
                                        </td>
                                        <td><?php echo $row_emplock['age']; ?></td>
                                        <td><?php echo $row_emplock['empPhone']; ?></td>
                                        <td><?php echo $row_emplock['emprole']; ?></td>
                                        <td><?php echo $row_emplock['empdepartment']; ?></td>
                                        <td><?php echo $row_emplock['lockdate']; ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-edit"
                                                data-lockid="<?php echo $row_emplock['lockID']; ?>"
                                                data-id="<?php echo $row_emplock['empID']; ?>" data-toggle="modal"
                                                data-target="#reserveDeleteModal">
                                                <i class="fas fa-unlock"></i> ปลดล็อก
                                            </button>
                                        </td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal สำหรับยกเลิกการจอง -->
<div class="modal fade" id="reserveDeleteModal" tabindex="-1" role="dialog" aria-labelledby="reserveDeleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="lockEmp_db.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="lock" value="emplock">
            <input type="hidden" name="empID" id="empID">
            <input type="hidden" name="lockID" id="lockID">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="reserveDeleteModalLabel">คุณแน่ใจที่จะปลดล็อกหรือไม่ ID:</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> ยืนยัน</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="reserveCancelModal" tabindex="-1" role="dialog" aria-labelledby="reserveCancelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="reserveCancelModalLabel">ประวัติการปลดล็อกพนักงาน</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body table-responsive p-0">
                                    <table id="example1" class="table table-head-fixed text-nowrap">
                                        <thead>
                                            <tr class="danger">
                                                <th>No.</th>
                                                <th>รหัสการล็อก</th>
                                                <th>ชื่อ-สกุล</th>
                                                <th>เพศ</th>
                                                <th>อายุ</th>
                                                <th>เบอร์โทร</th>
                                                <th>ตำแหน่ง</th>
                                                <th>แผนก</th>
                                                <th>วันที่ล็อก</th>
                                                <th>สถานะ</th>
                                            </tr>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                             $l = 0;
                                             while ($row_emplockHistory = oci_fetch_assoc($rs_emplockHistory)) { ?>
                                            <tr>
                                                <td><?php echo ++$l; ?></td>
                                                <td><?php echo $row_emplockHistory['lockID']; ?></td>
                                                <td><?php echo $row_emplockHistory['fullname']; ?></td>
                                                <td>
                                                  <?php
                                                    if($row_emplockHistory['empGender']=="M"){
                                                      echo "ชาย";
                                                    }else{
                                                        echo "หญิง";
                                                    }
                                                  ?>
                                                </td>
                                                <td><?php echo $row_emplockHistory['age']; ?></td>
                                                <td><?php echo $row_emplockHistory['empPhone']; ?></td>
                                                <td><?php echo $row_emplockHistory['emprole']; ?></td>
                                                <td><?php echo $row_emplockHistory['empdepartment']; ?></td>
                                                <td><?php echo $row_emplockHistory['lockdate']; ?></td>
                                                <td><?php echo $row_emplockHistory['lockstatus']; ?></td>
                                            </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card-body table-responsive p-0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
<script>
$(document).ready(function() {
    $('#reserveDeleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var empID = button.data('id');
        var lockid = button.data('lockid'); // เพิ่มส่วนนี้เพื่อนำ lockID มาใช้งาน

        console.log("Reserve ID in Modal: ", empID);
        console.log("lockid ID in Modal: ", lockid); // แสดง lockID ใน console

        var modal = $(this);
        modal.find('.modal-title').text('คุณแน่ใจที่จะปลดล็อกหรือไม่ ID: ' + lockid);
        modal.find('#empID').val(empID); // กำหนด empID
        modal.find('#lockID').val(lockid); // กำหนด lockID
    });

    $('form').on('submit', function(event) {
        console.log("Submitting form with reserveID: ", $('#empID').val());
        console.log("Submitting form with lockID: ", $('#lockID').val());
    });
});
</script>
</body>

</html>