<?php 
$active = "reserve";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบการเชื่อมต่อกับฐานข้อมูล
if (!$condb) {
    $e = oci_error();
    echo "Failed to connect to Oracle database";
    exit;
}

// ดึงข้อมูลตึก
$query_building = "SELECT BUIID AS buiID, BUINAME AS buiname FROM BUILDING";
$rs_building = oci_parse($condb, $query_building);
oci_execute($rs_building);

$query_duration = "SELECT DURATIONID AS durationID, 
TO_CHAR(DURATIONSTARTTIME, 'HH24:MI:SS') || ' - ' || TO_CHAR(DURATIONENDTIME, 'HH24:MI:SS') AS startEndTime 
FROM DURATION";
$rs_duration = oci_parse($condb, $query_duration);
oci_execute($rs_duration);
?>

<br>
<!-- Main content -->
<section class="content">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">จองห้องประชุม</h3>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body table-responsive p-0">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <!-- ฟอร์มเลือกตึก -->
                                <div class="form-group">
                                    <label for="reserve_building">ตึก</label>
                                    <select class="form-control" name="reserve_building" id="reserve_building" required>
                                        <option value="">-- เลือกตึก --</option>
                                        <?php
                                        while ($row = oci_fetch_assoc($rs_building)) {
                                            echo '<option value="' . $row['BUIID'] . '">' . $row['BUINAME'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- ฟอร์มเลือกชั้น -->
                                <div class="form-group">
                                    <label for="reserve_floor">ชั้น</label>
                                    <select class="form-control" name="reserve_floor" id="reserve_floor" required>
                                        <option value="">-- เลือกชั้น --</option>
                                    </select>
                                </div>

                                <!-- ฟอร์มเลือกห้อง -->
                                <div class="form-group">
                                    <label for="reserve_room">ห้อง</label>
                                    <select class="form-control" name="reserve_room" id="reserve_room" required>
                                        <option value="">-- เลือกห้อง --</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- ปฏิทิน -->
                    <div class="col-md-12">
                        <div class="card-body table-responsive p-0">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<!-- Modal สำหรับจองห้องประชุม -->
<div class="modal fade" id="reserveModal" tabindex="-1" aria-labelledby="reserveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reserveModalLabel">รายละเอียดการจอง</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reserveForm" action="" method="POST">
                    <div class="form-group">
                        <label for="reserve_date">วันที่จอง</label>
                        <input type="text" class="form-control" id="reserve_date" name="reserve_date" readonly>
                    </div>
                    <div class="form-group">
                        <label for="reserve_time">ช่วงเวลา</label>
                        <select class="form-control" name="reserve_time" id="reserve_time" required>
                            <option value="">-- เลือกช่วงเวลา --</option>
                            <?php
                            // ดึงข้อมูลช่วงเวลา
                            while ($row = oci_fetch_assoc($rs_duration)) {
                                echo '<option value="' . $row['durationID'] . '">' . $row['startEndTime'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reserve_details">รายละเอียดเพิ่มเติม</label>
                        <textarea class="form-control" name="reserve_details" id="reserve_details" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">ยืนยันการจอง</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
// เมื่อเลือกตึก
$(document).ready(function() {
    $('#reserve_building').change(function() {
        var building_id = $(this).val();

        // ล้างข้อมูลเก่า
        $('#reserve_floor').html('<option value="">-- เลือกชั้น --</option>');
        $('#reserve_room').html('<option value="">-- เลือกห้อง --</option>');

        if (building_id) {
            $.ajax({
                url: 'reserve_db.php',
                type: 'POST',
                data: {
                    building_id: building_id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.length > 0) {
                        $.each(response, function(index, floor) {
                            $('#reserve_floor').append('<option value="' + floor.floorID + '">' + floor.floorName + '</option>');
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

    // เมื่อเลือกชั้น
    $('#reserve_floor').change(function() {
        var floor_id = $(this).val();

        // ล้างข้อมูลเก่า
        $('#reserve_room').html('<option value="">-- เลือกห้อง --</option>');

        if (floor_id) {
            $.ajax({
                url: 'reserve_db.php',
                type: 'POST',
                data: {
                    floor_id: floor_id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.length > 0) {
                        $.each(response, function(index, room) {
                            $('#reserve_room').append('<option value="' + room.roomID + '">' + room.roomName + '</option>');
                        });
                    } else {
                        alert('ไม่พบข้อมูลห้อง');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });

    // เมื่อเลือกห้อง
    $('#reserve_room').change(function() {
        var room_id = $(this).val();

        if (room_id) {
            $.ajax({
                url: 'reserve_db.php',
                type: 'POST',
                data: {
                    room_id: room_id
                },
                dataType: 'json',
                success: function(events) {
                    var calendarEl = document.getElementById('calendar');

                    if (typeof FullCalendar !== "undefined") {
                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            events: events,
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            dateClick: function(info) {
                                $('#reserveModal').modal('show');
                                $('#reserve_date').val(info.dateStr); 
                            }
                        });

                        // ลบ events เดิม
                        calendar.removeAllEvents();

                        // เพิ่ม events ใหม่จากฐานข้อมูล
                        calendar.addEventSource(events);

                        // แสดงผลปฏิทิน
                        calendar.render();
                    } else {
                        console.error('FullCalendar is not defined');
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error fetching events: ", error);
                }
            });
        }
    });

    // ตั้งค่าปฏิทินเริ่มต้นเมื่อโหลดหน้า
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        if (typeof FullCalendar !== 'undefined') {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [], 
                dateClick: function(info) {
                    $('#reserveModal').modal('show');
                    $('#reserve_date').val(info.dateStr); 
                }
            });

            calendar.render();
        } else {
            console.error('FullCalendar is not loaded');
        }
    });
    
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
