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

// بررسی اینکه آیا دکمه حذف فشرده شده است
if (isset($_POST['deleteButton'])) {
    // بررسی اینکه آیا شناسه‌ای ارسال شده است
    if (isset($_POST['meeting_ids']) && is_array($_POST['meeting_ids'])) {
        $ids = $_POST['meeting_ids'];
        $ids = implode(',', array_map('intval', $ids)); // تبدیل به رشته‌ای از شناسه‌ها

        // آماده‌سازی و اجرای کوئری حذف
        $sql = "DELETE FROM meetings WHERE id IN ($ids)";
        if ($conn->query($sql) === TRUE) {
            // اگر حذف موفقیت‌آمیز بود، کاربر را به صفحه view_meetings.php هدایت کنید
            header("Location: view_meetings.php");
            exit();
        } else {
            // در صورت بروز خطا، کاربر را به صفحه view_meetings.php هدایت کنید و پیام خطا را به عنوان پارامتر ارسال کنید
            header("Location: view_meetings.php?error=" . urlencode($conn->error));
            exit();
        }
    } else {
        // اگر هیچ شناسه‌ای ارسال نشده باشد، کاربر را به صفحه view_meetings.php هدایت کنید
        header("Location: view_meetings.php?error=No%20IDs%20provided");
        exit();
    }
}

// بستن اتصال
$conn->close();
?>