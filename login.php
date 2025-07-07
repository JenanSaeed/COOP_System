<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel='stylesheet' href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  </head>
  
<body>
<?php
  include 'header.php';
  ?>

  <h2 class="form-title">تسجيل الدخول</h2>

<div class="container">
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