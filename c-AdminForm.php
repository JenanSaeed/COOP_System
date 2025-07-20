<?php
include 'header.php';
include 'db_connect.php';

// توليد con_id تلقائي
$result = mysqli_query($conn, "SELECT MAX(con_id) AS max_id FROM contract");
$row = mysqli_fetch_assoc($result);
$nextId = $row['max_id'] + 1;
$contract_code = str_pad($nextId, 4, "0", STR_PAD_LEFT) . "-2025";

// حفظ البيانات عند الإرسال
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $con_id = $nextId;
  $con_date = $_POST['gregorian_date'];
  $party1 = $_POST['party1'];
  $party2 = $_POST['party2'];
  $duration = $_POST['duration'];
  $start_date = $_POST['start_gregorian'];
  $program_name = $_POST['program_name'];
  $program_id = $_POST['program_code'];
  $total = $_POST['contract_total'];

  $sql = "INSERT INTO contract (con_id, con_date, 1st_party, 2nd_party, con_duration, con_starting_date, program_name, program_id, total)
          VALUES ('$con_id', '$con_date', '$party1', '$party2', '$duration', '$start_date', '$program_name', '$program_id', '$total')";

  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('تم حفظ العقد بنجاح');</script>";
  } else {
    echo "<script>alert('حدث خطأ أثناء الحفظ: " . mysqli_error($conn) . "');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>العقود</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    html {
      scroll-behavior: smooth;
    }
    section {
      margin: 40px 0;
    }
  </style>
</head>
<body>

<!-- القسم الأول -->
<section id="section1">
  <div class="form-box">
    <h2 class="form-title">عقد تنفيذ برنامج تدريبي</h2>
    <form>
      <div class="form-group">
        <label>التاريخ الميلادي:</label>
        <input type="date" class="form-control" id="gregorianDate">
      </div>

      <div class="form-group">
        <label>التاريخ الهجري:</label>
        <input type="date" class="form-control" id="hijriDate">
      </div>

      <div class="form-group">
        <label>اليوم:</label>
        <input type="text" class="form-control" id="dayName" readonly>
      </div>

      <div class="form-group">
        <label>الطرف الأول:</label>
        <select class="form-control" id="party1">
          <option value="فاطمة">فاطمة</option>
          <option value="جواهر">جواهر</option>
        </select>
      </div>

      <div class="form-group">
        <label>الطرف الثاني:</label>
        <input type="text" class="form-control" id="party2">
      </div>

      <div class="form-buttons">
        <button type="button" class="buttons" onclick="document.getElementById('section2').scrollIntoView({behavior: 'smooth'})">التالي</button>
      </div>
    </form>
  </div>
</section>

<!-- القسم الثاني -->
<section id="section2">
  <div class="form-box">
    <h2 class="form-title">بيانات العقد</h2>
    <form>
      <div class="form-group">
        <label>رمز العقد:</label>
        <input type="text" class="form-control" value="<?php echo $contract_code; ?>" readonly>
      </div>

      <div class="form-group">
        <label>اسم البرنامج:</label>
        <input type="text" class="form-control" id="program_name">
      </div>

      <div class="form-group">
        <label>رمز البرنامج:</label>
        <input type="text" class="form-control" id="program_code">
      </div>

      <div class="form-group">
        <label>إجمالي العقد:</label>
        <input type="text" class="form-control" id="contract_total" placeholder="ريالاً">
      </div>

      <div class="form-group">
        <label>مدة العقد:</label>
        <div class="radio-group">
          <label><input type="radio" name="duration" value="days"> أيام</label>
          <label><input type="radio" name="duration" value="weeks"> أسابيع</label>
          <label><input type="radio" name="duration" value="months"> أشهر</label>
        </div>
      </div>

      <div class="form-group">
        <label>تاريخ بداية العقد بالميلادي:</label>
        <input type="date" class="form-control" id="startGregorian">
      </div>

      <div class="form-group">
        <label>تاريخ بداية العقد بالهجري:</label>
        <input type="date" class="form-control" id="startHijri">
      </div>

      <div class="form-buttons">
        <button type="button" class="buttons" onclick="document.getElementById('section1').scrollIntoView({behavior: 'smooth'})">السابق</button>
        <button type="button" class="buttons" onclick="document.getElementById('section3').scrollIntoView({behavior: 'smooth'})">التالي</button>
      </div>
    </form>
  </div>
</section>

<!-- القسم الثالث -->
<section id="section3">
  <div class="form-box">
    <h2 class="form-title">بيانات الطرف الأول</h2>
    <form id="contractFormAll" method="POST">
      <!-- hidden fields -->
      <input type="hidden" name="gregorian_date" id="hidden_gregorian_date">
      <input type="hidden" name="party1" id="hidden_party1">
      <input type="hidden" name="party2" id="hidden_party2">
      <input type="hidden" name="program_name" id="hidden_program_name">
      <input type="hidden" name="program_code" id="hidden_program_code">
      <input type="hidden" name="contract_total" id="hidden_contract_total">
      <input type="hidden" name="duration" id="hidden_duration">
      <input type="hidden" name="start_gregorian" id="hidden_start_gregorian">

      <div class="form-group">
        <label>الاسم:</label>
        <input type="text" class="form-control" name="name1">
      </div>

      <div class="form-group">
        <label>الصفة:</label>
        <input type="text" class="form-control" name="role1">
      </div>

      <div class="form-group">
        <label>العنوان:</label>
        <input type="text" class="form-control" name="address1">
      </div>

      <div class="form-group">
        <label>رقم الهاتف:</label>
        <input type="tel" class="form-control" name="phone1">
      </div>

      <div class="form-group">
        <label>البريد الإلكتروني:</label>
        <input type="email" class="form-control" name="email1">
      </div>

      <div class="form-buttons">
        <button type="submit" class="buttons">حفظ</button>
      </div>
    </form>
  </div>
</section>

<?php include 'footer.php'; ?>

<script>
// مزامنة التاريخ الميلادي إلى هجري والعكس + اليوم
function updateGregorianFromHijri() {
  const hijriInput = document.getElementById('hijriDate');
  const gregInput = document.getElementById('gregorianDate');
  const dayNameInput = document.getElementById('dayName');

  if (hijriInput.value) {
    gregInput.value = hijriInput.value;
    const day = new Date(hijriInput.value).getDay();
    const daysArabic = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    dayNameInput.value = daysArabic[day];
  }
}

document.getElementById('gregorianDate').addEventListener('change', function () {
  const selectedDate = new Date(this.value);
  const dayName = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'][selectedDate.getDay()];
  document.getElementById('dayName').value = dayName;
  document.getElementById('hijriDate').value = this.value;
});

document.getElementById('hijriDate').addEventListener('change', updateGregorianFromHijri);

document.getElementById('startGregorian').addEventListener('change', function () {
  document.getElementById('startHijri').value = this.value;
});

document.getElementById('startHijri').addEventListener('change', function () {
  document.getElementById('startGregorian').value = this.value;
});

// تمرير البيانات إلى النموذج الأخير قبل الإرسال
const form = document.getElementById('contractFormAll');
form.addEventListener('submit', function () {
  document.getElementById('hidden_gregorian_date').value = document.getElementById('gregorianDate').value;
  document.getElementById('hidden_party1').value = document.getElementById('party1').value;
  document.getElementById('hidden_party2').value = document.getElementById('party2').value;
  document.getElementById('hidden_program_name').value = document.getElementById('program_name').value;
  document.getElementById('hidden_program_code').value = document.getElementById('program_code').value;
  document.getElementById('hidden_contract_total').value = document.getElementById('contract_total').value;
  document.getElementById('hidden_duration').value = document.querySelector('[name="duration"]:checked')?.value || '';
  document.getElementById('hidden_start_gregorian').value = document.getElementById('startGregorian').value;
});
</script>

</body>
</html>
