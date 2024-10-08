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
// $query_reserve = "SELECT 
//     reserveID AS \"reserveID\",
//     BUILDING.BUINAME || FLOOR.FLOORNAME||ROOMNAME AS \"roomName\",
//     reservelWillDate AS \"reservelWillDate\",
//     TO_CHAR(DURATIONSTARTTIME, 'HH24:MI:SS') || ' - ' || TO_CHAR(DURATIONENDTIME, 'HH24:MI:SS') AS \"timebetween\",
//     reservelDetail AS \"reservelDetail\",
//     status.staName AS \"statuscancle\",
//     bookstatus.staName AS \"bookstatus\"
// FROM reserveroom
//     INNER JOIN room ON room.roomID = reserveroom.reservel_roomID
//     INNER JOIN duration ON duration.durationID = reserveroom.reservel_durationID
//     INNER JOIN status ON status.staID = reserveroom.reservel_staID
//     INNER JOIN status bookstatus ON bookstatus.staID = reserveroom.reservel_BookingstatusID
//     INNER JOIN FLOOR ON FLOOR.FLOORID = ROOM.ROOM_FLOORID
//     INNER JOIN BUILDING ON FLOOR.BUIID = BUILDING.BUIID
//     WHERE reserveroom.reservel_BookingstatusID = 'STA0000007'
//     AND reservel_empID = :emp_ID
//     ORDER BY reserveID ASC
//     ";
// $rs_reserve = oci_parse($condb, $query_reserve);
// oci_bind_by_name($rs_reserve, ':emp_ID', $emp_ID);
// oci_execute($rs_reserve);

$query_reserve = "SELECT 
    reserveID AS \"reserveID\",
    RESERVELQRCODE AS \"qrcode\",
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
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000007'
    AND reservel_empID = :emp_ID
    ORDER BY reserveID ASC
    ";
$rs_reserve = oci_parse($condb, $query_reserve);
oci_bind_by_name($rs_reserve, ':emp_ID', $emp_ID);
oci_execute($rs_reserve);


$query_reserveCancel = "SELECT
    reserveID AS \"reserveID\",
    BUILDING.BUINAME || FLOOR.FLOORNAME||ROOMNAME AS \"roomName\",
    reservelWillDate AS \"reservelWillDate\",
    TO_CHAR(DURATIONSTARTTIME, 'HH24:MI:SS') || ' - ' || TO_CHAR(DURATIONENDTIME, 'HH24:MI:SS') AS \"timebetween\",
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
    INNER JOIN FLOOR ON FLOOR.FLOORID = ROOM.ROOM_FLOORID
    INNER JOIN BUILDING ON FLOOR.BUIID = BUILDING.BUIID
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000008'
    AND reservel_empID = :emp_ID
    ORDER BY reserveID ASC
    ";
$rs_reserveCancle = oci_parse($condb, $query_reserveCancel);
oci_bind_by_name($rs_reserveCancle, ':emp_ID', $emp_ID);
oci_execute($rs_reserveCancle);


$query_reserveAccess = "SELECT
    reserveID AS \"reserveID\",
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
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000011' OR reserveroom.reservel_BookingstatusID = 'STA0000012'
    AND reservel_empID = :emp_ID
    ORDER BY reserveID ASC
    ";
$rs_reserveAccess = oci_parse($condb, $query_reserveAccess);
oci_bind_by_name($rs_reserveAccess, ':emp_ID', $emp_ID);
oci_execute($rs_reserveAccess);



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

                <button class="btn btn-success btn-edit" data-id="<?php echo $row_reserve['reserveID']; ?>"
                    data-toggle="modal" data-target="#reserveAccessModal">
                    <i class="fas fa-solid fa-clipboard-list"></i> ประวัติการเข้าใช้งานห้อง
                </button>

                <a href="scanqrcode.php" target="_blank" ><button class="btn btn-warning" ?>
                        <i class="fas fa-qrcode"></i> สแถน qrcode
                    </button></a>


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
                                        <th>Qrcode</th>
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

                                        <td>
                                            <button class="btn btn-warning btn-edit"
                                                data-id="<?php echo $row_reserve['reserveID']; ?>"
                                                data-qrcode="<?php echo $row_reserve['qrcode']; ?>" data-toggle="modal"
                                                data-target="#reserveQrcodeModal">
                                                <i class="fas fa-qrcode"></i> QR code
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



<div class="modal fade" id="reserveQrcodeModal" tabindex="-1" role="dialog" aria-labelledby="reserveQrcodeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="reserveQrcodeModalLabel">Qrcode เข้าใช้ห้อง ID:</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="qrcode" style="width:300px; height:300px; margin:auto;"></div>
            </div>
            <div class="modal-footer">
                <button id="downloadQrcode" class="btn btn-primary">ดาวน์โหลด QR code</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reserveAccessModal" tabindex="-1" role="dialog" aria-labelledby="reserveAccessModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="reserveAccessModalLabel">ประวัติการเข้าใช้งานห้อง</h5>
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                             $l = 0;
                                             while ($row_reserveAccess = oci_fetch_assoc($rs_reserveAccess)) { ?>
                                            <tr>
                                                <td><?php echo ++$l; ?></td>
                                                <td><?php echo $row_reserveAccess['reserveID']; ?></td>
                                                <td><?php echo $row_reserveAccess['roomName']; ?></td>
                                                <td><?php echo $row_reserveAccess['reservelWillDate']; ?></td>
                                                <td><?php echo $row_reserveAccess['timebetween']; ?></td>
                                                <td><?php echo $row_reserveAccess['reservelDetail']; ?></td>
                                                <td><?php echo $row_reserveAccess['bookstatus']; ?></td>
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

    $('#reserveQrcodeModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var reserveID = button.data('id');
        var qrCodeData = button.data('qrcode');

        console.log("Reserve ID in Modal: ", reserveID);
        console.log("QR Code Data: ", qrCodeData);

        var modal = $(this);
        modal.find('.modal-title').text('Qrcode เข้าใช้ห้อง ID: ' + reserveID);
        modal.find('#reservID').val(reserveID);

        $('#qrcode').empty();

        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: qrCodeData,
            width: 300,
            height: 300
        });


        $('#downloadQrcode').on('click', function() {
            var canvas = $('#qrcode canvas')[0];
            var imgData = canvas.toDataURL('image/png');

            var downloadLink = document.createElement('a');
            downloadLink.href = imgData;
            downloadLink.download = reserveID+'_qrcode.png';
            downloadLink.click();
        });
    });


    $('form').on('submit', function(event) {
        console.log("Submitting form with reserveID: ", $('#reservID').val());
    });
});
</script>
</body>

</html>