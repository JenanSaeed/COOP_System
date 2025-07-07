<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Saturn</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />



</head>
<body>
  <header>
    <div class="logo">
      <a href="homepage.php">
      <img src="logo_white_no_bg.png" alt="مركز التعليم المستمر">
    </div>
    <nav class="main-nav">
      <ul>
        <li><a href="homepage.php">الرئيسية</a></li>
        <li><a href="#">العقود</a></li>
        <li><a href="EmpReqs.php">الإجازات</a></li>
</ul>
    </nav>
  <div class="admin-icon">
          <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
        <!-- إذا كان مسجل دخول، نعرض زر تسجيل الخروج -->
        <form action="logout.php" method="post" style="display: inline;">
          <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">
            <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
          </button>
        </form>
      <?php else: ?>
        <!-- إذا ما كان مسجل دخول، نعرض أيقونة الدخول -->
        <a href="login.php"><i class="fa-solid fa-user"></i></a>
      <?php endif; ?>
  </div>
  </header>
</body>
</html>