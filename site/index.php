<?php 
$active = "all";
include("header.php");
session_start();

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
