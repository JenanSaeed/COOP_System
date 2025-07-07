<?php
session_start();

// إذا ما هو مسجل دخول، رجّعه للصفحة الرئيسية
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: homepage.php");
    exit();
}

// إذا الدور ليس "manager"، وجهه حسب نوعه
if ($_SESSION['role'] === 'finance') {
    header("Location: finMain.php");
    exit();
} elseif ($_SESSION['role'] === 'employee') {
    header("Location: EmpReqs.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>طلبات الإجازات - المالية</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
  include 'header.php';
  ?>

  <!-- Vacation Requests List -->
  <main class="vacation-list-page">
    <h3>طلبات الإجازات</h3>

 <div class="vacation-row">
  <span class="employee-name">مشاعل الخالدي</span>
  <span class="vac-date">29/06/2025</span>
  <span class="status">
    <span class="dot pending"></span>معلق
  </span>
</div>

<div class="vacation-row">
  <span class="employee-name">مشاعل الخالدي</span>
  <span class="vac-date">24/06/2025</span>
  <span class="status">
    <span class="dot approved"></span>مقبول
  </span>
</div>

<div class="vacation-row">
  <span class="employee-name">مشاعل الخالدي</span>
  <span class="vac-date">22/06/2025</span>
  <span class="status">
    <span class="dot rejected"></span>مرفوض
  </span>
</div>

  </main>

<?php
  include 'footer.php';
  ?>

</body>
</html>
