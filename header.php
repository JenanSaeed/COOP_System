<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$contractsLink = "login.php";
$vacationsLink = "login.php";
$role = null;

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
  $role = $_SESSION['role'];

  switch ($role) {
    case 'employee':
      $contractsLink = "c-adminMain.php";
      $vacationsLink = "empMain.php";
      break;
    case 'finance':
      $contractsLink = "c-adminMain.php";
      $vacationsLink = "finMain.php";
      break;
    case 'manager':
      $contractsLink = "c-adminMain.php";
      $vacationsLink = "manMain.php";
      break;
    case 'guest':
      $contractsLink = "c-adminMain.php"; // allow access to contracts
      $vacationsLink = "#"; // no vacation access
      break;
  }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
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
        <?php if (!isset($role) || $role !== 'guest'): ?>
          <li><a href="index.php">الرئيسية</a></li>
          <li><a href="<?= $contractsLink ?>">العقود</a></li>
          <li><a href="<?= $vacationsLink ?>">الإجازات</a></li>
        <?php else: ?>
          <li><a href="<?= $contractsLink ?>">العقود</a></li>
        <?php endif; ?>
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
