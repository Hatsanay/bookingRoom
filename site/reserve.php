<?php 
$active = "reserve";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!$condb) {
    $e = oci_error();
    echo "Failed to connect to Oracle database";
    exit;
}

// ดึงข้อมูลตึก
$query_building = "SELECT BUIID AS buiID, BUINAME AS buiname FROM BUILDING";
$rs_building = oci_parse($condb, $query_building);
oci_execute($rs_building);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- <link href='../assets/fullcalendar/index.global.js' rel='stylesheet' /> -->
    <script src='../assets/fullcalendar/index.global.js'></script>
    <script src='../assets/fullcalendar/index.global.min.js'></script>

    <!-- รวม jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>จองห้องประชุม</title>
</head>

<body>

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
                                    <div class="form-group">
                                        <label for="reserve_building">ตึก</label>
                                        <select class="form-control" name="reserve_building" id="reserve_building"
                                            required>
                                            <option value="">-- เลือกตึก --</option>
                                            <?php
                                            while ($row = oci_fetch_assoc($rs_building)) {
                                                echo '<option value="' . $row['BUIID'] . '">' . $row['BUINAME'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="reserve_floor">ชั้น</label>
                                        <select class="form-control" name="reserve_floor" id="reserve_floor" required>
                                            <option value="">-- เลือกชั้น --</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="reserve_room">ห้อง</label>
                                        <select class="form-control" name="reserve_room" id="reserve_room" required>
                                            <option value="">-- เลือกห้อง --</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
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

    <script>
    $(document).ready(function() {
        // เมื่อเลือกตึก
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
                                $('#reserve_floor').append('<option value="' + floor
                                    .floorID + '">' + floor.floorName +
                                    '</option>');
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
                                $('#reserve_room').append('<option value="' + room
                                    .roomID + '">' + room.roomName + '</option>'
                                );
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
                                eventContent: function(info) {
                                    var startTime = info.event.start
                                        .toLocaleTimeString([], {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        });
                                    var endTime = info.event.end ? info.event
                                        .end.toLocaleTimeString([], {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        }) : '';

                                    var timeText = document.createElement(
                                    'div');
                                    timeText.innerHTML = startTime + ' - ' +
                                        endTime + ' ' + info.event.title;

                                    return {
                                        domNodes: [timeText]
                                    };
                                    // var timeText = document.createElement(
                                    // 'div');
                                    // timeText.innerHTML = startTime + ' ' + info.event.title;

                                    // return {
                                    //     domNodes: [timeText]
                                    // };
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
                    events: [], // เริ่มต้นเป็นว่าง
                    dateClick: function(info) {
                        var date = info.dateStr;
                        $('#reserveModal').modal('show');
                        $('#reserve_date').val(date);
                    },
                    eventClick: function(info) {
                        alert('Room: ' + info.event.title + '\nDetails: ' + info.event
                            .extendedProps.description);
                    }
                });

                calendar.render();
            } else {
                console.error('FullCalendar is not loaded');
            }
        });
    });
    </script>

</body>

</html>