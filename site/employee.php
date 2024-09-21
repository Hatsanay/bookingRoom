<?php 
$active = "employee";
include("header.php");
session_start();
$mem_id = $_SESSION['mem_id'];
// if(!substr($permistion,0,1)=="1"){
//   session_destroy();
//   Header("Location: ../index.php");
// }
$query_thesis = "SELECT t.*, m.mem_name
FROM 
    thesis t
JOIN 
    member m ON t.mem_id = m.mem_id
WHERE 
    t.thesis_status != 3 and t.thesis_status != 0 ;" ;
$rs_thesis = mysqli_query($condb, $query_thesis);

?>

<br>
  <!-- Main content -->
  <section class="content">

  <div class="card card-primary">
      <div class="card-header ">
        <h3 class="card-title">จัดการพนักงาน</h3>
      </div>  
      
      <div class="card-body">

        <div class="container-fluid">

          <div class="row">

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


