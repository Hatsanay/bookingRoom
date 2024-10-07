</div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b>1.0
    </div>
    <strong>Copyright &copy; 2024 SSB.</strong>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->



<!-- jQuery -->
<script src="../assets/jquery.min.js"></script>

<!-- Bootstrap 4 -->
<script src="../assets/bootstrap.bundle.min.js"></script>

<!-- Select2 -->
<script src="../assets/select2.full.min.js"></script>

<!-- DataTables -->
<script src="../assets/jquery.dataTables.js"></script>
<script src="../assets/dataTables.bootstrap4.js"></script>

<!-- FullCalendar -->
<script src='../assets/fullcalendar/index.global.min.js'></script>

<!-- Other Scripts -->
<script src="../assets/tagsinput.js?v=1"></script>
<script src="../assets/sweetalert2@9.js"></script>
<script src="../assets/adminlte.min.js"></script>
<script src="../assets/demo.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>







<script>
  $(document).ready(function () {
    //$('.sidebar-menu').tree();
    //$('.select2').select2();
    //Initialize Select2 Elements
    $('.select2').select2({
      theme: 'bootstrap4'
    })
  })
</script>

<script>
$(function() {


    $('#example1').DataTable({
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10 ,25, 50, -1],
            [10 ,25, 50, "All"]
        ],

    });
   

});
</script>

<?php if(isset($_GET['employee_add'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'บันทึกข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'employee.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['employee_add_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถแก้ไขข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'employee.php';
    }
  });
</script>
<?php } ?>




<?php if(isset($_GET['employee_edit'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'employee.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['employee_edit_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถแก้ไขข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'employee.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['reserve_cancle'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'index.php';
    }
  });
</script>
<?php } ?>



<?php if(isset($_GET['reserve_cancle_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถแก้ไขข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'index.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['cancle_cancel'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'cancel.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['cancle_cancel_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถแก้ไขข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'cancel.php';
    }
  });
</script>
<?php } ?>


<?php if(isset($_GET['emplock_unlock'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'lockEmp.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['emplock_unlock_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถแก้ไขข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'lockEmp.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['room_add'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'บันทึกข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'room.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['room_add_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถบันทึกข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'room.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['room_edit'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'room.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['room_edit_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถแก้ไขข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'room.php';
    }
  });
</script>
<?php } ?>


<?php if(isset($_GET['role_add'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'บันทึกข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'role.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['role_add_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถบันทึกข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'role.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['role_edit'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'role.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['role_edit_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถแก้ไขข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'role.php';
    }
  });
</script>
<?php } ?>



<?php if(isset($_GET['reserve_add'])){ ?>
<script>
  Swal.fire({
  title: 'จองห้องประชุมสำเร็จ',
  text: 'บันทึกข้อมูลการจองสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'reserve.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['reserve_add_add_error'])){ ?>
<script>
  Swal.fire({
  title: 'จองห้องประชุมไม่สำเร็จ',
  text: 'ไม่สามารถบันทึกข้อมูลการจองได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'reserve.php';
    }
  });
</script>
<?php } ?>


<?php if(isset($_GET['reserve_havereserv'])){ ?>
<script>
  Swal.fire({
  title: 'จองห้องประชุมไม่สำเร็จ',
  text: 'มีการจองห้องประชุมนี้แล้ว โปรดเลือกช่วงเวลาอื่น',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'reserve.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['reserve_backdate'])){ ?>
<script>
  Swal.fire({
  title: 'จองห้องประชุมไม่สำเร็จ',
  text: 'ไม่สามารถจองย้อนหลังได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'reserve.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['reserve_locked'])){ ?>
<script>
  Swal.fire({
  title: 'จองห้องประชุมไม่สำเร็จ',
  text: 'คุณโดนล็อก โปรดติดต่อผู้ดูแลห้อง',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'reserve.php';
    }
  });
</script>
<?php } ?>


<?php if(isset($_GET['access_add'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'บันทึกข้อมูลสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'index.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['access_add_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่สามารถบันทึกข้อมูลได้',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'index.php';
    }
  });
</script>
<?php } ?>

<?php if(isset($_GET['access_can_error'])){ ?>
<script>
  Swal.fire({
  title: 'ไม่สำเร็จ',
  text: 'ไม่มีสิทธิ์เข้าใช้ห้องนี้หรือข้อมูลไม่ถูกต้อง',
  icon: 'error',
  confirmButtonText: 'ตกลง'
}).then((result) => {
    if (result.isConfirmed) {
      window.location = 'index.php';
    }
  });
</script>
<?php } ?>








