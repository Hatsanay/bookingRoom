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
            <p>เวลาเริ่มต้น: <span id="startTime"></span></p>
            <p>เวลาสิ้นสุด: <span id="endTime"></span></p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.7/html5-qrcode.min.js"></script>
    <script>
    window.onload = function() {
        resetScanner();
    };

    function resetScanner() {
        document.getElementById('qrcodeScanner').style.display = 'none';
        document.getElementById('scanResult').style.display = 'none';
        document.getElementById('reserveID').innerText = "";
        document.getElementById('scannedEmpID').innerText = "";
        document.getElementById('reserveDate').innerText = "";
        document.getElementById('durationID').innerText = "";
        document.getElementById('startTime').innerText = "";
        document.getElementById('endTime').innerText = "";
    }

    document.getElementById('scanQrcodeBtn').addEventListener('click', function() {
        const html5QrCode = new Html5Qrcode("qrcodeScanner");
        document.getElementById('qrcodeScanner').style.display = 'block';

        let alertShown = false; // ตัวแปรเพื่อเช็คว่า alert ได้แสดงแล้วหรือยัง

        html5QrCode.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: 250
            },
            qrCodeMessage => {
                console.log("QR Code scanned: ", qrCodeMessage);

                if (qrCodeMessage.length < 40) {
                    if (!alertShown) {
                        alert("ข้อมูล QR Code ไม่ถูกต้อง");
                        alertShown = true;
                    }
                    return;
                }

                let reserveID = qrCodeMessage.substring(0, 10);
                let scannedEmpID = qrCodeMessage.substring(10, 20);
                let reserveDate = qrCodeMessage.substring(20, 30);
                let durationID = qrCodeMessage.substring(30, 40);

                let loggedInEmpID = "<?php echo $emp_ID; ?>";

                let xhr = new XMLHttpRequest();
                xhr.open("POST", "save_qr_data.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        try {
                            let response = JSON.parse(this.responseText);

                            if (response.status === "success") {
                                document.getElementById('reserveID').innerText = reserveID;
                                document.getElementById('scannedEmpID').innerText = scannedEmpID;
                                document.getElementById('reserveDate').innerText = reserveDate;
                                document.getElementById('durationID').innerText = durationID;
                                document.getElementById('startTime').innerText = response.startTime;
                                document.getElementById('endTime').innerText = response.endTime;

                                document.getElementById('scanResult').style.display = 'block';

                                let startDateTime = new Date(reserveDate + "T" + response.startTime);
                                let endDateTime = new Date(reserveDate + "T" + response.endTime);
                                let currentDateTime = new Date();

                                console.log("Current DateTime: ", currentDateTime);
                                console.log("Start DateTime: ", startDateTime);
                                console.log("End DateTime: ", endDateTime);

                                if (currentDateTime >= startDateTime && currentDateTime <=
                                    endDateTime) {
                                    if (!alertShown) {
                                        alert("คุณสามารถเข้าใช้ห้องได้");
                                        alertShown = true;
                                        window.close(); // ปิดหน้าเมื่อแสดง alert เสร็จ
                                    }
                                } else {
                                    if (!alertShown) {
                                        alert(
                                            "ไม่สามารถเข้าใช้ห้องได้เพราะไม่อยู่ในช่วงเวลาหรือวันที่จอง");
                                        alertShown = true;
                                        window.close(); // ปิดหน้าเมื่อแสดง alert เสร็จ
                                    }
                                }

                                resetScanner();
                            } else {
                                if (!alertShown) {
                                    alert(response.message);
                                    alertShown = true;
                                    window.close(); // ปิดหน้าเมื่อแสดง alert เสร็จ
                                }
                                resetScanner();
                            }
                        } catch (e) {
                            console.error("Error parsing JSON: ", e);
                            if (!alertShown) {
                                alert("เกิดข้อผิดพลาดในระบบ");
                                alertShown = true;
                                window.close(); // ปิดหน้าเมื่อแสดง alert เสร็จ
                            }
                            resetScanner();
                        }
                    }
                };
                xhr.send("reserveID=" + reserveID + "&empID=" + scannedEmpID + "&loggedInEmpID=" +
                    loggedInEmpID);
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