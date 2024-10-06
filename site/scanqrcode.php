<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$emp_ID = $_SESSION['userEmpID'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบสแกน QR Code เพื่อเข้าใช้ห้อง</title>
    <style>
    .container {
        text-align: center;
        margin-top: 50px;
    }

    #scanQrcodeBtn {
        font-size: 20px;
        padding: 10px 20px;
    }

    #scanResult {
        margin-top: 20px;
        font-size: 18px;
    }

    #qrcodeScanner {
        width: 500px;
        height: 500px;
        margin: 20px auto;
    }
    </style>
</head>

<body>

    <div class="container">
        <h2>ระบบสแกน QR Code เพื่อยืนยันการเข้าใช้ห้อง <?php echo $emp_ID?></h2>
        <button id="scanQrcodeBtn" class="btn btn-success">สแกน QR Code</button>
        <div id="qrcodeScanner"></div>

        <div id="scanResult" style="display: none;">
            <h3>ผลลัพธ์การสแกน QR Code</h3>
            <p>รหัสการจอง: <span id="reserveID"></span></p>
            <p>รหัสพนักงาน: <span id="empID"></span></p>
            <p>วันที่จอง: <span id="reserveDate"></span></p>
            <p>รหัสช่วงเวลา: <span id="durationID"></span></p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.7/html5-qrcode.min.js"></script>
    <script>
    document.getElementById('scanQrcodeBtn').addEventListener('click', function() {
        const html5QrCode = new Html5Qrcode("qrcodeScanner");
        html5QrCode.start({
                facingMode: "environment"
            }, // ใช้กล้องหลัง
            {
                fps: 10,
                qrbox: 250
            },
            qrCodeMessage => {
                console.log("QR Code scanned: ", qrCodeMessage);

                // แยกข้อมูลจาก QR Code
                let reserveID = qrCodeMessage.substring(0, 10); // RES0000003
                let empID = qrCodeMessage.substring(10, 20); // EMP0000001
                let reserveDate = qrCodeMessage.substring(20, 30); // 2024-10-06
                let durationID = qrCodeMessage.substring(30, 40); // DOR000000168

                // แสดงผลข้อมูล
                document.getElementById('reserveID').innerText = reserveID;
                document.getElementById('empID').innerText = empID;
                document.getElementById('reserveDate').innerText = reserveDate;
                document.getElementById('durationID').innerText = durationID;

                document.getElementById('scanResult').style.display = 'block';

                // ส่งข้อมูลไปยัง PHP เพื่อตรวจสอบสิทธิ์การเข้าใช้ห้อง
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "save_qr_data.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        alert(this.responseText); // แจ้งผลการตรวจสอบสิทธิ์
                    }
                };
                xhr.send("reserveID=" + reserveID + "&empID=" + empID + "&reserveDate=" + reserveDate +
                    "&durationID=" + durationID);

                // หยุดการสแกน QR Code
                html5QrCode.stop().then(() => {
                    console.log("QR Code scanning stopped.");
                }).catch(err => {
                    console.error("Error stopping QR Code scan: ", err);
                });
            },
            errorMessage => {
                console.log("Error: " + errorMessage);
            }
        ).catch(err => {
            console.error("Unable to start scanning: ", err);
        });
    });
    </script>
</body>

</html>