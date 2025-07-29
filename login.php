<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php
include 'header.php';
?>
<h2 class="form-title">تسجيل الدخول</h2>

<div class="container">

<form class="log-form" action="check_login.php" method="post">
    <label class="loglabels" for="emp_id">اسم المستخدم:</label>
    <input class="loginputs" type="text" id="emp_id" name="emp_id" placeholder="اسم المستخدم" required>

    <label class="loglabels" for="password">كلمة المرور:</label>
    <input class="loginputs" type="password" id="password" name="password" placeholder="كلمة المرور" required>

    <div class="forgot-password">
    <a href="forget-pass.php">نسيت كلمة المرور؟</a>
</div>

<?php
if (isset($_GET['error'])) {
    echo "<div style='color: black; font-weight: bold; text-align: center; background-color: #f8d7da; font-size: 14px; padding: 10px; border-radius: 5px; margin-top: 15px; margin-bottom: -15px;'>"
    . htmlspecialchars($_GET['error']) .
    "</div>";

}
?>

    <div class="form-buttons">
        <button class="buttons" type="submit" value="تسجيل الدخول">تسجيل الدخول</button>
    </div>
  </form>
</div>

<?php
include 'footer.php';
?>

</body>
</html>
