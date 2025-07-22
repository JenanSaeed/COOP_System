<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contract_type'])) {
    $_SESSION['contract_type'] = trim($_POST['contract_type']);
    header("Location: c-adminForm.php");
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
?>

<main class="home-main">
  <!-- The heading placed outside of the button groups -->
  <div style="text-align: center; margin-bottom: 20px;">
    <h2>الرجاء اختيار نوع العقد:</h2>
  </div>

  <form action="" method="POST" style="display:inline-block;">
    <div class="form-buttons">
      <button type="submit" name="contract_type" value="عقد تقديم خدمات" class="home-btn">عقد تقديم خدمات</button>
      
      <button type="submit" name="contract_type" value="(عقد تقديم خدمات (الدبلومات المهنية" class="home-btn">عقد تقديم خدمات (الدبلومات المهنية)</button>
        
      <button type="submit" name="contract_type" value="عقد عمل تعاوني (منسوبي الجامعة)" class="home-btn">عقد عمل تعاوني (منسوبي الجامعة)</button>
        
      <button type="submit" name="contract_type" value="عقد تدريب بنظام المكافأة الشهرية" class="home-btn">عقد تدريب بنظام المكافأة الشهرية</button>
        
      <button type="submit" name="contract_type" value="عقد التدريب (الدورات)" class="home-btn">عقد  التدريب (الدورات)</button>
    </div>
  </form>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
