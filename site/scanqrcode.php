<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$emp_ID = $_SESSION['userEmpID']; // รหัสพนักงานที่เข้าสู่ระบบ
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบสแกน QR Code เพื่อเข้าใช้ห้อง</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        #scanQrcodeBtn {
            background-color: #28a745;
            color: white;
            font-size: 18px;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #scanQrcodeBtn:hover {
            background-color: #218838;
        }

        #qrcodeScanner {
            width: 100%;
            height: 300px;
            border: 2px dashed #ccc;
            display: none;
            margin-top: 20px;
            border-radius: 10px;
        }

        #scanResult {
            margin-top: 20px;
            font-size: 18px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #ced4da;
            display: none;
        }

        #scanResult h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #007bff;
        }

        #scanResult p {
            margin: 5px 0;
            color: #495057;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>ระบบสแกน QR Code เพื่อยืนยันการเข้าใช้ห้อง (รหัสพนักงาน: <?php echo $emp_ID; ?>)</h2>
        <button id="scanQrcodeBtn">สแกน QR Code</button>
        <div id="qrcodeScanner"></div>

        <div id="scanResult">
            <h3>ผลลัพธ์การสแกน QR Code</h3>
            <p>รหัสการจอง: <span id="reserveID"></span></p>
            <p>รหัสพนักงานจาก QR: <span id="scannedEmpID"></span></p>
            <p>วันที่จอง: <span id="reserveDate"></span></p>
            <p>รหัสช่วงเวลา: <span id="durationID"></span></p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.7/html5-qrcode.min.js"></script>
    <script>
        // รีเซ็ตข้อมูลเมื่อโหลดหน้า
        window.onload = function() {
            resetScanner();
        };

        function resetScanner() {
            document.getElementById('qrcodeScanner').style.display = 'none'; // ซ่อนกล้อง
            document.getElementById('scanResult').style.display = 'none'; // ซ่อนผลลัพธ์
            document.getElementById('reserveID').innerText = ""; // ล้างค่า
            document.getElementById('scannedEmpID').innerText = ""; // ล้างค่า
            document.getElementById('reserveDate').innerText = ""; // ล้างค่า
            document.getElementById('durationID').innerText = ""; // ล้างค่า
        }

        document.getElementById('scanQrcodeBtn').addEventListener('click', function() {
            const html5QrCode = new Html5Qrcode("qrcodeScanner");
            document.getElementById('qrcodeScanner').style.display = 'block'; // แสดงกล้อง

            html5QrCode.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: 250
                },
                qrCodeMessage => {
                    console.log("QR Code scanned: ", qrCodeMessage);

                    let reserveID = qrCodeMessage.substring(0, 10); // RES0000003
                    let scannedEmpID = qrCodeMessage.substring(10, 20); // EMP0000001
                    let reserveDate = qrCodeMessage.substring(20, 30); // 2024-10-06
                    let durationID = qrCodeMessage.substring(30, 40); // DOR0000001

                    let loggedInEmpID = "<?php echo $emp_ID; ?>";

                    document.getElementById('reserveID').innerText = reserveID;
                    document.getElementById('scannedEmpID').innerText = scannedEmpID;
                    document.getElementById('reserveDate').innerText = reserveDate;
                    document.getElementById('durationID').innerText = durationID;

                    document.getElementById('scanResult').style.display = 'block';


                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "save_qr_data.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                            alert(this.responseText); // แจ้งผลการตรวจสอบสิทธิ์
                            resetScanner(); // ล้างค่าหลังจากตรวจสอบเสร็จ
                        }
                    };
                    xhr.send("reserveID=" + reserveID + "&empID=" + scannedEmpID + "&reserveDate=" + reserveDate +
                        "&durationID=" + durationID + "&loggedInEmpID=" + loggedInEmpID);

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
