<?php 
$active = "room";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (substr($permistion, 7, 1) != "1") {
    session_destroy();
    header("Location: ../logout.php");
    exit();
}
$query_room = "SELECT
    roomID AS \"roomID\",
    BuiName ||  floorName  || roomName AS  \"roomName\",
    roomCapacity AS \"roomCapacity\",
    employee.empfname AS \"roomEmp\", 
    status.staname AS \"staname\",
    floor.floorName AS \"floorName\",
    building.BuiName AS \"BuiName\",
    roomtypt.ROOMTYPNAME AS \"roomtype\"
FROM room
INNER JOIN employee on employee.empid = room.room_empid
INNER JOIN status ON status.staid = room.room_staid
INNER JOIN floor ON room.room_floorID = floor.floorID
INNER JOIN building ON floor.BuiID = building.BuiID
INNER JOIN roomtypt ON roomtypt.ROOMTYPTID  = room.ROOM_ROOMTYPTID
WHERE room_staid = 'STA0000003'
ORDER BY roomID ASC";
$rs_room = oci_parse($condb, $query_room);
oci_execute($rs_room);

$query_roomtype = "SELECT 
    roomtyptID \"roomtyptID\", 
    roomtypName \"roomtyptName\"
FROM ROOMTYPT";
$rs_roomtype = oci_parse($condb, $query_roomtype);
oci_execute($rs_roomtype);

//ข้อมูลผู้ดูแลสำหรับเพิ่มข้อมูล
$query_emp = "SELECT 
    empID \"empID\", 
    empFname \"empFname\"
FROM employee
WHERE emp_stald = 'STA0000001'
ORDER BY empID ASC";
$rs_emp = oci_parse($condb, $query_emp);
oci_execute($rs_emp);

// ดึงข้อมูลตึกและชั้นสำหรับ dropdown
$query_building = "SELECT 
    BuiID \"BuiID\", 
    BuiName \"BuiName\"
FROM building";
$rs_building = oci_parse($condb, $query_building);
oci_execute($rs_building);


// ดึงข้อมูลตึก for edite
$query_Editbuilding = "SELECT 
    BuiID \"BuiID\", 
    BuiName \"BuiName\"
FROM building";
$rs_Editbuilding = oci_parse($condb, $query_Editbuilding);
oci_execute($rs_Editbuilding);

// ดึงข้อมูลชั้น for edite
$query_floor = "SELECT 
    floorID \"floorID\", 
    floorName \"floorName\"
FROM floor
--INNER JOIN BUILDING on floor.buiid = :building
INNER JOIN BUILDING on floor.buiid = BUILDING.buiid
ORDER BY floorID ASC";
$rs_floor = oci_parse($condb, $query_floor);
oci_execute($rs_floor);

// ข้อมูลสถานะสำหรับแก้ไข
$query_statusEdit = "SELECT
    staID AS \"staID\",
    staName AS \"staName\"
FROM 
    status
WHERE sta_statypID = 'STT0000002'";
$rs_statusEdit = oci_parse($condb, $query_statusEdit);
oci_execute($rs_statusEdit);

// ข้อมูลประเภทสำหรับแก้ไข
$query_typeEdit = "SELECT
    roomtyptID AS \"roomtyptID\",
    roomtypName AS \"roomtyptName\"
FROM 
    roomtypt";
$rs_typeEdit = oci_parse($condb, $query_typeEdit);
oci_execute($rs_typeEdit);

//ข้อมูลผู้ดูแลสำหรับแก้ไขข้อมูล
$query_empEdit = "SELECT 
    empID \"empID\", 
    empFname \"empFname\"
FROM employee
WHERE emp_stald = 'STA0000001'
ORDER BY empID ASC";
$rs_empEdit = oci_parse($condb, $query_empEdit);
oci_execute($rs_empEdit);





// ดึงข้อมูลตึก for VIEW
$query_Viewbuilding = "SELECT 
    BuiID \"BuiID\", 
    BuiName \"BuiName\"
FROM building";
$rs_Viewbuilding = oci_parse($condb, $query_Viewbuilding);
oci_execute($rs_Viewbuilding);

// ดึงข้อมูลชั้น for VIEW
$query_floorView = "SELECT 
    floorID \"floorID\", 
    floorName \"floorName\"
FROM floor
--INNER JOIN BUILDING on floor.buiid = :building
INNER JOIN BUILDING on floor.buiid = BUILDING.buiid
ORDER BY floorID ASC";
$rs_floorView = oci_parse($condb, $query_floorView);
oci_execute($rs_floorView);

// ข้อมูลสถานะสำหรับแก้ไข
$query_statusView = "SELECT
    staID AS \"staID\",
    staName AS \"staName\"
FROM 
    status
WHERE sta_statypID = 'STT0000002'";
$rs_statusView = oci_parse($condb, $query_statusView);
oci_execute($rs_statusView);

// ข้อมูลประเภทสำหรับแก้ไข
$query_typeView = "SELECT
    roomtyptID AS \"roomtyptID\",
    roomtypName AS \"roomtyptName\"
FROM 
    roomtypt";
$rs_typeView = oci_parse($condb, $query_typeView);
oci_execute($rs_typeView);

//ข้อมูลผู้ดูแลสำหรับแก้ไขข้อมูล
$query_empView = "SELECT 
    empID \"empID\", 
    empFname \"empFname\"
FROM employee
WHERE emp_stald = 'STA0000001'
ORDER BY empID ASC";
$rs_empView = oci_parse($condb, $query_empView);
oci_execute($rs_empView);
?>

<br>
<!-- Main content -->
<section class="content">
    <div class="card card-primary">
        <div class="card-header ">
            <h3 class="card-title">จัดการห้องประชุม</h3>
            <div align="right">

                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#roomModal"><i
                        class="fa fa-plus"></i> เพิ่มข้อมูลห้องประชุม</button>

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
                                        <th>รหัสห้องประชุม</th>
                                        <th>ชื่อห้องประชุม</th>
                                        <th>ความจุ</th>
                                        <th>ชั้น</th>
                                        <th>ตึก</th>
                                        <th>ประเภทห้อง</th>
                                        <th>สถานะ</th>
                                        <th>เพิ่มเติม</th>
                                        <th>แก้ไข</th>

                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    $l = 0;
                                    while ($row_room = oci_fetch_assoc($rs_room)) { 
                                    ?>
                                        <tr>
                                        <td><?php echo @$l+=1; ?></td>
                                        <td><?php echo $row_room['roomID']; ?></td>
                                        <td><?php echo $row_room['roomName']; ?></td>
                                        <td><?php echo $row_room['roomCapacity']; ?></td>
                                        <td><?php echo $row_room['floorName']; ?></td>
                                        <td><?php echo $row_room['BuiName']; ?></td>
                                        <td><?php echo $row_room['roomtype']; ?></td>
                                        <td><?php echo $row_room['staname']; ?></td>
                                        <td>
                                        <td>
                                            <button class="btn btn-info"
                                                data-id="<?php echo $row_room['roomID']; ?>" data-toggle="modal"
                                                data-target="#roomViewModal">
                                                <i class="fas fas fa-eye"></i> เพิ่มเติม
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-edit"
                                                data-id="<?php echo $row_room['roomID']; ?>" data-toggle="modal"
                                                data-target="#roomEditModal">
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

<!-- Modal สำหรับเพิ่มข้อมูลห้องประชุม -->
<div class="modal fade" id="roomModal" tabindex="-1" role="dialog" aria-labelledby="roomModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="room_db.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="room" value="add">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="roomModalLabel">เพิ่มข้อมูลห้องประชุม</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label for="building" class="col-sm-2 col-form-label">ตึก</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="building" id="building" required>
                                    <option value="">เลือกตึก</option>
                                    <?php
                                    while ($row = oci_fetch_assoc($rs_building)) {
                                        echo '<option value="' . $row['BuiID'] . '">' . $row['BuiName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                    </div>
                    <!-- ฟอร์มเลือกชั้น -->
                    <div class="form-group row">
                        <label for="room_floor" class="col-sm-2 col-form-label">ชั้น</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="room_floor" id="room_floor" required>
                                <option value="">เลือกชั้น</option>
                            </select>
                        </div>
                    </div>                   

                    <div class="form-group row">
                        <label for="roomName" class="col-sm-2 col-form-label">ชื่อ</label>
                        <div class="col-sm-10">
                            <input  name="roomName" type="text" required class="form-control" placeholder="ชื่อ"
                                minlength="2" maxlength="2" pattern="\d{2}" title="กรุณากรอกตัวเลข 2 ตัว"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="roomCapacity" class="col-sm-2 col-form-label">ความจุ </label>
                        <div class="col-sm-10">
                            <input name="roomCapacity"  type="text" required class="form-control" placeholder="ความจุ"
                                minlength="1" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="roomDetail" class="col-sm-2 col-form-label">รายละเอียด </label>
                        <div class="col-sm-10">
                            <input name="roomDetail" type="text" required class="form-control" placeholder="รายละเอียด"
                                minlength="3" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="room_roomtypeID" class="col-sm-2 col-form-label">ประเภท</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="room_roomtypeID" id="room_roomtypeID" required>
                                <option value="">เลือกประเภท</option>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_roomtype)) {
                                        echo '<option value="' . $row['roomtyptID'] . '">' . $row['roomtyptName'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="room_empID" class="col-sm-2 col-form-label">ผู้ดูแล</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="room_empID" id="room_empID" required>
                                <option value="">เลือกผู้ดูแลห้อง</option>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_emp)) {
                                        echo '<option value="' . $row['empID'] . '">' . $row['empFname'] . '</option>';
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

<!-- Modal สำหรับแก้ไขข้อมูลห้องประชุม -->
<div class="modal fade" id="roomEditModal" tabindex="-1" role="dialog" aria-labelledby="roomModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="room_db.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="room" value="Edit">
            <input type="hidden" name="roomID" id="roomID">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="roomModalLabel">แก้ไขข้อมูลห้องประชุม</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label for="building2" class="col-sm-2 col-form-label">ตึก</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="building2" id="building2" required disabled>
                                    <option value="">เลือกตึก</option>
                                    <?php
                                    while ($row = oci_fetch_assoc($rs_Editbuilding)) {
                                        echo '<option value="' . $row['BuiID'] . '">' . $row['BuiName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                    </div>
                    <div class="form-group row">
                        <label for="room_floor2" class="col-sm-2 col-form-label">ชั้น</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="room_floor2" id="room_floor2" required disabled>
                                    <option value="">เลือกชั้น</option>
                                    <?php
                                    while ($row = oci_fetch_assoc($rs_floor)) {
                                        echo '<option value="' . $row['floorID'] . '">' . $row['floorName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                    </div>
                    <!-- ฟอร์มเลือกชั้น 
                    <div class="form-group row">
                        <label for="room_floor2" class="col-sm-2 col-form-label">ชั้น</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="room_floor2" id="room_floor2" required>
                                <option value="">เลือกชั้น</option>
                            </select>
                        </div>
                    </div> -->                    
                    <div class="form-group row">
                        <label for="roomName" class="col-sm-2 col-form-label">ชื่อ</label>
                        <div class="col-sm-10">
                            <input  name="roomName" id="roomName" type="text" required class="form-control" placeholder="ชื่อ"
                                minlength="2" maxlength="2" pattern="\d{2}" title="กรุณากรอกตัวเลข 2 ตัว"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="roomCapacity" class="col-sm-2 col-form-label">ความจุ </label>
                        <div class="col-sm-10">
                            <input name="roomCapacity" id="roomCapacity" type="text" required class="form-control" placeholder="ความจุ"
                                minlength="2" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="roomDetail" class="col-sm-2 col-form-label">รายละเอียด </label>
                        <div class="col-sm-10">
                            <input name="roomDetail" id="roomDetail" type="text" required class="form-control" placeholder="รายละเอียด"
                                minlength="3" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="room_roomtypeID2" class="col-sm-2 col-form-label">ประเภท</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="room_roomtypeID2" id="room_roomtypeID2" required>
                                <option value="">เลือกประเภท</option>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_typeEdit)) {
                                        echo '<option value="' . $row['roomtyptID'] . '">' . $row['roomtyptName'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="room_empID2" class="col-sm-2 col-form-label">ผู้ดูแล</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="room_empID2" id="room_empID2" required>
                                <option value="">เลือกผู้ดูแลห้อง</option>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_empEdit)) {
                                        echo '<option value="' . $row['empID'] . '">' . $row['empFname'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="room_status2" class="col-sm-2 col-form-label">สถานะ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="room_status2" id="room_status2" required>
                                <option value="">เลือกสถานะห้อง</option>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_statusEdit)) {
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

<!-- Modal สำหรับดูข้อมูลห้องประชุม -->
<div class="modal fade" id="roomViewModal" tabindex="-1" role="dialog" aria-labelledby="roomModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="room_db.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="room" value="Edit">
            <input type="hidden" name="roomID" id="roomID">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="roomModalLabel">ดูข้อมูลห้องประชุม</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label for="building3" class="col-sm-2 col-form-label">ตึก</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="building3" id="building3" required disabled>
                                    <option value="">เลือกตึก</option>
                                    <?php
                                    while ($row = oci_fetch_assoc($rs_Viewbuilding)) {
                                        echo '<option value="' . $row['BuiID'] . '">' . $row['BuiName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                    </div>
                    <div class="form-group row">
                        <label for="room_floor3" class="col-sm-2 col-form-label">ชั้น</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="room_floor3" id="room_floor3" required disabled>
                                    <option value="">เลือกชั้น</option>
                                    <?php
                                    while ($row = oci_fetch_assoc($rs_floorView)) {
                                        echo '<option value="' . $row['floorID'] . '">' . $row['floorName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                    </div>
                    <!-- ฟอร์มเลือกชั้น 
                    <div class="form-group row">
                        <label for="room_floor2" class="col-sm-2 col-form-label">ชั้น</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="room_floor2" id="room_floor2" required>
                                <option value="">เลือกชั้น</option>
                            </select>
                        </div>
                    </div> -->                    
                    <div class="form-group row">
                        <label for="roomName3" class="col-sm-2 col-form-label">ชื่อ</label>
                        <div class="col-sm-10">
                            <input  name="roomName3" id="roomName3" type="text" required class="form-control" placeholder="ชื่อ"
                                minlength="2" maxlength="2" pattern="\d{2}" title="กรุณากรอกตัวเลข 2 ตัว" disabled/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="roomCapacity3" class="col-sm-2 col-form-label">ความจุ </label>
                        <div class="col-sm-10">
                            <input name="roomCapacity3" id="roomCapacity3" type="text" required class="form-control" placeholder="ความจุ"
                                minlength="2" disabled/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="roomDetail3" class="col-sm-2 col-form-label">รายละเอียด </label>
                        <div class="col-sm-10">
                            <input name="roomDetail3" id="roomDetail3" type="text" required class="form-control" placeholder="รายละเอียด"
                                minlength="3" disabled/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="room_roomtypeID3" class="col-sm-2 col-form-label">ประเภท</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="room_roomtypeID3" id="room_roomtypeID3" required disabled>
                                <option value="">เลือกประเภท</option>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_typeView)) {
                                        echo '<option value="' . $row['roomtyptID'] . '">' . $row['roomtyptName'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="room_empID3" class="col-sm-2 col-form-label">ผู้ดูแล</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="room_empID3" id="room_empID3" required disabled>
                                <option value="">เลือกผู้ดูแลห้อง</option>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_empView)) {
                                        echo '<option value="' . $row['empID'] . '">' . $row['empFname'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="room_status3" class="col-sm-2 col-form-label">สถานะ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="room_status3" id="room_status3" required disabled>
                                <option value="">เลือกสถานะห้อง</option>
                                <?php
                                    while ($row = oci_fetch_assoc($rs_statusView)) {
                                        echo '<option value="' . $row['staID'] . '">' . $row['staName'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<?php include('footer.php'); ?>

<script>
    $(document).ready(function() {
        // ฟังก์ชันทำงานเมื่อโมดัลถูกปิด
        $('#roomEditModal').on('hidden.bs.modal', function () {
            // ล้างค่าใน input field ที่มี id เป็น roleID
            $('#roomID').val();
                $('#roomName').val('');
                $('#roomCapacity').val('');
                $('#roomDetail').val('');
                $('#room_roomtypeID2').val('').trigger('change');
                $('#room_empID2').val('').trigger('change');
                $('#room_status2').val('').trigger('change');
                $('#room_floor2').val('').trigger('change');
                $('#building2').val('').trigger('change');  
        });
    });
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
        var roomID = $(this).data('id');
        $.ajax({
            url: 'room_db.php',
            type: 'GET',
            data: {
                id: roomID
            },
            success: function(response) {
                console.log(response);
                var roomData = JSON.parse(response);
                $('#roomID').val(roomData.roomID);
                $('#roomName').val(roomData.roomName);
                $('#roomCapacity').val(roomData.roomCapacity);
                $('#roomDetail').val(roomData.roomDetail);
                $('#room_roomtypeID2').val(roomData.room_roomtyptID).trigger('change');
                $('#room_empID2').val(roomData.room_empID).trigger('change');
                $('#room_status2').val(roomData.room_staID).trigger('change');
                $('#room_floor2').val(roomData.room_floorID).trigger('change');
                $('#building2').val(roomData.BuiID).trigger('change');  
            }
        });
    });
});

$(document).ready(function() {
    $('.btn-info').click(function() {
        var roomID = $(this).data('id');
        $.ajax({
            url: 'room_db.php',
            type: 'GET',
            data: {
                id: roomID
            },
            success: function(response) {
                console.log(response);
                var roomData = JSON.parse(response);
                $('#roomID').val(roomData.roomID);
                $('#roomName3').val(roomData.roomName);
                $('#roomCapacity3').val(roomData.roomCapacity);
                $('#roomDetail3').val(roomData.roomDetail);
                $('#room_roomtypeID3').val(roomData.room_roomtyptID).trigger('change');
                $('#room_empID3').val(roomData.room_empID).trigger('change');
                $('#room_status3').val(roomData.room_staID).trigger('change');
                $('#room_floor3').val(roomData.room_floorID).trigger('change');
                $('#building3').val(roomData.BuiID).trigger('change');
            }
        });
    });
});



// เมื่อเลือกตึก
$(document).ready(function() {
    $('#building').change(function() {
        var building = $(this).val();

        // ล้างข้อมูลเก่า
        $('#room_floor').html('<option value=""> เลือกชั้น </option>');

        if (building) {
            $.ajax({
                url: 'room_db.php',
                type: 'POST',
                data: {
                    building: building
                },
                dataType: 'json',
                success: function(response) {
                    if (response.length > 0) {
                        $.each(response, function(index, floor) {
                            $('#room_floor').append('<option value="' + floor.floorID + '">' + floor.floorName + '</option>');
                        });
                    } else {
                        alert('ไม่พบข้อมูลชั้น');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });
});


</script>
