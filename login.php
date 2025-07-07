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

<h2>تسجيل الدخول</h2>

<div class="container">
<?php
if (isset($_GET['error_message'])) {
    echo "<div style='color: red; font-weight: bold; text-align: center; margin-bottom: 15px;'>" 
        . htmlspecialchars($_GET['error_message']) . 
        "</div>";
}
?>


<form action="check_login.php" method="POST">
  <label for="emp_id">رقم الموظف:</label>
  <input type="text" name="id" id="emp_id" required><br>

<<<<<<< HEAD
  <label for="password">كلمة المرور:</label>
  <input type="password" name="password" id="password" required><br>
=======
    <label class="loglabels" for="emp_pass">كلمة المرور:</label>
    <input class="loginputs" type="password" id="password" name="password" placeholder="كلمة المرور" required>
>>>>>>> f3cb3ca76cae25356f9b8cabd450a7fc6ac1e481

  <input type="submit" value="تسجيل الدخول">
</form>
</div>

</body>
</html>
