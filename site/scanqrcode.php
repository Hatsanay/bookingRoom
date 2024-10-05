<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <!-- เพิ่มไลบรารี html5-qrcode -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.7/html5-qrcode.min.js"></script>
</head>
<body>

    <div id="reader" style="width:500px; height:500px;"></div>
    <div id="result"></div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // แสดงผล QR Code ที่ถูกสแกน
            document.getElementById('result').innerHTML = `QR Code Result: ${decodedText}`;
        }

        function onScanFailure(error) {
            // เมื่อสแกนไม่สำเร็จ แสดงข้อความในคอนโซล
            console.warn(`QR Code scan error: ${error}`);
        }

        // ฟังก์ชันตรวจสอบว่าเบราว์เซอร์รองรับการเข้าถึงกล้องหรือไม่
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // เบราว์เซอร์รองรับการเข้าถึงกล้อง
            navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                // สร้าง Html5Qrcode object และเริ่มสแกน QR Code
                let html5QrCode = new Html5Qrcode("reader");
                html5QrCode.start(
                    { facingMode: "environment" }, // ใช้กล้องหลัง
                    {
                        fps: 10, // เฟรมต่อวินาที
                        qrbox: { width: 250, height: 250 } // กำหนดขนาดกรอบสแกน
                    },
                    onScanSuccess, // ฟังก์ชันเมื่อสแกนสำเร็จ
                    onScanFailure // ฟังก์ชันเมื่อสแกนไม่สำเร็จ
                );
            })
            .catch(function(err) {
                // ข้อผิดพลาดเมื่อเข้าถึงกล้องไม่ได้
                alert("ไม่สามารถเข้าถึงกล้องได้: " + err.name);
            });
        } else {
            alert("เบราว์เซอร์ของคุณไม่รองรับการสตรีมกล้อง.");
        }
    </script>

</body>
</html>
