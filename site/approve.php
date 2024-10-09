<?php 
$active = "approve";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (substr($permistion, 2, 1) != "1") {
  session_destroy();
  header("Location: ../logout.php");
  exit();
}
 
$emp_ID = $_SESSION['userEmpID'];
//ข้อมูลการจอง
$query_reserve = "SELECT 
    reserveID AS \"reserveID\",
    EMPLOYEE.EMPFNAME || ' ' || EMPLOYEE.EMPLNAME AS \"fullname\",
    BUILDING.BUINAME || FLOOR.FLOORNAME||ROOMNAME AS \"roomName\",
    reservelWillDate AS \"reservelWillDate\",
    TO_CHAR(DURATIONSTARTTIME, 'HH24:MI:SS') || ' - ' || TO_CHAR(DURATIONENDTIME, 'HH24:MI:SS') AS \"timebetween\",
    reservelDetail AS \"reservelDetail\",
    status.staName AS \"statuscancle\",
    bookstatus.staName AS \"bookstatus\"
FROM reserveroom
    INNER JOIN room ON room.roomID = reserveroom.reservel_roomID
    INNER JOIN duration ON duration.durationID = reserveroom.reservel_durationID
    INNER JOIN status ON status.staID = reserveroom.reservel_staID
    INNER JOIN status bookstatus ON bookstatus.staID = reserveroom.reservel_BookingstatusID
    INNER JOIN FLOOR ON FLOOR.FLOORID = ROOM.ROOM_FLOORID
    INNER JOIN BUILDING ON FLOOR.BUIID = BUILDING.BUIID
    INNER JOIN EMPLOYEE ON EMPLOYEE.EMPID = reserveroom.RESERVEL_EMPID
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000007'
    AND room_empID = :emp_ID
    AND reserveroom.reservel_staID = 'STA0000006'
    ORDER BY reserveID ASC
    ";
$rs_reserve = oci_parse($condb, $query_reserve);
oci_bind_by_name($rs_reserve, ':emp_ID', $emp_ID);
oci_execute($rs_reserve);

$query_approve = "SELECT 
    reserveID AS \"reserveID\",
    EMPLOYEE.EMPFNAME || ' ' || EMPLOYEE.EMPLNAME AS \"fullname\",
    BUILDING.BUINAME || FLOOR.FLOORNAME||ROOMNAME AS \"roomName\",
    reservelWillDate AS \"reservelWillDate\",
    TO_CHAR(DURATIONSTARTTIME, 'HH24:MI:SS') || ' - ' || TO_CHAR(DURATIONENDTIME, 'HH24:MI:SS') AS \"timebetween\",
    reservelDetail AS \"reservelDetail\",
    status.staName AS \"statuscancle\",
    bookstatus.staName AS \"bookstatus\"
FROM reserveroom
    INNER JOIN room ON room.roomID = reserveroom.reservel_roomID
    INNER JOIN duration ON duration.durationID = reserveroom.reservel_durationID
    INNER JOIN status ON status.staID = reserveroom.reservel_staID
    INNER JOIN status bookstatus ON bookstatus.staID = reserveroom.reservel_BookingstatusID
    INNER JOIN FLOOR ON FLOOR.FLOORID = ROOM.ROOM_FLOORID
    INNER JOIN BUILDING ON FLOOR.BUIID = BUILDING.BUIID
    INNER JOIN EMPLOYEE ON EMPLOYEE.EMPID = reserveroom.RESERVEL_EMPID
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000007'
    AND room_empID = :emp_ID
    AND reserveroom.reservel_staID = 'STA0000005'
    AND room.room_roomtyptID = 'RTY0000001'
    ORDER BY reserveID ASC
    ";
$rs_approve = oci_parse($condb, $query_approve);
oci_bind_by_name($rs_approve, ':emp_ID', $emp_ID);
oci_execute($rs_approve);
?>
<br>
  <!-- Main content -->
  <section class="content">

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">อนุมัติการจอง
            <button class="btn btn-danger btn-edit" data-id="<?php echo $row_reserve['reserveID']; ?>"
                data-toggle="modal" data-target="#reserveApproveModal">
                <i class="fas fa-solid fa-clipboard-list"></i> ประวัติการอนุมัติ
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
                                    <th>รหัสการจอง</th>
                                    <th>ชื่อผู้จอง</th>
                                    <th>ห้อง</th>
                                    <th>วันที่</th>
                                    <th>ช่วงเวลา</th>
                                    <th>รายละเอียด</th>
                                    <th>สถานะการจอง</th>
                                    <th>อนุมัติ</th>
                                    <th>ยกเลิก</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $l = 0;
                                while ($row_reserve = oci_fetch_assoc($rs_reserve)) { ?>
                                <tr>
                                    <td><?php echo ++$l; ?></td>
                                    <td><?php echo $row_reserve['reserveID']; ?></td>
                                    <td><?php echo $row_reserve['fullname']; ?></td>
                                    <td><?php echo $row_reserve['roomName']; ?></td>
                                    <td><?php echo $row_reserve['reservelWillDate']; ?></td>
                                    <td><?php echo $row_reserve['timebetween']; ?></td>
                                    <td><?php echo $row_reserve['reservelDetail']; ?></td>
                                    <td><?php echo $row_reserve['bookstatus']; ?></td>
                                    <td>
                                        <button class="btn btn-success btn-edit"
                                            data-id="<?php echo $row_reserve['reserveID']; ?>" data-toggle="modal"
                                            data-target="#approveModal">
                                            <i class="fas fa-pencil-alt"></i> อนุมัติ
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-edit"
                                            data-id="<?php echo $row_reserve['reserveID']; ?>" data-toggle="modal"
                                            data-target="#reserveDeleteModal">
                                            <i class="fas fa-pencil-alt"></i> ยกเลิก
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
    <form action="approve_db.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="listReserve" value="cancel">
        <input type="hidden" name="reservID" id="reservID">

        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="reserveDeleteModalLabel">ยกเลิกการจอง ID:</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group row">
                    <label for="reserve_DtlCancle" class="col-sm-2 col-form-label">เหตุผลที่ยกเลิก</label>
                    <div class="col-sm-10">
                        <textarea name="reserve_DtlCancle" id="reserve_DtlCancle" class="form-control"
                            placeholder="เหตุผลที่ยกเลิก" minlength="3" required></textarea>
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

<!-- Modal สำหรับอนุมัตการจอง -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
    <form action="approve_db.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="listReserve" value="approve">
        <input type="hidden" name="reservID" id="reservID">

        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="approveModalLabel">อนุมัติการจอง ID:</h5>
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
   
<div class="modal fade" id="reserveApproveModal" tabindex="-1" role="dialog" aria-labelledby="reserveApproveModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="reserveApproveModalLabel">อนุมัติการจอง</h5>
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
                                                <th>รหัสการจอง</th>
                                                <th>ห้อง</th>
                                                <th>ผู้จอง</th>
                                                <th>วันที่</th>
                                                <th>ช่วงเวลา</th>
                                                <th>รายละเอียด</th>
                                                <th>สถานะการจอง</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                             $l = 0;
                                             while ($row_approve = oci_fetch_assoc($rs_approve)) { ?>
                                            <tr>
                                                <td><?php echo ++$l; ?></td>
                                                <td><?php echo $row_approve['reserveID']; ?></td>
                                                <td><?php echo $row_approve['roomName']; ?></td>
                                                <td><?php echo $row_approve['fullname']; ?></td>
                                                <td><?php echo $row_approve['reservelWillDate']; ?></td>
                                                <td><?php echo $row_approve['timebetween']; ?></td>
                                                <td><?php echo $row_approve['reservelDetail']; ?></td>
                                                <td><?php echo $row_approve['bookstatus']; ?></td>
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
    
    
    
    
  </section>
  <!-- /.content -->

  
  
  <?php include('footer.php'); ?>
  <script>
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
    $('#reserveDeleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var reserveID = button.data('id');

        console.log("Reserve ID in Modal: ", reserveID);

        var modal = $(this);
        modal.find('.modal-title').text('ยกเลิกการจอง ID: ' + reserveID);
        modal.find('#reservID').val(reserveID);
    });

    $('form').on('submit', function(event) {
        console.log("Submitting form with reserveID: ", $('#reservID').val());
    });
});

$(document).ready(function() {
    $('#approveModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var reserveID = button.data('id');

        console.log("Reserve ID in Modal: ", reserveID);

        var modal = $(this);
        modal.find('.modal-title').text('อนุมัติการจอง ID: ' + reserveID);
        modal.find('#reservID').val(reserveID);
    });

    $('form').on('submit', function(event) {
        console.log("Submitting form with reserveID: ", $('#reservID').val());
    });
});
  </script>


