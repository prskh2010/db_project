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
    die("اتصال برقرار نشد: " . $conn->connect_error);
}

// تنظیم کدگذاری کاراکترها
$conn->set_charset("utf8mb4");

// متغیر برای ذخیره خطاها
$errors = [];

// دریافت اطلاعات جلسه و اعتبارسنجی ورودی ها
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meeting_name = $_POST['meeting_name'] ?? '';
    $meeting_date = $_POST['meeting_date'] ?? '';
    $meeting_time = $_POST['meeting_time'] ?? '';
    $meeting_description = $_POST['meeting_description'] ?? '';

    if (empty($meeting_name)) {
        $errors[] = "لطفا نام جلسه را پر کنید.";
    }
    if (empty($meeting_date)) {
        $errors[] = "لطفا تاریخ جلسه را پر کنید.";
    }
    if (empty($meeting_time)) {
        $errors[] = "لطفا زمان جلسه را پر کنید.";
    }

    // اگر خطایی وجود نداشت، داده‌ها را به دیتابیس اضافه کن
    if (empty($errors)) {
        // آماده‌سازی و اجرای کوئری
        $sql = "INSERT INTO meetings (meeting_name, meeting_date, meeting_time, meeting_description) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $meeting_name, $meeting_date, $meeting_time, $meeting_description);

        if ($stmt->execute()) {
            // هدایت به صفحه view_meetings.php
            header("Location: view_meetings.php?success=1");
            exit();
        } else {
            echo "خطا در اضافه کردن جلسه: " . $stmt->error;
        }
    } else {
        // نمایش خطاها
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}

// بستن اتصال
$conn->close();
?>