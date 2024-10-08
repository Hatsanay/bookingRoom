<?php 
$active = "report";
include("header.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$query_building = "SELECT BUIID AS buiID, BUINAME AS buiname FROM BUILDING";
$rs_building = oci_parse($condb, $query_building);
oci_execute($rs_building);

// ตรวจสอบว่ามีการส่งข้อมูล POST มาหรือไม่
$selected_month = isset($_POST['selected_month']) ? date('m-Y', strtotime($_POST['selected_month'])) : date('m-Y');
$selected_room = isset($_POST['selected_room']) ? $_POST['selected_room'] : '';

?>

<br>
<!-- Main content -->
<section class="content">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">รายงานการใช้งานห้อง Meeting</h3>
        </div>

        <div class="card-body">
            <div class="container-fluid">
                <form method="POST" action="">
                    <div class="row">
                        <!-- เลือกเดือน -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="selected_month">เลือกเดือน:</label>
                                <input type="month" id="selected_month" name="selected_month" class="form-control"
                                    value="<?php echo isset($_POST['selected_month']) ? $_POST['selected_month'] : date('Y-m'); ?>"
                                    required>
                            </div>
                        </div>

                        <!-- เลือกตึก -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="selected_building">ตึก:</label>
                                <select id="selected_building" name="selected_building" class="form-control" required>
                                    <option value="">เลือกตึก</option>
                                    <?php
                                        while ($row = oci_fetch_assoc($rs_building)) {
                                            echo '<option value="' . $row['BUIID'] . '">' . $row['BUINAME'] . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- เลือกชั้น -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="selected_floor">ชั้น:</label>
                                <select class="form-control" name="selected_floor" id="selected_floor" required>
                                    <option value="">-- เลือกชั้น --</option>
                                </select>
                            </div>
                        </div>
                        <!-- เลือกห้องประชุม -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="selected_room">เลือกห้องประชุม</label>
                                <select class="form-control" name="selected_room" id="selected_room" required>
                                    <option value="">-- เลือกห้อง --</option>
                                </select>
                            </div>
                        </div>
                        <!-- ปุ่มค้นหา -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">ค้นหา</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>วันที่</th>
                                        <th>จำนวนการจอง</th>
                                        <th>จำนวนที่ใช้งาน</th>
                                        <th>จำนวนไม่มาใช้งาน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($selected_room) {
                                        $query = "
                                        SELECT
                                        TO_CHAR(RESERVELWILLDATE, 'DD-MM-YYYY') AS DAY, 
                                        COUNT(*) AS TOTAL_BOOKINGS,
                                        SUM(CASE WHEN RESERVEL_BOOKINGSTATUSID = 'STA0000007' THEN 1 ELSE 0 END) AS TOTAL_USAGE,
                                        SUM(CASE WHEN RESERVEL_BOOKINGSTATUSID = 'STA0000012' THEN 1 ELSE 0 END) AS TOTAL_NO_SHOW
                                        FROM RESERVEROOM
                                        WHERE 
                                            TO_CHAR(RESERVELWILLDATE, 'MM-YYYY') = :selected_month
                                            AND RESERVEL_ROOMID = :selected_room
                                        GROUP BY 
                                            TO_CHAR(RESERVELWILLDATE, 'DD-MM-YYYY')
                                        ORDER BY 
                                            TO_CHAR(RESERVELWILLDATE, 'DD-MM-YYYY')";

                                        $result = oci_parse($condb, $query);
                                        oci_bind_by_name($result, ':selected_month', $selected_month);
                                        oci_bind_by_name($result, ':selected_room', $selected_room);
                                        oci_execute($result);

                                        while ($row = oci_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>{$row['DAY']}</td>";
                                            echo "<td>{$row['TOTAL_BOOKINGS']}</td>";
                                            echo "<td>{$row['TOTAL_USAGE']}</td>";
                                            echo "<td>{$row['TOTAL_NO_SHOW']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>ไม่มีข้อมูลสำหรับเดือนและห้องที่เลือก</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>
<script>
$(document).ready(function() {
    $('#selected_building').change(function() {
        var building_id = $(this).val();
        $('#selected_floor').html('<option value="">-- เลือกชั้น --</option>');
        $('#selected_room').html('<option value="">-- เลือกห้อง --</option>');

        if (building_id) {
            $.ajax({
                url: 'report_meeting_usage_db.php',
                type: 'POST',
                data: {
                    building_id: building_id
                },
                dataType: 'json',
                success: function(response) {
                    $.each(response, function(index, floor) {
                        $('#selected_floor').append('<option value="' + floor
                            .floorID + '">' + floor.floorName + '</option>');
                    });
                }
            });
        }
    });

    $('#selected_floor').change(function() {
        var floor_id = $(this).val();
        $('#selected_room').html('<option value="">-- เลือกห้อง --</option>');

        if (floor_id) {
            $.ajax({
                url: 'report_meeting_usage_db.php',
                type: 'POST',
                data: {
                    floor_id: floor_id
                },
                dataType: 'json',
                success: function(response) {
                    $.each(response, function(index, room) {
                        $('#selected_room').append('<option value="' + room.roomID +
                            '">' + room.roomName + '</option>');
                    });
                }
            });
        }
    });
});
</script>