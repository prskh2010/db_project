<?php
session_start();
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیست جلسات</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>لیست جلسات</h1>
    <a href="logout.php" class="button">خوش آمدید، برای خروج کلیک کنید <?php echo htmlspecialchars($_SESSION['username']); ?>!</a>

    <?php
    if (isset($_POST['success'])) {
        echo '<p style="color: green;">جلسات با موفقیت حذف شدند.</p>';
    }
    if (isset($_POST['error'])) {
        echo '<p style="color: red;">خطا: ' . htmlspecialchars($_GET['error']) . '</p>';
    }
    ?>

    <form method="POST" action="delete_meetings.php">
        <table id="meetingsTable" border="1">
            <thead>
                <tr>
                    <th>انتخاب</th>
                    <th>نام جلسه</th>
                    <th>تاریخ جلسه</th>
                    <th>زمان جلسه</th>
                    <th>توضیحات جلسه</th>
                </tr>
            </thead>
            <tbody>
                <!-- داده‌ها در اینجا بارگذاری می‌شوند -->
            </tbody>
        </table>

        <br>
        <?php if ($_SESSION['user_type'] == 1): // اگر کاربر ادمین باشد ?>
            <a href="index.php" class="button">اضافه کردن جلسه جدید</a>
            <button type="submit" class="button" name="deleteButton" disabled>حذف کردن جلسه</button>
        <?php endif; ?>

        <button id="todayMeetingsButton" class="button">مشاهده جلسات امروز</button>
    </form>

    <script>
        // درخواست مجوز نوتیفیکیشن

        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }
        // تابع برای ارسال نوتیفیکیشن
        function sendNotification(meetingName, meetingDate, meetingTime) {
            if (Notification.permission === "granted") {
                // تبدیل تاریخ به روز هفته
                const date = new Date(meetingDate);
                const options = { weekday: 'long' }; // گزینه برای نمایش روز هفته
                const dayName = date.toLocaleDateString('fa-IR', options); // نام روز به زبان فارسی

                new Notification("جلسه دارید!", {
                    body: `جلسه "${meetingName}" در روز ${dayName} ساعت ${meetingTime} یادتون نره`,
                });
            }
        }

                        // بارگذاری داده‌ها از get_meetings.php
        fetch('get_meetings.php')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('meetingsTable').getElementsByTagName('tbody')[0];
                const today = new Date().toISOString().split('T')[0]; // تاریخ امروز به فرمت YYYY-MM-DD
                const meetingsToday = []; // آرایه برای ذخیره نام جلسات امروز

                data.forEach(meeting => {
                    const row = tableBody.insertRow();
                    const cellSelect = row.insertCell(0);
                    const cellName = row.insertCell(1);
                    const cellDate = row.insertCell(2);
                    const cellTime = row.insertCell(3);
                    const cellDescription = row.insertCell(4);

                    // یک چک باکس برای انتخاب سطر های جدول
                    cellSelect.innerHTML = `<input type="checkbox" class="meeting-checkbox" name="meeting_ids[]" value="${meeting.id}">`;
                    cellName.innerText = meeting.meeting_name;
                    cellDate.innerText = meeting.meeting_date;
                    cellTime.innerText = meeting.meeting_time;
                    cellDescription.innerText = meeting.meeting_description;

                    // اضافه کردن رویداد برای تغییر رنگ سطر
                    cellSelect.querySelector('input').addEventListener('change', function() {
                        if (this.checked) {
                            row.style.backgroundColor = 'lightgreen'; // تغییر رنگ سطر به سبز
                        } else {
                            row.style.backgroundColor = ''; // بازگشت به رنگ پیش‌فرض
                        }
                    });

                    // اگر تاریخ جلسه برابر با تاریخ امروز باشد، نام جلسه را به آرایه اضافه کن
                    if (meeting.meeting_date === today) {
                        meetingsToday.push(meeting.meeting_name);
                        sendNotification(meeting.meeting_name, meeting.meeting_date, meeting.meeting_time); // ارسال نوتیفیکیشن
                    }
                });

                // اگر جلساتی برای امروز وجود داشته باشد، یک آلرت نمایش بده
                document.getElementById('todayMeetingsButton').onclick = function(event) {
                    event.preventDefault(); // جلوگیری از رفتار پیش‌فرض
                    if (meetingsToday.length > 0) {
                        alert(`جلسات امروز: ${meetingsToday.join(', ')}`);
                    } else {
                        alert("هیچ جلسه‌ای برای امروز وجود ندارد.");
                    }
                };

                // فعال و غیرفعال کردن دکمه حذف
                const checkboxes = document.querySelectorAll('.meeting-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                        const deleteButton = document.querySelector('button[name="deleteButton"]');
                        deleteButton.disabled = !anyChecked;
                        deleteButton.style.backgroundColor = anyChecked ? 'green' : '#ccc'; // تغییر رنگ دکمه
                    });
                });
            })
            .catch(error => console.error('Error fetching meetings:', error)); // سفارشی سازی از هرگونه خطای احتمالی
    </script>
</body>
</html>