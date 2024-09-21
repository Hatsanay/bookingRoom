<?php 
$active = "employee";
include("header.php");
session_start();

if (substr($permistion, 4, 1) != "1") {
    session_destroy();
    header("Location: ../logout.php");
    exit();
}

$query_mem = "SELECT
    empID,
    CONCAT(empFname, ' ', empLname) AS fullname,
    empGender,
    TIMESTAMPDIFF(YEAR, empBdate, CURDATE()) AS age,
    empPhone,
    role.roleName AS emprole,
    department.depName AS empdepartment
FROM 
    employee
    INNER JOIN role on role.roleID = employee.emp_roleID
    INNER JOIN department on department.depID = employee.emp_depID
    INNER JOIN status on status.staID = employee.emp_stalD ;" ;

// Where member.mem_status != 2 and member.mem_level != 0;" ;
$rs_mem = mysqli_query($condb, $query_mem);
?>


<br>
<!-- Main content -->
<section class="content">

    <div class="card card-primary">
        <div class="card-header ">
            <h3 class="card-title">จัดการพนักงาน</h3>
            <div align="right">

                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#employeeModal"><i
                        class="fa fa-plus"></i> เพิ่มข้อมูลพนักงาน</button>

            </div>
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
                                        <th>รหัสพนักงาน</th>
                                        <th>ชื่อ-สกุล</th>
                                        <th>เพศ</th>
                                        <th>อายุ</th>
                                        <th>เบอร์โทร</th>
                                        <th>แผนก</th>
                                        <th>ตำแหน่ง</th>
                                        <th>แก้ไข</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rs_mem as $row_mem) { ?>


                                    <tr>
                                        <td><?php echo @$l+=1; ?></td>
                                        <td><?php echo $row_mem['empID']; ?></td>
                                        <td><?php echo $row_mem['fullname']; ?></td>
                                        <td><?php echo $row_mem['empGender']; ?></td>
                                        <td><?php echo $row_mem['age']; ?></td>
                                        <td><?php echo $row_mem['empPhone']; ?></td>
                                        <td><?php echo $row_mem['emprole']; ?></td>
                                        <td><?php echo $row_mem['empdepartment']; ?></td>
                                        <td>
                                            <p style="margin-bottom: 10px;">
                                                <a 
                                                    class="btn btn-warning"><i class="fas fa-pencil-alt"></i> แก้ไข</a>
                                            </p>

                                        </td>
                                        <!-- <td>
                                            <?php if($row_mem['mem_status']=='1'){ ?>
                                            <a href="mem_db.php?mem_id=<?php echo $row_mem['mem_id']; ?>&&member=status&&mem_status=0"
                                                class="btn btn-success"><i class=""></i>ปกติ</a>
                                            <?php } else {?>
                                            <a href="mem_db.php?mem_id=<?php echo $row_mem['mem_id']; ?>&&member=status&&mem_status=1"
                                                class="btn btn-secondary"><i class=""></i>ถูกระงับ</a>
                                            <?php }?>
                                        </td>

                                        <td>
                                            <p style="margin-bottom: 10px;">
                                                <a href="edit_mem.php?mem_id=<?php echo $row_mem['mem_id']; ?>"
                                                    class="btn btn-warning"><i class="fas fa-pencil-alt"></i> แก้ไข</a>
                                            </p>

                                        </td>
                                        <td><a href="mem_db.php?mem_id=<?php echo $row_mem['mem_id']; ?>&&member=del"
                                                onclick="return confirm('ต้องการลบข้อมูลนี้ใช่หรือไม่')"
                                                class="del-btn btn btn-danger"><i class="fas fas fa-trash"></i> ลบ</a>
                                        </td> -->

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

<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="departments" value="add">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="employeeModalLabel">เพิ่มข้อมูลสาขา</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">ชื่อ </label>
                        <div class="col-sm-10">
                            <input name="emp_Fname" type="text" required class="form-control" placeholder="ชื่อ"
                                minlength="3" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">นามสกุล </label>
                        <div class="col-sm-10">
                            <input name="emp_Lname" type="text" required class="form-control" placeholder="นามสกุล"
                                minlength="3" />
                        </div>
                    </div>

                    <!-- <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">ชื่อผู้ใช้ </label>
                        <div class="col-sm-10">
                            <input name="emp_Username" type="text" required class="form-control"
                                placeholder="ชื่อผู้ใช้" minlength="3" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">รหัสผ่าน </label>
                        <div class="col-sm-10">
                            <input name="emp_Username" type="password" required class="form-control"
                                placeholder="นามสกุล" minlength="3" />
                        </div>
                    </div> -->

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">เพศ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="gender" id="gender" required>
                                <option value="">-- เลือกเพศ --</option>
                                <option value="M">ชาย</option>
                                <option value="F">หญิง</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">วันเกิด</label>
                        <div class="col-sm-10">
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input"
                                    data-target="#reservationdate" placeholder="เลือกวันเกิด" />
                                <div class="input-group-append" data-target="#reservationdate"
                                    data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">เบอร์โทร </label>
                        <div class="col-sm-10">
                            <input name="emp_Lname" type="text" required class="form-control" placeholder="นามสกุล"
                                minlength="3" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">แผนก</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="department" id="department" required>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">ตำแหน่ง</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="role" id="role" required>
                            </select>
                        </div>
                    </div>




                    <!-- <div class="form-group row">
                  <label for="" class="col-sm-2 col-form-label">คณะ</label>
                  <div class="col-sm-10">
                  <select class="form-control select2" name="dep_fac_id" id="dep_fac_id" required>
                    <option value="">-- เลือกคณะ --</option>
                  </select>
                  </div>
                </div> -->
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> ยืนยัน</button>
                </div>
            </div>
        </form>
    </div>
</div>
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