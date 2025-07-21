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

        $services = "c-AdminForm.php";
        $servicesDeploma = "c-AdminForm.php";
        $coopUni = "c-AdminForm.php";
        $paidTraining = "c-AdminForm.php";
        $trainingProgram = "c-AdminForm.php";

  }
?>

<main class="home-main">
  <!-- The heading placed outside of the button groups -->
  <div style="text-align: center; margin-bottom: 20px;">
    <h2>الرجاء اختيار نوع العقد:</h2>
  </div>

    <div class="home-buttons">
    <div class="button-group mb-3">
      <form action="c-adminForm.php" method="POST" style="display:inline-block;">
        <input type="hidden" name="contract_type" value="عقد تنفيذ خدمات">
        <button type="submit" class="home-btn">عقد تنفيذ خدمات</button>
      </form>

      <form action="c-adminForm.php" method="POST" style="display:inline-block;">
        <input type="hidden" name="contract_type" value="عقد تقديم خدمات (الدبلومات المهنية)">
        <button type="submit" class="home-btn">عقد تقديم خدمات (الدبلومات المهنية)</button>
      </form>
    </div>

    <div class="button-group">
      <form action="c-adminForm.php" method="POST" style="display:inline-block;">
        <input type="hidden" name="contract_type" value="عقد عمل تعاوني (منسوبي الجامعة)">
        <button type="submit" class="home-btn">عقد عمل تعاوني (منسوبي الجامعة)</button>
      </form>

      <form action="c-adminForm.php" method="POST" style="display:inline-block;">
        <input type="hidden" name="contract_type" value="عقد تدريب بنظام المكافأة الشهرية">
        <button type="submit" class="home-btn">عقد تدريب بنظام المكافأة الشهرية</button>
      </form>

      <form action="c-adminForm.php" method="POST" style="display:inline-block;">
        <input type="hidden" name="contract_type" value="عقد تنفيذ برنامج تدريبي">
        <button type="submit" class="home-btn">عقد تنفيذ برنامج تدريبي</button>
      </form>

</main>



<?php include 'footer.php'; ?>

</body>
</html>
