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
      $contractsLink = "c-adminRec.php";
      $vacationsLink = "empMain.php";
      break;
    case 'finance':
      $contractsLink = "c-adminRec.php";
      $vacationsLink = "finMain.php";
      break;
    case 'manager':
      $contractsLink = "c-adminRec.php";
      $vacationsLink = "manMain.php";
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

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
          <li><a href="<?= $vacationsLink ?>">الإجازات</a></li>
          <li><a href="<?= $contractsLink ?>">العقود</a></li>
        <?php else: ?>
          <li><a href="<?= $contractsLink ?>">العقود</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="logging">
  <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
    <div class="dropdown">
      <a class="logged dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-user"></i> <?= htmlspecialchars($_SESSION['name']) ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end text-end" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="c-adminProfile.php">الملف الشخصي</a></li>
        <li><a class="dropdown-item" href="logout.php">تسجيل الخروج</a></li>
      </ul>
    </div>
  <?php else: ?>
    <a class="logged" href="login.php"><i class="fa-solid fa-user"></i></a>
  <?php endif; ?>
</div>

  </header>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
