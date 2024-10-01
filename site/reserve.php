<?php 
$active = "reserve";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$condb) {
    $e = oci_error();
    echo "Failed to connect to Oracle database";
    exit;
}

// ดึงข้อมูลจากฐานข้อมูล
$query_reserve = "SELECT 
    reserveID AS \"reserveID\",
    room.roomName AS \"roomName\",
    reservelWillDate AS \"reservelWillDate\",
    duration.DurationStartTime AS \"start_time\",
    duration.DurationEndTime AS \"end_time\",
    reservelDetail AS \"reservelDetail\",
    status.staName AS \"statuscancle\",
    bookstatus.staName AS \"bookstatus\"
FROM reserveroom
    INNER JOIN room ON room.roomID = reserveroom.reservel_roomID
    INNER JOIN duration ON duration.durationID = reserveroom.reservel_durationID
    INNER JOIN status ON status.staID = reserveroom.reservel_staID
    INNER JOIN status bookstatus ON bookstatus.staID = reserveroom.reservel_BookingstatusID
    WHERE reserveroom.reservel_BookingstatusID = 'STA0000007'";

$rs_reserve = oci_parse($condb, $query_reserve);
oci_execute($rs_reserve);

// ดึงข้อมูลที่ใช้ใน FullCalendar
$events = [];
while ($row = oci_fetch_assoc($rs_reserve)) {
    $reservelWillDate = date("Y-m-d", strtotime($row['reservelWillDate']));
    $start_time = str_replace('.', ':', substr($row['start_time'], 11, 8));
    $end_time = str_replace('.', ':', substr($row['end_time'], 11, 8));
    $events[] = [
        'id' => $row['reserveID'],
        'title' => $row['roomName'],
        'start' => $reservelWillDate . 'T' . $start_time,
        'end' => $reservelWillDate . 'T' . $end_time,
        'description' => $row['reservelDetail']
    ];
}

// แปลงข้อมูล event เป็น JSON เพื่อใช้ใน JavaScript
$events_json = json_encode($events);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js'></script>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

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
                                            <!-- ตัวเลือกอื่น ๆ -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="reserve_floor">ชั้น</label>
                                        <select class="form-control" name="reserve_floor" id="reserve_floor" required>
                                            <option value="">-- เลือกชั้น --</option>
                                            <!-- ตัวเลือกอื่น ๆ -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="reserve_room">ห้อง</label>
                                        <select class="form-control" name="reserve_room" id="reserve_room" required>
                                            <option value="">-- เลือกห้อง --</option>
                                            <!-- ตัวเลือกอื่น ๆ -->
                                        </select>
                                    </div>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card-body table-responsive p-0">
                                <div id="calendar"></div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="reserveModal" tabindex="-1" aria-labelledby="reserveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reserveModalLabel">จองห้องประชุม</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="reserve_date">วันที่</label>
                            <input type="text" class="form-control" id="reserve_date" name="reserve_date" readonly>
                        </div>
                        <div class="form-group">
                            <label for="reserve_detail">รายละเอียดเพิ่มเติม</label>
                            <input name="reserve_detail" type="text" class="form-control"
                                placeholder="รายละเอียดเพิ่มเติม" minlength="3" required />
                        </div>
                        <button type="submit" class="btn btn-primary">ยืนยันการจอง</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: <?php echo $events_json; ?>, // โหลด events จาก PHP
            dateClick: function(info) {
                var date = info.dateStr;
                $('#reserveModal').modal('show');
                $('#reserve_date').val(date);
            },
            eventClick: function(info) {
                alert('Room: ' + info.event.title + '\nDetails: ' + info.event.extendedProps
                    .description);
            }
        });

        calendar.render();
    });
    </script>

</body>

</html>