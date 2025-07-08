<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
include 'header.php';
?>
<h2 class="form-title">تسجيل الدخول</h2>

<div class="container">
<?php
if (isset($_GET['error_message'])) {
    echo "<div style='color: red; font-weight: bold; text-align: center; margin-bottom: 15px;'>" 
        .htmlspecialchars($_GET['error_message']) . 
        "</div>";
}
?>


<form class="logform" action="check_login.php" method="post">
    <label class="loglabels" for="emp_id">اسم المستخدم:</label>
    <input class="loginputs" type="text" id="emp_id" name="id" placeholder="اسم المستخدم" required>

    <label class="loglabels" for="emp_pass">كلمة المرور:</label>
    <input class="loginputs" type="password" id="emp_pass" name="password" placeholder="كلمة المرور" required>

    <input class="buttons" type="submit" value="تسجيل الدخول">
  </form>
</div>

<?php
include 'footer.php';
?>

</body>
</html>
