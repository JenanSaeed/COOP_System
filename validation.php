<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>تفاصيل الطلب - مركز التعليم المستمر</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />

<body>
   <?php
  include 'header.php';
  ?>
    

  <!-- Main Content -->
  <main class="request-page">
    <h2 class="form-title">تفاصيل الطلب</h2>

    <form class="leave-form">

      <div class="form-group">
        <label>رصيد الموظف المستهلك:</label>
        <input type="text" placeholder="يوماً">
      </div>

      <div class="form-group">
        <label>رصيد الموظف المتبقي:</label>
        <input type="text" placeholder="يوماً">
      </div>

      <div class="form-group">
        <label>تاريخ آخر إجازة تمتع بها الموظف بتاريخ:</label>
        <input type="date">
      </div>

      <div class="form-group">
        <label>ومدتها:</label>
        <input type="number" class="inline-input" placeholder="14"> يوماً
      </div>

      <div class="form-group">
        <label><input type="checkbox"> الإجازة مستحقة نظاماً</label>
        <label><input type="checkbox"> الإجازة غير مستحقة نظاماً</label>
      </div>

      <div class="form-buttons">
        <button type="button" class="buttons">عودة</button>
        <button type="submit" class="buttons">اعتماد</button>
      </div>
    </form>
  </main>

 <?php
  include 'footer.php';
  ?>
</body>
</html>
