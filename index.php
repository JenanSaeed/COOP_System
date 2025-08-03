<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الصفحة الرئيسية</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

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
        $contractsLink = "c-adminRec.php"; 
      break;
    }
  }
?>

<main class="home-main">
  <div class="index-buttons">
    <a href="<?= $vacationsLink ?>" class="index-btn">
      🗓️ الإجازات
    </a>
    <a href="c-adminRec.php" class="index-btn">
      📄 العقود
    </a>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
