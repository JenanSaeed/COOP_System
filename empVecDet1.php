<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>طلب إجازة</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
  include 'footer.php';
  ?>
<!-- Leave Request Display -->
<main class="request-page">
  <h2 class="form-title">طلب إجازة</h2>
  <div class="leave-display">
    <div class="form-group">
      <label>نوع الإجازة:</label>
      <p>اعتيادية / مرضية / أخرى</p>
      <p>حدد إذا كانت أخرى: __________</p>
    </div>

    <div class="form-group">
      <label>المدة:</label>
      <p>____ يوم</p>
    </div>

    <div class="form-group">
      <label>من تاريخ:</label>
      <p>____/____/____</p>
    </div>

    <div class="form-group">
      <label>إلى تاريخ:</label>
      <p>____/____/____</p>
    </div>

    <div class="form-group">
      <label>اسم الشخص المكلف:</label>
      <p>_____________________</p>
    </div>
  </div>
</main>

<?php
  include 'footer.php';
  ?>
</body>
</html>
