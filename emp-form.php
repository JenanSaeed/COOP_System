<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>طلب إجازة</title>
  <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<?php
  include 'header.php';
  ?>

    
  <!-- Leave Request Form -->
  <main class="request-page">
    <h2 class="form-title">طلب إجازة</h2>
    <form class="leave-form" action="#" method="post" onsubmit="return redirectAfterSubmit()">
      <div class="form-group">
        <label>نوع الإجازة:</label>
        <input type="radio" name="type" value="اعتيادية" id="regular">
        <label for="regular">اعتيادية</label>

        <input type="radio" name="type" value="مرضية" id="sick">
        <label for="sick">مرضية</label>

        <input type="radio" name="type" value="أخرى" id="other">
        <label for="other">أخرى</label>

        <input type="text" name="other" placeholder="حدد إذا كانت أخرى">
      </div>

      <div class="form-group">
        <label>المدة:</label>
        <input type="number" min="1" name="days" class="inline-input"><strong>يوم</strong>
      </div>

      <div class="form-group">
        <label>من تاريخ:</label>
        <input type="date" name="fromDate">
      </div>

      <div class="form-group">
        <label>إلى تاريخ:</label>
        <input type="date" name="toDate">
      </div>

      <div class="form-group">
        <label>اسم الشخص المكلف:</label>
        <input type="text" name="delegate">
      </div>

      <div class="form-buttons">
        <button type="reset" class="buttons">إلغاء</button>
        <button type="submit" class="buttons">إرسال الطلب</button>
      </div>
    </form>
  </main>

  <!-- Footer -->
  <footer class="main-footer">
    جميع الحقوق محفوظة © مركز التعليم المستمر - جامعة الإمام عبدالرحمن بن فيصل
  </footer>

  <script>
  function redirectAfterSubmit() {
    // إذا كنتِ ترغبين بإرسال النموذج فعلاً:
    setTimeout(function () {
      window.location.href = 'thankyou.php';
    }, 100); // تأخير بسيط يسمح للنموذج بالإرسال
    return true; // يسمح بالإرسال
  }
</script>
<?php
  include 'footer.php';
  ?>
</body>
</html>
