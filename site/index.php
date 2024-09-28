<?php 
$active = "all";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (substr($permistion, 0, 1) != "1") {
    session_destroy();
    header("Location: ../logout.php");
    exit();
}

$emp_ID = $_SESSION['userEmpID'];
//ข้อมูลการจอง
$query_reserve = "SELECT 
    reserveID AS \"reserveID\",
    room.roomName AS \"roomName\",
    reservelWillDate AS \"reservelWillDate\",
    duration.DurationStartTime || ' ' || duration.DurationEndTime AS \"timebetween\",
    reservelDetail AS \"reservelDetail\",
    status.staName AS \"statuscancle\",
    bookstatus.staName AS \"bookstatus\"
FROM reserveroom
    INNER JOIN room ON room.roomID = reserveroom.reservel_roomID
    INNER JOIN duration ON duration.durationID = reserveroom.reservel_durationID
    INNER JOIN status ON status.staID = reserveroom.reservel_staID
    INNER JOIN status bookstatus ON bookstatus.staID = reserveroom.reservel_BookingstatusID
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000007'
    AND reservel_empID = :emp_ID";
$rs_reserve = oci_parse($condb, $query_reserve);
oci_bind_by_name($rs_reserve, ':emp_ID', $emp_ID);
oci_execute($rs_reserve);

$query_reserveCancel = "SELECT
    reserveID AS \"reserveID\",
    room.roomName AS \"roomName\",
    reservelWillDate AS \"reservelWillDate\",
    duration.DurationStartTime || ' ' || duration.DurationEndTime AS \"timebetween\",
    reservelDetail AS \"reservelDetail\",
    status.staName AS \"statuscancle\",
    bookstatus.staName AS \"bookstatus\",
    reserveidtailcancel.DETAIL_TEXT AS \"reDetial\"
FROM reserveroom
    INNER JOIN room ON room.roomID = reserveroom.reservel_roomID
    INNER JOIN duration ON duration.durationID = reserveroom.reservel_durationID
    INNER JOIN status ON status.staID = reserveroom.reservel_staID
    INNER JOIN status bookstatus ON bookstatus.staID = reserveroom.reservel_BookingstatusID
    INNER JOIN reserveidtailcancel ON reserveroom.RESERVEID = reserveidtailcancel.RDC_RESERVEID
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000008'
    AND reservel_empID = :emp_ID";
$rs_reserveCancle = oci_parse($condb, $query_reserveCancel);
oci_bind_by_name($rs_reserveCancle, ':emp_ID', $emp_ID);
oci_execute($rs_reserveCancle);
?>

<br>
<!-- Main content -->
<section class="content">

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">รายการจองห้องประชุม
                <button class="btn btn-danger btn-edit" data-id="<?php echo $row_reserve['reserveID']; ?>"
                    data-toggle="modal" data-target="#reserveCancelModal">
                    <i class="fas fa-solid fa-clipboard-list"></i> ประวัติการยกเลิก
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
                                        <th>ห้อง</th>
                                        <th>วันที่</th>
                                        <th>ช่วงเวลา</th>
                                        <th>รายละเอียด</th>
                                        <th>สถานะการจอง</th>
                                        <th>สถานะอนุมัติ</th>
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
                                        <td><?php echo $row_reserve['roomName']; ?></td>
                                        <td><?php echo $row_reserve['reservelWillDate']; ?></td>
                                        <td><?php echo $row_reserve['timebetween']; ?></td>
                                        <td><?php echo $row_reserve['reservelDetail']; ?></td>
                                        <td><?php echo $row_reserve['statuscancle']; ?></td>
                                        <td><?php echo $row_reserve['bookstatus']; ?></td>
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
        <form action="index_db.php" method="POST" enctype="multipart/form-data">
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

<div class="modal fade" id="reserveCancelModal" tabindex="-1" role="dialog" aria-labelledby="reserveCancelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="reserveCancelModalLabel">ยกเลิกการจอง</h5>
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
                                                <th>วันที่</th>
                                                <th>ช่วงเวลา</th>
                                                <th>รายละเอียด</th>
                                                <th>สถานะการจอง</th>
                                                <th>สาเหตุการยกเลิก</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                             $l = 0;
                                             while ($row_reserveCancle = oci_fetch_assoc($rs_reserveCancle)) { ?>
                                            <tr>
                                                <td><?php echo ++$l; ?></td>
                                                <td><?php echo $row_reserveCancle['reserveID']; ?></td>
                                                <td><?php echo $row_reserveCancle['roomName']; ?></td>
                                                <td><?php echo $row_reserveCancle['reservelWillDate']; ?></td>
                                                <td><?php echo $row_reserveCancle['timebetween']; ?></td>
                                                <td><?php echo $row_reserveCancle['reservelDetail']; ?></td>
                                                <td><?php echo $row_reserveCancle['bookstatus']; ?></td>
                                                <td><?php echo $row_reserveCancle['reDetial']; ?></td>
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
</script>
</body>
</html>
