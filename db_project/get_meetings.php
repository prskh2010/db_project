<?php
// اطلاعات اتصال به دیتابیس
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meetings";

// ایجاد اتصال به دیتابیس
$conn = new mysqli($servername, $username, $password, $dbname);

// بررسی اتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// تنظیم کدگذاری کاراکترها
$conn->set_charset("utf8mb4");

// دریافت جلسات از دیتابیس
$sql = "SELECT * FROM meetings";
$result = $conn->query($sql);

$meetings = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $meetings[] = $row;
    }
}

// ارسال داده‌ها به صورت JSON
header('Content-Type: application/json');
echo json_encode($meetings);

// بستن اتصال
$conn->close();
?>