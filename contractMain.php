<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الصفحة الرئيسية - قسم العقود</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<?php
  include 'header.php';

  $role = $_SESSION['role'] ?? null;
  $services = "login.php";
  $servicesDeploma = "login.php";
  $coopUni = "login.php";
  $paidTraining = "login.php";
  $trainingProgram = "login.php";

  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $role = $_SESSION['role'];

        $services = "services.php";
        $servicesDeploma = "servicesDeploma.php";
        $coopUni = "coopUni.php";
        $paidTraining = "paidTraining.php";
        $trainingProgram = "trainingProgram.php";

  }
?>

<main class="home-main">
  <div class="home-buttons">
    <p><h2>:الرجاء اختيار نوع العقد</h2></p>
    <a href="<?= $services ?>" class="home-btn">
      عقد تنفيذ خدمات
    </a>
    <a href="<?= $servicesDeploma ?>" class="home-btn">
      عقد تقديم خدمات (للدبلومات المهنية)
    </a>
    <a href="<?= $coopUni ?>" class="home-btn">
        عقد عمل تعاوني (منسوبي الجامعة)
    </a>
    <a href="<?= $paidTraining ?>" class="home-btn">
        عقد تدريب بنظام المكافأة الشهرية
    </a>
    <a href="<?= $trainingProgram ?>" class="home-btn">
        عقد تنفيذ برنامج تدريبي
    </a>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
