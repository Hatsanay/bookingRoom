<?php 
$active = "reserve";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
 

?>

<br>
<!-- Main content -->
<section class="content">

    <div class="card card-primary">
        <div class="card-header ">
            <h3 class="card-title">จองห้องประชุม</h3>
        </div>

        <div class="card-body">

            <div class="container-fluid">

                <div class="row">

                    <div class="col-md-12">
                        <div class="card-body table-responsive p-0">
                            <form action="employee_db.php" method="POST" enctype="multipart/form-data">

                                <input type="hidden" name="employee" value="add">



                                        <div class="form-group row">
                                            <label for="emp_Fname" class="col-sm-2 col-form-label">ชื่อ </label>
                                            <div class="col-sm-10">
                                                <input name="emp_Fname" type="text" required class="form-control"
                                                    placeholder="ชื่อ" minlength="3" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="emp_Lname" class="col-sm-2 col-form-label">นามสกุล </label>
                                            <div class="col-sm-10">
                                                <input name="emp_Lname" type="text" required class="form-control"
                                                    placeholder="นามสกุล" minlength="3" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="emp_gender" class="col-sm-2 col-form-label">เพศ</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2" name="emp_gender" id="emp_gender"
                                                    required>
                                                    <option value="">-- เลือกเพศ --</option>
                                                    <option value="M">ชาย</option>
                                                    <option value="F">หญิง</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="emp_bDate" class="col-sm-2 col-form-label">วันเกิด</label>
                                            <div class="col-sm-10">
                                                <input name="emp_bDate" type="date" required class="form-control"
                                                    id="emp_bDate" value="" placeholder="วัน/เดือน/ปีเกิด" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="emp_Phone" class="col-sm-2 col-form-label">เบอร์โทร </label>
                                            <div class="col-sm-10">
                                                <input name="emp_Phone" type="number" required class="form-control"
                                                    placeholder="เบอร์โทร" minlength="3" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="emp_department" class="col-sm-2 col-form-label">แผนก</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2" name="emp_department"
                                                    id="emp_department" required>
                                                    <option value="">เลือกแผนก</option>
                                                    
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="emp_role" class="col-sm-2 col-form-label">ตำแหน่ง</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2" name="emp_role" id="emp_role"
                                                    required>
                                                    <option value="">เลือกตำแหน่ง</option>
                                                    
                                                </select>
                                            </div>
                                        </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">ปิด</button>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>
                                            ยืนยัน</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
                                        <th>แก้ไข</th>

                                    </tr>
                                </thead>
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