<?php 
$active = "all";
include("header.php");
session_start();

if (substr($permistion, 0, 1) != "1") {
  session_destroy();
  header("Location: ../logout.php");
  exit();
}

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
INNER JOIN status AS bookstatus on bookstatus.staID = reserveroom.reservel_BookingstatusID;" ;
$rs_reserve = mysqli_query($condb, $query_reserve);
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
                                                data-id="<?php echo $row_emp['empID']; ?>" data-toggle="modal"
                                                data-target="#employeeEditModal">
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
  </script>



  
</body>
</html>
