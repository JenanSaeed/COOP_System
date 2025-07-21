<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الصفحة الرئيسية - إدارة قسم العقود</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<?php
  include 'header.php';

  $role = $_SESSION['role'] ?? null;

  // Default URLs
  $newContract = "#";
  $contractRecords = "#";

  // Role-based routing
  switch ($role) {
      case 'employee':
          $newContract = "c-main.php";
          $contractRecords = "c-adminRec.php";
          break;
      case 'finance':
          $newContract = "c-main.php";
          $contractRecords = "c-adminRec.php";
          break;

      case 'manager':
          $newContract = "c-main.php";
          $contractRecords = "c-adminRec.php";
          break;

      case 'guest':
          $newContract = "c-main.php";
          $contractRecords = "c-guestRec.php";
          break;

      default:
          // fallback in case of invalid role
          $newContract = "#";
          $contractRecords = "#";
  }
?>

<main class="home-main">
  <div style="text-align: center; margin-bottom: 20px;">
    <h2>الرجاء اختيار العملية:</h2>
  </div>

  <div class="home-buttons">
    <div class="button-group mb-3">
      <a href="<?= $newContract ?>" class="home-btn">
        إنشاء عقد جديد
      </a>
      <a href="<?= $contractRecords ?>" class="home-btn">
        سجل العقود
      </a>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
