<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الإجازات - موظف</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
  include 'header.php';
  ?>

  <!-- Main Vacation Section -->
  <main class="vacation-page">
    <div class="vacation-container">
      <div class="vacation-title">
        <a href="empForm.php" class="new-vacation-link">
          <h3>طلب إجازة جديدة + </h3></a>
      </div>
        
      <div class="vacation-left">
        <h3 class="vacation-title">الإجازات السابقة</h3>

        <div class="vacation-card">
          <span class="status-dot" style="background-color: green;"></span>
          <span>مقبول</span>
        </div>

        <div class="vacation-card">
          <span class="status-dot" style="background-color: red;"></span>
          <span>مرفوض</span>
        </div>

        <div class="vacation-card">
          <span class="status-dot" style="background-color: coral;"></span>
          <span>معلق</span>
        </div>
      </div>

    </div>
  </main>

<?php
  include 'footer.php';
  ?>

</body>
</html>
