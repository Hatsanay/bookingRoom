<?php 
$active = "role";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (substr($permistion, 6, 1) != "1") {
  session_destroy();
  header("Location: ../logout.php");
  exit();
}
//ข้อมูลตำแหน่ง
$query_role = "SELECT
    roleID AS \"roleID\",
    rolename AS \"rolename\",
    roleaccess AS \"roleaccess\",
    status.staname AS \"staname\"
FROM 
    role
INNER JOIN status on status.staID = role.role_staID
WHERE role_staID = 'STA0000003'
ORDER BY roleID ASC
";
$rs_role = oci_parse($condb, $query_role);
oci_execute($rs_role);

//ข้อมูลสภานะสำหรับแก้ไข
$query_statusEdit = "SELECT
    staID AS \"staID\", 
    staName AS \"staName\"
FROM 
    status
    WHERE sta_statypID = 'STT0000002'
    ";
$rs_statusEdit = oci_parse($condb, $query_statusEdit);
oci_execute($rs_statusEdit);

//ข้อมูลสภานะสำหรับดูข้อมูลเพิ่มเติม
$query_statusView = "SELECT
    staID AS \"staID\", 
    staName AS \"staName\"
FROM 
    status
    WHERE sta_statypID = 'STT0000002'
    ";
$rs_statusView = oci_parse($condb, $query_statusView);
oci_execute($rs_statusView);

//ข้อมูลสิธการเข้าถึงสำหรับแก้ไข
$query_roleaccessEdit = "SELECT
	roleID,
  rolename ,
  roleaccess
FROM 
    role";
$rs_roleaccessEdit = oci_parse($condb, $query_roleaccessEdit);
oci_execute($rs_roleaccessEdit);
?>

<br>
  <!-- Main content -->
  <section class="content">

    <div class="card card-primary">
        <div class="card-header ">
            <h3 class="card-title">จัดการตำแหน่ง</h3>
            <div align="right">

                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#roleModal"><i
                        class="fa fa-plus"></i> เพิ่มข้อมูลตำแหน่ง</button>
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
                                        <th>รหัสตำแหน่ง</th>
                                        <th>ชื่อตำแหน่ง</th>
                                        <th>สถานะ</th>
                                        <th>เพิ่มเติม</th>
                                        <th>แก้ไข</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                        $l = 0;
                                        while ($row_role = oci_fetch_assoc($rs_role)) { 
                                        ?>
                                            <tr>
                                                <td><?php echo ++$l; ?></td>
                                                <td><?php echo isset($row_role['roleID']) ? $row_role['roleID'] : 'N/A'; ?></td>
                                                <td><?php echo isset($row_role['rolename']) ? $row_role['rolename'] : 'N/A'; ?></td>
                                                <td><?php echo isset($row_role['staname']) ? $row_role['staname'] : 'N/A'; ?></td>
                                                <!--<td>
                                                    <a href="" class="btn btn-info" target="" data-toggle="modal"
                                                        data-target="#Modal<?php echo $row_thesis['thesis_id'];?>"><i
                                                            class="fas fas fa-eye"></i></a>
                                                    <?php 
                                                    ?>
                                                </td>--> 
                                                <td>
                                                    <button class="btn btn-info"
                                                        data-id="<?php echo isset($row_role['roleID']) ? $row_role['roleID'] : ''; ?>" 
                                                        data-toggle="modal" data-target="#roleViewModal">
                                                        <i class="fas fas fa-eye"></i> เพิ่มเติม
                                                    </button>
                                                </td>
                                                <td>
                                                    <button class="btn btn-warning btn-edit"
                                                        data-id="<?php echo isset($row_role['roleID']) ? $row_role['roleID'] : ''; ?>" 
                                                        data-toggle="modal" data-target="#roleEditModal">
                                                        <i class="fas fa-pencil-alt"></i> แก้ไข
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- Modal สำหรับเพิ่มข้อมูลตำแหน่ง -->
  <div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="role_db.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="role" value="add">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-titlrole">เพิ่มข้อมูลตำแหน่ง</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">ชื่อตำแหน่ง </label>
                        <div class="col-sm-10">
                            <input name="role_name" type="text" required class="form-control" placeholder="ชื่อ"
                                minlength="3" />
                        </div>
                    </div>
                    <!--<div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">สถานะ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="role_status" id="role_status" required >
                                <option value="">ใช้งาน</option>
                                    ?>
                            </select>
                        </div>
                    </div>-->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll" onclick="toggleCheckboxes(this)">      
                        <label class="form-check-label" for="selectAll">
                            เลือกทั้งหมด / ยกเลิกทั้งหมด
                        </label>
                    </div>
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="indexchecked" id="indexchecked" checked>      
                            <label class="form-check-label" for="indexchecked">
                                รายการที่จอง
                            </label>
                    </div>
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name ="reservechecked">
                            <label class="form-check-label" for="reservechecked">
                                จองห้องประชุม
                            </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="approvechecked">
                            <label class="form-check-label" for="approvechecked">
                                อนุมัติการจอง
                            </label>
                    </div>
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name ="lockEmpchecked">
                            <label class="form-check-label" for="lockEmpchecked">
                                ล็อกพนักงาน
                            </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="cancelchecked">
                            <label class="form-check-label" for="cancelchecked">
                                ยกเลิก
                            </label>
                    </div>
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name ="employeechecked">
                            <label class="form-check-label" for="employeechecked">
                                พนักงาน
                            </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="rolechecked">
                            <label class="form-check-label" for="rolechecked">
                                ตำแหน่งและสิทธิ์
                            </label>
                    </div>
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name ="roomchecked">
                            <label class="form-check-label" for="roomchecked">
                                ห้องประชุม
                            </label>
                    </div>
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name ="reportchecked">
                            <label class="form-check-label" for="reportchecked">
                                รายงาน
                            </label>
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
  
<!-- Modal สำหรับแก้ไขข้อมูลตำแหน่ง -->
<div class="modal fade" id="roleEditModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="role_db.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="role" value="Edit">
            <input type="hidden" name="roleID" id="roleID">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="roleModalLabel">แก้ไขข้อมูลตำแหน่ง</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label for="role_name" class="col-sm-2 col-form-label">ชื่อ</label>
                        <div class="col-sm-10">
                            <input name="role_name" id="role_name" type="text" required class="form-control"
                                placeholder="ชื่อ" minlength="3" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="role_status2" class="col-sm-2 col-form-label">สถานะ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="role_status2" id="role_status2" required>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_statusEdit)) {
                                        echo '<option value="' . $row['staID'] . '">' . $row['staName'] . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll" onclick="toggleCheckboxes(this)">      
                        <label class="form-check-label" for="selectAll">
                            เลือกทั้งหมด / ยกเลิกทั้งหมด
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="indexchecked" >
                        <label class="form-check-label" for="indexchecked">
                            รายการที่จอง
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="reservechecked">
                        <label class="form-check-label" for="reservechecked">
                            จองห้องประชุม
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="approvechecked">
                        <label class="form-check-label" for="approvechecked">
                            อนุมัติการจอง
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="lockEmpchecked">
                        <label class="form-check-label" for="lockEmpchecked">
                            ล็อกพนักงาน
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="cancelchecked">
                        <label class="form-check-label" for="cancelchecked">
                            ยกเลิก
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="employeechecked">
                        <label class="form-check-label" for="employeechecked">
                            พนักงาน
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="rolechecked">
                        <label class="form-check-label" for="rolechecked">
                            ตำแหน่งและสิทธิ์
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="roomchecked">
                        <label class="form-check-label" for="roomchecked">
                            ห้องประชุม
                        </label>
                    </div>
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name ="reportchecked">
                            <label class="form-check-label" for="reportchecked">
                                รายงาน
                            </label>
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

<!-- Modal สำหรับดูข้อมูลตำแหน่ง -->
<div class="modal fade" id="roleViewModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="role_db.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="role" value="Edit">
            <input type="hidden" name="roleID" id="roleID">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="roleModalLabel">ข้อมูลตำแหน่ง</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label for="role_name2" class="col-sm-2 col-form-label">ชื่อ</label>
                        <div class="col-sm-10">
                            <input name="role_name2" id="role_name2" type="text" required class="form-control"
                                placeholder="ชื่อ" minlength="3" disabled/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="role_status3" class="col-sm-2 col-form-label">สถานะ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="role_status3" id="role_status3" required disabled>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_statusView)) {
                                        echo '<option value="' . $row['staID'] . '">' . $row['staName'] . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="indexchecked" disabled>
                        <label class="form-check-label" for="indexchecked">
                            รายการที่จอง
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="reservechecked" disabled>
                        <label class="form-check-label" for="reservechecked">
                            จองห้องประชุม
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="approvechecked" disabled>
                        <label class="form-check-label" for="approvechecked">
                            อนุมัติการจอง
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="lockEmpchecked" disabled>
                        <label class="form-check-label" for="lockEmpchecked">
                            ล็อกพนักงาน
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="cancelchecked" disabled>
                        <label class="form-check-label" for="cancelchecked">
                            ยกเลิก
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="employeechecked" disabled>
                        <label class="form-check-label" for="employeechecked">
                            พนักงาน
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="rolechecked" disabled>
                        <label class="form-check-label" for="rolechecked">
                            ตำแหน่งและสิทธิ์
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name ="roomchecked" disabled>
                        <label class="form-check-label" for="roomchecked">
                            ห้องประชุม
                        </label>
                    </div>
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name ="reportchecked" disabled>
                            <label class="form-check-label" for="reportchecked">
                                รายงาน
                            </label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    
                </div>
            </div>
        </form>
    </div>
</div>
  
  <?php include('footer.php'); ?>
  <script>
  $(document).ready(function() {
        // ฟังก์ชันทำงานเมื่อโมดัลถูกปิด
        $('#roleEditModal').on('click','hidden.bs.modal', function () {
            // ล้างค่าใน input field ที่มี id เป็น roleID
            $('#roleID').val('');
            $('#role_name').val('');  
            $('#role_status2').val('').trigger('change'); 
            //location.reload();
                    $('input[name="indexchecked"]').prop('checked', false);
                    $('input[name="reservechecked"]').prop('checked', false);
                    $('input[name="approvechecked"]').prop('checked', false);
                    $('input[name="lockEmpchecked"]').prop('checked', false);
                    $('input[name="cancelchecked"]').prop('checked', false);
                    $('input[name="employeechecked"]').prop('checked', false);
                    $('input[name="rolechecked"]').prop('checked', false);
                    $('input[name="roomchecked"]').prop('checked', false);
                    $('input[name="reportchecked"]').prop('checked', false);
        });
    });
  $(function () {
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
    $(document).on('click', '.btn-edit', function() {
        var roleID = $(this).data('id');

        $.ajax({
            url: 'role_db.php',
            type: 'GET',
            data: {
                id: roleID
            },
            success: function(response) {
                console.log(response);
                var roleData = JSON.parse(response);
                $('#roleID').val(roleData.roleID);
                $('#role_name').val(roleData.rolename);
                $('#role_status2').val(roleData.role_staID).trigger('change');
                var roleAccessArray = roleData.roleaccess.split(''); 
                // วนลูปเช็คค่า checkbox
                if (roleAccessArray[0] == '1') {
                    $('input[name="indexchecked"]').prop('checked', true);
                } else {
                    $('input[name="indexchecked"]').prop('checked', false);
                }

                if (roleAccessArray[1] == '1') {
                    $('input[name="reservechecked"]').prop('checked', true);
                } else {
                    $('input[name="reservechecked"]').prop('checked', false);
                }

                if (roleAccessArray[2] == '1') {
                    $('input[name="approvechecked"]').prop('checked', true);
                } else {
                    $('input[name="approvechecked"]').prop('checked', false);
                }

                if (roleAccessArray[3] == '1') {
                    $('input[name="lockEmpchecked"]').prop('checked', true);
                } else {
                    $('input[name="lockEmpchecked"]').prop('checked', false);
                }

                if (roleAccessArray[4] == '1') {
                    $('input[name="cancelchecked"]').prop('checked', true);
                } else {
                    $('input[name="cancelchecked"]').prop('checked', false);
                }

                if (roleAccessArray[5] == '1') {
                    $('input[name="employeechecked"]').prop('checked', true);
                } else {
                    $('input[name="employeechecked"]').prop('checked', false);
                }

                if (roleAccessArray[6] == '1') {
                    $('input[name="rolechecked"]').prop('checked', true);
                } else {
                    $('input[name="rolechecked"]').prop('checked', false);
                }

                if (roleAccessArray[7] == '1') {
                    $('input[name="roomchecked"]').prop('checked', true);
                } else {
                    $('input[name="roomchecked"]').prop('checked', false);
                }
                if (roleAccessArray[8] == '1') {
                    $('input[name="reportchecked"]').prop('checked', true);
                } else {
                    $('input[name="reportchecked"]').prop('checked', false);
                }
            }
        });
    });
});
$(document).ready(function() {
    $(document).on('click', '.btn-info', function()  {
        var roleID = $(this).data('id');

        $.ajax({
            url: 'role_db.php',
            type: 'GET',
            data: {
                id: roleID
            },
            success: function(response) {
                console.log(response);
                var roleData = JSON.parse(response);
                $('#roleID').val(roleData.roleID);
                $('#role_name2').val(roleData.rolename);
                $('#role_status3').val(roleData.role_staID).trigger('change');
                var roleAccessArray = roleData.roleaccess.split(''); 
                // วนลูปเช็คค่า checkbox
                if (roleAccessArray[0] == '1') {
                    $('input[name="indexchecked"]').prop('checked', true);
                } else {
                    $('input[name="indexchecked"]').prop('checked', false);
                }

                if (roleAccessArray[1] == '1') {
                    $('input[name="reservechecked"]').prop('checked', true);
                } else {
                    $('input[name="reservechecked"]').prop('checked', false);
                }

                if (roleAccessArray[2] == '1') {
                    $('input[name="approvechecked"]').prop('checked', true);
                } else {
                    $('input[name="approvechecked"]').prop('checked', false);
                }

                if (roleAccessArray[3] == '1') {
                    $('input[name="lockEmpchecked"]').prop('checked', true);
                } else {
                    $('input[name="lockEmpchecked"]').prop('checked', false);
                }

                if (roleAccessArray[4] == '1') {
                    $('input[name="cancelchecked"]').prop('checked', true);
                } else {
                    $('input[name="cancelchecked"]').prop('checked', false);
                }

                if (roleAccessArray[5] == '1') {
                    $('input[name="employeechecked"]').prop('checked', true);
                } else {
                    $('input[name="employeechecked"]').prop('checked', false);
                }

                if (roleAccessArray[6] == '1') {
                    $('input[name="rolechecked"]').prop('checked', true);
                } else {
                    $('input[name="rolechecked"]').prop('checked', false);
                }

                if (roleAccessArray[7] == '1') {
                    $('input[name="roomchecked"]').prop('checked', true);
                } else {
                    $('input[name="roomchecked"]').prop('checked', false);
                }
                if (roleAccessArray[8] == '1') {
                    $('input[name="reportchecked"]').prop('checked', true);
                } else {
                    $('input[name="reportchecked"]').prop('checked', false);
                }
            }
        });
    });
});
function toggleCheckboxes(master) {
    // Get all checkboxes with class 'form-check-input'
    const checkboxes = document.querySelectorAll('.form-check-input');

    // Set each checkbox's checked status based on the master checkbox
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = master.checked;
    });
}
  </script>