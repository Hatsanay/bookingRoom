<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
// $permistion = $_SESSION['permistion']="1111111";
$permistion = $_SESSION['userRoleaccess'];

?>
<!-- bit 1 index.php -->
<!-- bit 2 reserve.php -->
<!-- bit 3 approve.php -->
<!-- bit 4 lockEmp.php -->



<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link bg-primary">
      <img src="../assets/img/logo/Logo-mut.png"
           alt="Logo"
           class="brand-image"
          >
      <span class="brand-text font-weight-light">ระบบจองห้องประชุม</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar"> 
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
           <img src="../assets/img/member/mem.png" class="img-circle elevation-2" alt="User Image">
          
        </div>
        <div class="info">
        <!-- <a href="profile.php" target="" class="d-block"> <?php echo $_SESSION['userEmpFname']," "; echo $_SESSION['userEmpLname'];?> | Profile</a> -->
        <a href="index.php" target="" class="d-block"> <?php echo $_SESSION['userEmpFname']," "; echo $_SESSION['userEmpLname'];?></a>
        </div>
      </div>

        <!-- Sidebar Menu -->
      <nav class="mt-2">
        <?php if(substr($permistion,0,1) == "1"){?> <!-- bitที่1 index.php -->
        <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
         
          <li class="nav-header">สรุป</li>
        
         <li class="nav-item">
            <a href="index.php" class="nav-link <?php if($active=="all"){echo "active";} ?> ">
            <i class="nav-icon fas fa-book"></i>
              <p>รายการที่จอง</p>
            </a>
          </li>


        </ul>
        <?php }?>

        <?php if(substr($permistion,1,1)== "1"||substr($permistion,2,1)== "1"||substr($permistion,3,1)== "1"||substr($permistion,4,1)== "1"){?> <!-- bitที่2 และ 3 -->
        <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
         
          <li class="nav-header">เมนู</li>

          <?php if(substr($permistion,1,1)== "1"){?> <!-- bitที่2 -->
         <li class="nav-item">
            <a href="reserve.php" class="nav-link <?php if($active=="reserve"){echo "active";} ?> ">
            <i class="nav-icon fas fa-book"></i>
              <p>จองห้องประชุม</p>
            </a>
          </li>
          <?php }?>
          <?php if(substr($permistion,2,1)== "1"){?> <!-- bitที่3 -->
          <li class="nav-item">
            <a href="approve.php" class="nav-link <?php if($active=="approve"){echo "active";} ?> ">
            <i class="nav-icon fas fa-solid fa-stamp"></i>
              <p>อนุมัติการจอง</p>
            </a>
          </li>
          <?php }?>
          <?php if(substr($permistion,3,1)== "1"){?> <!-- bitที่4 -->
          <li class="nav-item">
            <a href="lockEmp.php" class="nav-link <?php if($active=="lockEmp"){echo "active";} ?> ">
            <i class="nav-icon fas fa-user-lock"></i>
              <p>ล็อกพนักงาน</p>
            </a>
          </li>
        <?php }?>
        <?php if(substr($permistion,4,1)== "1"){?> <!-- bitที่4 -->
          <li class="nav-item">
            <a href="cancel.php" class="nav-link <?php if($active=="cancel"){echo "active";} ?> ">
            <i class="nav-icon fas far fa-calendar-times"></i>
              <p>ยกเลิกการจอง</p>
            </a>
          </li>
        <?php }?>
        </ul>
        <?php }?>

       <hr>
        
       <?php if(substr($permistion,5,1)== "1"||substr($permistion,6,1)== "1"||substr($permistion,7,1)== "1"){?> <!-- bitที่ 5 6 7 -->
        <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
   
          <li class="nav-header">การตั้งค่าข้อมูลระบบ</li>
          <?php if(substr($permistion,5,1)== "1"){?> <!-- bitที่5 -->
          <li class="nav-item">
            <a href="employee.php" class="nav-link <?php if($active=="employee"){echo "active";} ?> ">
              <i class="nav-icon fa fa-users"></i>
              <p>พนักงาน</p>
            </a>
          </li>
        <?php }?>
        <?php if(substr($permistion,6,1)== "1"){?> <!-- bitที่6 -->
          <li class="nav-item">
            <a href="role.php" class="nav-link <?php if($active=="role"){echo "active";} ?> ">
              <i class="nav-icon fas fa-university"></i>
              <p>ตำแหน่งและสิทธิ์</p>
            </a>
          </li>
          <?php }?>
        <?php if(substr($permistion,7,1)== "1"){?> <!-- bitที่6 -->
          <li class="nav-item">
            <a href="room.php" class="nav-link <?php if($active=="room"){echo "active";} ?> ">
              <i class="nav-icon fas fa-clipboard-list"></i>
              <p>ห้องประชุม</p>
            </a>
          </li>
          <?php }?>

        </ul>
        <hr>
        <?php }?>

<ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">

          <li class="nav-item">
            <a href="../logout.php" class="nav-link text-danger">
              <i class="nav-icon fas fa-power-off"></i>
              <p>ออกจากระบบ</p>
            </a>
          </li>
         
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>