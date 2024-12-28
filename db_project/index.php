<?php
session_start();
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php"); // اگر کاربر لاگین نکرده ، به صفحه لاگین هدایت بشه
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت جلسات</title>
    <link rel="stylesheet" href="css/style.css">

    <script>
            
        function validateForm(event) {
            // جلوگیری از ارسال فرم
            event.preventDefault();

            // دریافت فیلدها
            const meetingName = document.getElementById('meeting_name');
            const meetingDate = document.getElementById('meeting_date');
            const meetingTime = document.getElementById('meeting_time');
            const meetingDescription = document.getElementById('meeting_description');

            // بررسی خالی بودن فیلدها
            if (!meetingName.value) {
                alert("لطفا نام جلسه را پر کنید");
                meetingName.focus();
                return;
            }
            if (!meetingDate.value) {
                alert("لطفا تاریخ جلسه را پر کنید");
                meetingDate.focus();
                return;
            }
            if (!meetingTime.value) {
                alert("لطفا زمان جلسه را پر کنید");
                meetingTime.focus();
                return;
            }

            // اگر همه فیلدها پر شده باشند، فرم را ارسال کن
            document.getElementById('meetingForm').submit();
        }

        // تابع برای تنظیم تاریخ حداقل
        function setMinDate() {
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0'); // ماه از 0 شروع می‌شود
            const yyyy = today.getFullYear();
            const minDate = yyyy + '-' + mm + '-' + dd; // فرمت YYYY-MM-DD
            document.getElementById('meeting_date').setAttribute('min', minDate);
        }

        // تنظیم تاریخ حداقل هنگام بارگذاری صفحه
        window.onload = setMinDate;
    </script>
</head>
<body>
    <a href="logout.php" class="button">خوش آمدید، برای خروج کلیک کنید <?php echo htmlspecialchars($_SESSION['username']); ?>!</a> <!-- نمایش نام کاربر -->

    <form action="add_meeting.php" method="POST" id="meetingForm" onsubmit="validateForm(event)">
        <div class="form-group">
            <label for="meeting_name">نام جلسه:</label>
            <input type="text" id="meeting_name" name="meeting_name">
        </div>

        <div class="form-group">
            <label for="meeting_date">تاریخ جلسه:</label>
            <input type="date" id="meeting_date" name="meeting_date">
        </div>

        <div class="form-group">
            <label for="meeting_time">زمان جلسه:</label>
            <input type="time" id="meeting_time" name="meeting_time">
        </div>

        <div class="form-group">
            <label for="meeting_description">توضیحات جلسه:</label>
            <textarea id="meeting_description" name="meeting_description"></textarea>
        </div>

        <input type="submit" value="اضافه کردن جلسه" id="btn1">
    </form>

    <a href="view_meetings.php" class="button">مشاهده جلسات</a>
</body>
</html>