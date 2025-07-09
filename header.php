<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />



</head>
<body>
  <header>
    <div class="logo">
      <a href="homepage.php">
        <img src="logo_white_no_bg.png" alt="مركز التعليم المستمر">
      </a>
    </div>
    
    <nav class="main-nav">
      <ul class="nav-links">
        <li><a href="homepage.php">الرئيسية</a></li>
        <li><a href="#">العقود</a></li>
        <li><a href="EmpReqs.php">الإجازات</a></li>
      </ul>
    </nav>
    
    <div class="logging">  
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
        <a class="logged" href="logout.php">
          <i class="fa-solid fa-right-from-bracket"></i>
          <?= htmlspecialchars($_SESSION['name']) ?>      
        </a>
        <?php else: ?>
          <a class="logged" href="login.php"><i class="fa-solid fa-user"></i></a>
        <?php endif; ?>
    </div>
    
</header>
</body>
</html>