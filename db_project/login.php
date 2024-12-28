<?php
session_start();

// حذف سشن‌های مرتبط برای خالی کردن فیلدها هنگام لاگ‌اوت یا بارگذاری صفحه
unset($_SESSION['username'], $_SESSION['logout']);

// متغیرها
$errors = [];

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "meetings";

// اتصال به دیتابیس
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// بررسی ارسال فرم
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = trim($_POST['username'] ?? '');
    $input_password = trim($_POST['password'] ?? '');

    if (empty($input_username)) $errors[] = "نامتون چیست!";
    if (empty($input_password)) $errors[] = "رمزتون چیست!";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_type, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $input_username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_type, $stored_password);
            $stmt->fetch();

            if ($input_password === $stored_password) {
                $_SESSION['user_type'] = $user_type;
                $_SESSION['username'] = $input_username;

                header("Location: " . ($user_type == 1 ? "index.php" : "view_meetings.php"));
                exit();
            } else {
                $errors[] = "نام کاربری یا رمز عبور اشتباه است.";
            }
        } else {
            $errors[] = "نام کاربری یا رمز عبور اشتباه است.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحه لاگین</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        window.onload = function () {
            document.getElementById('username').value = "";
            document.getElementById('password').value = "";
        };

        function validateForm(event) {
            event.preventDefault();
            const userName = document.getElementById('username');
            const Password = document.getElementById('password');

            if (!userName.value.trim()) {
                alert("نامتون چیست!");
                userName.focus();
                return;
            }
            if (!Password.value.trim()) {
                alert("رمزتون چیست!");
                Password.focus();
                return;
            }

            document.getElementById('loginForm').submit();
        }
    </script>
</head>
<body>
    <h1>ورود به سیستم</h1>
    <form action="login.php" method="POST" id="loginForm" onsubmit="validateForm(event)">
        <div class="form-group">
            <label for="username">نام کاربری:</label>
            <input type="text" id="username" name="username">
        </div>
        <div class="form-group">
            <label for="password">رمز عبور:</label>
            <input type="password" id="password" name="password">
        </div>
        <input type="submit" value="ورود" id="btn1">
        <?php if (!empty($errors)) foreach ($errors as $error) echo "<p style='color:red;'>$error</p>"; ?>
    </form>
</body>
</html>
