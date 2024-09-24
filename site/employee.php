<?php 
$active = "employee";
include("header.php");
session_start();

if (substr($permistion, 4, 1) != "1") {
    session_destroy();
    header("Location: ../logout.php");
    exit();
}
//ข้อมูลพนักงาน
$query_emp = "SELECT
    empID,
    CONCAT(empFname, ' ', empLname) AS fullname,
    empGender,
    empPhone,
    role.roleName AS emprole,
    TIMESTAMPDIFF(YEAR, empBdate, CURDATE()) AS age,
    department.depName AS empdepartment
FROM 
    employee
    INNER JOIN role on role.roleID = employee.emp_roleID
    INNER JOIN department on department.depID = employee.emp_depID
    INNER JOIN status on status.staID = employee.emp_stalD
    WHERE emp_stalD = 'STA0000001'
    ORDER BY empID ASC
     ;" ;
$rs_emp = mysqli_query($condb, $query_emp);

//ข้อมูลตำแหน่ง
$query_dept = "SELECT
    depID,
    depName
FROM 
    department;";
$rs_dept = mysqli_query($condb, $query_dept);


//ข้อมูลแผนก
$query_role = "SELECT
    roleID,
    roleName
FROM 
    role
    WHERE role_staID = 'STA0000003'
    ;";
$rs_role = mysqli_query($condb, $query_role);


//ข้อมูลตำแหน่ง
$query_deptEdit = "SELECT
    depID,
    depName
FROM 
    department;";
$rs_deptEdit = mysqli_query($condb, $query_deptEdit);


//ข้อมูลแผนก
$query_roleEdit = "SELECT
    roleID,
    roleName
FROM 
    role
    WHERE role_staID = 'STA0000003'
    ;";
$rs_roleEdit = mysqli_query($condb, $query_roleEdit);


//ข้อมูลสภานะ
$query_statusEdit = "SELECT
    staID,
    staName
FROM 
    status
    WHERE sta_statypID = 'STT0000001'
    ;";
$rs_statusEdit = mysqli_query($condb, $query_statusEdit);



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
                                        <th>ตำแหน่ง</th>
                                        <th>แผนก</th>
                                        <!-- <th>ดูเพิ่มเติม</th> -->
                                        <th>แก้ไข</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rs_emp as $row_emp) { ?>


                                    <tr>
                                        <td><?php echo @$l+=1; ?></td>
                                        <td><?php echo $row_emp['empID']; ?></td>
                                        <td><?php echo $row_emp['fullname']; ?></td>
                                        <td><?php
                                        if($row_emp['empGender']=="M"){
                                            echo "ชาย";
                                        }else{
                                            echo "หญิง";
                                        }
                                        ?></td>
                                        <td><?php echo $row_emp['age']; ?></td>
                                        <td><?php echo $row_emp['empPhone']; ?></td>
                                        <td><?php echo $row_emp['emprole']; ?></td>
                                        <td><?php echo $row_emp['empdepartment']; ?></td>
                                        <!-- <td>
                                            <a href="" class="btn btn-info" target="" data-toggle="modal"
                                                data-target="#Modal<?php echo $row_thesis['thesis_id'];?>"><i
                                                    class="fas fas fa-eye"></i></a>
                                            <?php 
                                            // include 'detail_thesis_modal.php';
                                            ?>
                                        </td> -->
                                        <td>
                                            <button class="btn btn-warning btn-edit"
                                                data-id="<?php echo $row_emp['empID']; ?>" data-toggle="modal"
                                                data-target="#employeeEditModal">
                                                <i class="fas fa-pencil-alt"></i> แก้ไข
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

<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="employee_db.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="employee" value="add">

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
                            <input name="emp_Password" type="password" required class="form-control"
                                placeholder="นามสกุล" minlength="3" />
                        </div>
                    </div> -->

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">เพศ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="emp_gender" id="emp_gender" required>
                                <option value="">-- เลือกเพศ --</option>
                                <option value="M">ชาย</option>
                                <option value="F">หญิง</option>
                            </select>
                        </div>
                    </div>

                    <!-- <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">วันเกิด</label>
                        <div class="col-sm-10">
                        <input  name="Ddate" type="date" required class="form-control" id="Ddate" value="" placeholder="วัน/เดือน/ปีเกิด" />
                        </div>
                    </div> -->


                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">เบอร์โทร </label>
                        <div class="col-sm-10">
                            <input name="emp_Phone" type="text" required class="form-control" placeholder="เบอร์โทร"
                                minlength="3" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">แผนก</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="emp_department" id="emp_department" required>
                                <option value="">เลือกแผนก</option>
                                <?php
                                    while ($row = mysqli_fetch_assoc($rs_dept)) {
                                        echo '<option value="' . $row['depID'] . '">' . $row['depName'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">ตำแหน่ง</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="emp_role" id="emp_role" required>
                                <option value="">เลือกตำแหน่ง</option>
                                <?php
                                    while ($row = mysqli_fetch_assoc($rs_role)) {
                                        echo '<option value="' . $row['roleID'] . '">' . $row['roleName'] . '</option>';
                                    }
                                    ?>
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



<div class="modal fade" id="employeeEditModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="employee_db.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="employee" value="Edit">
            <input type="hidden" name="empID" id="empID">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="employeeModalLabel">แก้ไขข้อมูลพนักงาน</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label for="emp_Fname" class="col-sm-2 col-form-label">ชื่อ</label>
                        <div class="col-sm-10">
                            <input name="emp_Fname" id="emp_Fname" type="text" required class="form-control"
                                placeholder="ชื่อ" minlength="3" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="emp_Lname" class="col-sm-2 col-form-label">นามสกุล</label>
                        <div class="col-sm-10">
                            <input name="emp_Lname" id="emp_Lname" type="text" required class="form-control"
                                placeholder="นามสกุล" minlength="3" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="emp_gender" class="col-sm-2 col-form-label">เพศ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="emp_gender" id="emp_gender" required>
                                <option value="M">ชาย</option>
                                <option value="F">หญิง</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="emp_Phone" class="col-sm-2 col-form-label">เบอร์โทร</label>
                        <div class="col-sm-10">
                            <input name="emp_Phone" id="emp_Phone" type="text" required class="form-control"
                                placeholder="เบอร์โทร" minlength="3" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="emp_department" class="col-sm-2 col-form-label">แผนก</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="emp_department2" id="emp_department2" required>
                                <option value="">เลือกแผนก</option>
                                <?php
                                    while ($row = mysqli_fetch_assoc($rs_deptEdit)) {
                                        echo '<option value="' . $row['depID'] . '">' . $row['depName'] . '</option>';
                                    }
                                ?>
                            </select>


                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="emp_role" class="col-sm-2 col-form-label">ตำแหน่ง</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="emp_role2" id="emp_role2" required>
                                <option value="">เลือกตำแหน่ง</option>
                                <?php
                                    while ($row = mysqli_fetch_assoc($rs_roleEdit)) {
                                        echo '<option value="' . $row['roleID'] . '">' . $row['roleName'] . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="emp_role" class="col-sm-2 col-form-label">สถานะ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="emp_status2" id="emp_status2" required>
                                <!-- <option value="">เลือกสภานะ</option> -->
                                <?php
                                    while ($row = mysqli_fetch_assoc($rs_statusEdit)) {
                                        echo '<option value="' . $row['staID'] . '">' . $row['staName'] . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

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
$(document).ready(function() {
    $('.btn-edit').click(function() {
        var empID = $(this).data('id');

        $.ajax({
            url: 'employee_db.php',
            type: 'GET',
            data: {
                id: empID
            },
            success: function(response) {
                console.log(response);
                var empData = JSON.parse(response);

                $('#empID').val(empData.empID);
                $('#emp_Fname').val(empData.empFname);
                $('#emp_Lname').val(empData.empLname);
                $('#emp_gender').val(empData.empGender).trigger('change');
                $('#emp_Phone').val(empData.empPhone);


                $('#emp_department2').val(empData.emp_depID).trigger('change');
                $('#emp_role2').val(empData.emp_roleID).trigger('change');
                $('#emp_status2').val(empData.emp_stalD).trigger('change');
            }
        });
    });
});
</script>