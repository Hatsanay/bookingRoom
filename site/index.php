<?php 
$active = "all";
include("header.php");
session_start();

if (substr($permistion, 0, 1) != "1") {
  session_destroy();
  header("Location: ../logout.php");
  exit();
}

$emp_ID = $_SESSION['userEmpID'];
//ข้อมูลการจอง
$query_reserve = "SELECT 
    reserveID,
    room.roomName AS roomName,
    reservelWillDate,
    CONCAT(duration.DurationStartTime, ' - ', duration.DurationEndTime) AS timebetween,
    reservelDetail,
    status.staName AS statuscancle,
    bookstatus.staName AS bookstatus
FROM reserveroom
    INNER JOIN room on room.roomID = reserveroom.reservel_roomID
    INNER JOIN duration on duration.durationID = reserveroom.reservel_durationID
    INNER JOIN status on status.staID = reserveroom.reservel_staID
    INNER JOIN status AS bookstatus on bookstatus.staID = reserveroom.reservel_BookingstatusID
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000007'
    AND reservel_empID = '$emp_ID';" ;
$rs_reserve = mysqli_query($condb, $query_reserve);

$query_reserveCancel = "SELECT 
    reserveID,
    room.roomName AS roomName,
    reservelWillDate,
    CONCAT(duration.DurationStartTime, ' - ', duration.DurationEndTime) AS timebetween,
    reservelDetail,
    status.staName AS statuscancle,
    bookstatus.staName AS bookstatus,
    reserveidtailcancel.Detail AS reDetial
FROM reserveroom
    INNER JOIN room on room.roomID = reserveroom.reservel_roomID
    INNER JOIN duration on duration.durationID = reserveroom.reservel_durationID
    INNER JOIN status on status.staID = reserveroom.reservel_staID
    INNER JOIN status AS bookstatus on bookstatus.staID = reserveroom.reservel_BookingstatusID
    INNER JOIN reserveidtailcancel on reserveidtailcancel.RDC_reserveID = reserveroom.reserveID
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000008'
    AND reservel_empID = '$emp_ID';" ;
$rs_reserveCancle = mysqli_query($condb, $query_reserveCancel);

//ข้อมูลสภานะ
$query_statusEdit = "SELECT
    staID,
    staName
FROM 
    status
    WHERE sta_statypID = 'STT0000004'
    ;";
$rs_statusEdit = mysqli_query($condb, $query_statusEdit);
?>

<br>
<!-- Main content -->
<section class="content">

    <div class="card card-primary">
        <div class="card-header ">
            <h3 class="card-title">รายการจองห้องประชุม
                <button class="btn btn-danger btn-edit" data-id="<?php echo $row_reserve['reserveID']; ?>"
                    data-toggle="modal" data-target="#reserveCancelModal">
                    <!-- data-target="#reserveDeleteModal"> -->
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
                                        <!-- <th>ดูเพิ่มเติม</th> -->
                                        <th>ยกเลิก</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rs_reserve as $row_reserve) { ?>


                                    <tr>
                                        <td><?php echo @$l+=1; ?></td>
                                        <td><?php echo $row_reserve['reserveID']; ?></td>
                                        <td><?php echo $row_reserve['roomName']; ?></td>
                                        <td><?php echo $row_reserve['reservelWillDate']; ?></td>
                                        <td><?php echo $row_reserve['timebetween']; ?></td>
                                        <td><?php echo $row_reserve['reservelDetail']; ?></td>
                                        <td><?php echo $row_reserve['statuscancle']; ?></td>
                                        <td><?php echo $row_reserve['bookstatus']; ?></td>
                                        <!-- <td>
                                            <a href="" class="btn btn-info" target="" data-toggle="modal"
                                                data-target="#Modal<?php echo $row_thesis['thesis_id'];?>"><i
                                                    class="fas fas fa-eye"></i></a>
                                            <?php 
                                            // include 'detail_thesis_modal.php';
                                            ?>
                                        </td> -->
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

                    <div class="col-md-12">
                        <div class="card-body table-responsive p-0">

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>





</section>
<!-- /.content -->

<div class="modal fade" id="reserveDeleteModal" tabindex="-1" role="dialog" aria-labelledby="reserveDeleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="index_db.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="listReserve" value="cancel">
            <input type="hidden" name="reservID" id="reservID" value="<?php echo $row_reserve['reserveID']; ?>">

            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="reserveDeleteModalLabel">ยกเลิกการจอง ID:
                        <?php echo $row_reserve['reserveID']; ?></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">เหตุผลที่ยกเลิก </label>
                        <div class="col-sm-10">
                            <!-- <input name="emp_DtlCancle" type="text" required class="form-control" placeholder="เหตุผล"
                                minlength="3" /> -->
                            <textarea name="reserve_DtlCancle" id="reserve_DtlCancle" class="form-control"
                                placeholder="เหตุผลที่ยกเลิก" minlength="3"></textarea>
                        </div>
                    </div>

                    <!-- <div class="form-group row">
                        <label for="emp_role" class="col-sm-2 col-form-label">สถานะ</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="emp_status2" id="emp_status2" required> -->
                    <!-- <option value="">เลือกสภานะ</option> -->
                    <?php
                                    // while ($row = mysqli_fetch_assoc($rs_statusEdit)) {
                                    //     echo '<option value="' . $row['staID'] . '">' . $row['staName'] . '</option>';
                                    // }
                                ?>
                    <!-- </select>
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

<div class="modal fade" id="reserveCancelModal" tabindex="-1" role="dialog" aria-labelledby="reserveCancelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <!-- <form action="" method="POST" enctype="multipart/form-data"> -->
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
                                            <?php foreach ($rs_reserveCancle as $row_reserveCancle) { ?>


                                            <tr>
                                                <td><?php echo @$l2+=1; ?></td>
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
        <!-- </form> -->
    </div>
</div>


<!-- <div class="modalConfirm" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Modal body text goes here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->



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




</body>

</html>