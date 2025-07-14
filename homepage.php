<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الصفحة الرئيسية</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<?php
  include 'header.php';

  $role = $_SESSION['role'] ?? null;
  $contractsLink = "login.php";
  $vacationsLink = "login.php";

  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $role = $_SESSION['role'];

    switch ($role) {
      case 'employee':
        $contractsLink = "contracts.php";
        $vacationsLink = "empMain.php";
        break;
      case 'finance':
        $contractsLink = "contracts.php";
        $vacationsLink = "finMain.php";
        break;
      case 'manager':
        $contractsLink = "contracts.php";
        $vacationsLink = "manMain.php";
        break;
    }
  }
?>

<main class="home-main">
  <div class="home-buttons">
    <a href="<?= $vacationsLink ?>" class="home-btn">
      🗓️ الإجازات
    </a>
    <a href="<?= $contractsLink ?>" class="home-btn">
      📄 العقود
    </a>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
