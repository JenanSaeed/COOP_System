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
  <input type="date" id="vacation_date" />
  </div>

  <script>
    const dateInput = document.getElementById('vacation_date');

    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0'); // months are 0-based
    const dd = String(today.getDate()).padStart(2, '0');

    const minDate = `${yyyy}-${mm}-${dd}`;

    // Calculate max date: 12 months from today
    const maxDateObj = new Date(today);
    maxDateObj.setFullYear(maxDateObj.getFullYear() + 1);
    const maxY = maxDateObj.getFullYear();
    const maxM = String(maxDateObj.getMonth() + 1).padStart(2, '0');
    const maxD = String(maxDateObj.getDate()).padStart(2, '0');
    const maxDate = `${maxY}-${maxM}-${maxD}`;

    dateInput.min = minDate;
    dateInput.max = maxDate;
  </script>


      <div class="form-group">
        <label>ومدتها:</label>
        <input type="number" class="inline-input" placeholder="14"> يوماً
      </div>
      <div class="form-group">
        <label><input type="radio" name="vacation_status" value="eligible"> الإجازة مستحقة نظامًا</label>
        <label><input type="radio" name="vacation_status" value="not_eligible"> الإجازة غير مستحقة نظامًا</label>
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
