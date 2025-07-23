<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['contract_type'])) {
    header("Location: conTypes.php");
    exit();
}

$contract_type = $_SESSION['contract_type'] ?? '';
//علشان يتأكد من البروقرام id بدون مايرسل الفورم
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_check'])) {
    $code = $_POST['program_code'];
    $stmt = $conn->prepare("SELECT * FROM contract WHERE program_id = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    echo ($result->num_rows > 0) ? 'exists' : 'available';
    exit(); // مهم جداً عشان ما يكمل تنفيذ الصفحة
}


$currentYear = date("Y"); // السنة الحالية مثل 2025

// جلب آخر con_id بدايته بنفس السنة
$result = mysqli_query($conn, "SELECT con_id FROM contract WHERE con_id LIKE '%-$currentYear' ORDER BY con_id DESC LIMIT 1");

if ($row = mysqli_fetch_assoc($result)) {
    // استخراج الرقم قبل الشرطة
    $lastNumber = (int) explode('-', $row['con_id'])[0];
    $nextNumber = $lastNumber + 1;
} else {
    $nextNumber = 1;
}

// توليد الرمز الكامل مثل 0001-2025
$contract_code = str_pad($nextNumber, 4, "0", STR_PAD_LEFT) . "-$currentYear";

// حفظ البيانات عند الإرسال
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $con_id = $contract_code;
  $con_date = $_POST['gregorian_date'];
  $party1 = $_POST['party1'];
  $party2 = $_POST['party2'];
  $duration_value = $_POST['con_duration_value'];    // مثال: 5
  $duration_type = $_POST['duration_type'];          // مثال: أيام
  $con_duration = $duration_value . ' ' . $duration_type; // مثال: "5 أيام"
  $start_date = $_POST['start_gregorian'];
  $program_name = $_POST['program_name'];
  $program_id = $_POST['program_code'];
  $total = $_POST['contract_total'];
  


  $sql = "INSERT INTO contract (`con_id`, `con_date`, `1st_party`, `2nd_party`, `con_duration`, `con_starting_date`, `program_name`, `program_id`, `total`, `con_type`) 
  VALUES ('$con_id', '$con_date', '$party1', '$party2', '$con_duration', '$start_date', '$program_name', '$program_id', '$total', '$contract_type')";

  if (mysqli_query($conn, $sql)) {
    header("Location: c-terms.php");
    exit();
  } else {
    echo "<script>alert('حدث خطأ أثناء الحفظ: " . mysqli_error($conn) . "');</script>";
  }
}

// جلب بيانات الموظفين (فقط المانجر)
$employees = [];
$query = mysqli_query($conn, "SELECT name, emp_id, role, email, address, phone FROM employee WHERE role = 'manager'");
while ($row = mysqli_fetch_assoc($query)) {
    $employees[$row['name']] = $row;
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
    #warningMsg {
  opacity: 0;
  transition: opacity 0.3s ease;
}
#warningMsg.visible {
  opacity: 1;
}


  </style>
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>
</head>
<body>
<?php include 'header.php' ?>

<!-- القسم الأول -->
<section id="section1">
  <div class="form-box">
    <h2 class="form-title">تفاصيل تنفيذ العقد</h2>
    <form>
     <div class="form-group">
  <label>التاريخ الميلادي:</label>
  <input type="date" class="form-control" id="gregorianDate">
</div>

<div class="form-group">
  <label>التاريخ الهجري:</label>
  <input type="text" class="form-control" id="hijriDate" readonly>
</div>

<div class="form-group">
  <label>اليوم:</label>
  <input type="text" class="form-control" id="dayName" readonly>
</div>

      <div class="form-group">
        <label>الطرف الأول:</label>
        <select id="party1Name" style="font-size: 18px; padding: 10px; width: 100%;"  required>
          <?php foreach ($employees as $name => $data): ?>
            <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-buttons">
        <button type="button" class="buttons" onclick="window.location.href='c-conTypes.php'">عودة</button>
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
        <input type="hidden" name="contract_code" value="<?php echo $contract_code; ?>">
      </div>

      <div class="form-group">
        <label>اسم البرنامج:</label>
        <input type="text" class="form-control" id="program_name"  required>
      </div>

      <div class="form-group">
        <label>رمز البرنامج:</label>
        <input type="text" class="form-control" id="program_code" name="program_code" required>
        <small id="code-msg"></small>
      </div>


      <div class="form-group">
        <label>إجمالي العقد:</label>
        <input type="number" class="form-control" id="contract_total" name="contract_total" placeholder="ريالاً" min="0" max="20000" step="0.01" oninput="validatePrice(this)"  required>
      </div>

<div class="form-group">
  <label>مدة العقد:</label>
  <div style="display: flex; align-items: center; gap: 30px; flex-wrap: wrap;">
    <input type="text" class="form-control" name="con_duration_value" id="con_duration_value" style="width: 100px; margin-bottom: 0;"  required>

    <label style="display: flex; align-items: center; gap: 5px; margin: 0;">
      <input type="radio" name="duration_type" value="أيام"> أيام
    </label>

    <label style="display: flex; align-items: center; gap: 5px; margin: 0;">
      <input type="radio" name="duration_type" value="أسابيع"> أسابيع
    </label>

    <label style="display: flex; align-items: center; gap: 5px; margin: 0;">
      <input type="radio" name="duration_type" value="أشهر"> أشهر
    </label>
  </div>
</div>



      <div class="form-group">
        <label>تاريخ بداية العقد بالميلادي:</label>
        <input type="date" class="form-control" id="startGregorian"  required>
      </div>

      <div class="form-group">
        <label>تاريخ بداية العقد بالهجري:</label>
        <input type="text" class="form-control" id="startHijri" readonly>
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
      <!-- حقول مخفية للإرسال -->
      <input type="hidden" name="gregorian_date" id="hidden_gregorian_date">
      <input type="hidden" name="party1" id="hidden_party1">
      <input type="hidden" name="party2" id="hidden_party2">
      <input type="hidden" name="program_name" id="hidden_program_name">
      <input type="hidden" name="program_code" id="hidden_program_code">
      <input type="hidden" name="contract_total" id="hidden_contract_total">
      <input type="hidden" name="duration_type" id="hidden_duration">
      <input type="hidden" name="start_gregorian" id="hidden_start_gregorian">
      <input type="hidden" name="con_duration_value" id="hidden_duration_value">
      <input type="hidden" name="con_id" value="<?php echo $contract_code; ?>">

      

      <div class="form-group">
        <label>الاسم:</label>
        <select id="party3Name" name="party3" style="font-size: 18px; padding: 10px; width: 100%;">
          <?php foreach ($employees as $name => $data): ?>
            <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>الصفة:</label>
        <input type="text" class="form-control" name="role1" id="role1">
      </div>

      <div class="form-group">
        <label>العنوان:</label>
        <textarea type="text" class="form-control" name="address1" id="address1" rows="5"></textarea>
      </div>

      <div class="form-group">
        <label>رقم الهاتف:</label>
        <input type="tel" class="form-control" name="phone1" id="phone1">
      </div>

      <div class="form-group">
        <label>البريد الإلكتروني:</label>
        <input type="email" class="form-control" name="email1" id="email1">
      </div>

      <div class="form-buttons" style="display: flex; flex-direction: column; align-items: center;">
        <div id="warningMsg"></div>
        <button type="submit" id="submitBtn" class="submitBtn" disabled>إرسال</button>
     </div>

    </form>
  </div>
</section>

<?php include 'footer.php'; ?>

<script>
const employeeData = <?= json_encode($employees, JSON_UNESCAPED_UNICODE); ?>;

function fillParty3Info(name) {
  const data = employeeData[name];
  if (data) {
    document.getElementById("party3Name").value = name;
    document.getElementById("role1").value = data.role || '';
    document.getElementById("address1").value = data.address || '';
    document.getElementById("phone1").value = data.phone || '';
    document.getElementById("email1").value = data.email || '';
  }
}

document.getElementById("party1Name").addEventListener("change", function () {
  const selected = this.value;
  fillParty3Info(selected);
});

document.getElementById("party3Name").addEventListener("change", function () {
  const selected = this.value;
  document.getElementById("party1Name").value = selected;
  fillParty3Info(selected);
});

document.addEventListener("DOMContentLoaded", function () {
  fillParty3Info(document.getElementById("party1Name").value);

  // 👇 الكود الجديد للإظهار تاريخ اليوم في بارت
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');
  const formattedToday = `${yyyy}-${mm}-${dd}`;
  
  document.getElementById('gregorianDate').value = formattedToday;

  const day = today.getDay();
  const daysArabic = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
  document.getElementById('dayName').value = daysArabic[day];
  document.getElementById('hijriDate').value = moment(formattedToday, 'YYYY-MM-DD').format('iYYYY/iMM/iDD');
});


document.getElementById('gregorianDate').addEventListener('change', function () {
  const date = this.value;
  const day = new Date(date).getDay();
  const daysArabic = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
  document.getElementById('dayName').value = daysArabic[day];
  document.getElementById('hijriDate').value = moment(date, 'YYYY-MM-DD').format('iYYYY/iMM/iDD');
});

document.getElementById('startGregorian').addEventListener('change', function () {
  const date = this.value;
  document.getElementById('startHijri').value = moment(date, 'YYYY-MM-DD').format('iYYYY/iMM/iDD');
});

document.getElementById('contractFormAll').addEventListener('submit', function (e) {
  // امنع الإرسال مؤقتاً
  e.preventDefault();

  // انسخ القيم من الحقول الظاهرة إلى الـ hidden fields
  document.getElementById('hidden_gregorian_date').value = document.getElementById('gregorianDate').value;
  document.getElementById('hidden_party1').value = document.getElementById('party1Name').value;
  document.getElementById('hidden_program_name').value = document.getElementById('program_name').value;
  document.getElementById('hidden_program_code').value = document.getElementById('program_code').value;
  document.getElementById('hidden_contract_total').value = document.getElementById('contract_total').value;
  document.getElementById('hidden_duration').value = document.querySelector('[name="duration_type"]:checked')?.value || '';
  document.getElementById('hidden_start_gregorian').value = document.getElementById('startGregorian').value;
  document.getElementById('hidden_duration_value').value = document.querySelector('[name="con_duration_value"]').value || '';

  this.submit();
});

function validatePrice(input) {
  if (input.value > 20000) {
    alert("الحد الأعلى لإجمالي العقد هو 20,000 ريال");
    input.value = 20000;
  }
}

</script>

<script> //to check program_id before submiting the page
let programExists = false;
let hasTriedInvalid = false; // <-- جديد: يحدد إذا المستخدم دخل رمز مكرر سابقاً

document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('program_code');
  const msg = document.getElementById('code-msg');

  input.addEventListener('blur', function () {
    const code = input.value.trim();
    if (code === '') {
      msg.textContent = '';
      programExists = false;
      hasTriedInvalid = false;
      return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '', true); // نرسل لنفس الصفحة
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
      if (xhr.responseText === 'exists') {
        msg.textContent = '⚠️ رمز البرنامج مستخدم مسبقاً او غير صالح. الرجاء إدخال رمز آخر.';
        msg.style.color = 'red';
        programExists = true;
        hasTriedInvalid = true;
      } else {
        programExists = false;
        if (hasTriedInvalid) {
          msg.textContent = '✅ الرمز متاح.';
          msg.style.color = 'green';
        } else {
          msg.textContent = ''; // لا تظهر شيء إذا ما كان فيه محاولة خاطئة من قبل
        }
      }
    };
    xhr.send('ajax_check=1&program_code=' + encodeURIComponent(code));
  });

  const form = document.querySelector('form');
  form.addEventListener('submit', function (e) {
    if (programExists) {
      alert("رمز البرنامج مستخدم مسبقاً. الرجاء تغييره قبل الإرسال.");
      e.preventDefault();
    }
  });
});
</script>
<script>
  const requiredFieldIds = [
    'gregorianDate',
    'party1Name',
    'program_name',
    'program_code',
    'contract_total',
    'startGregorian',
    'con_duration_value'
  ];

  const submitBtn = document.getElementById('submitBtn');

  function checkFormCompletion() {
    const allFilled = requiredFieldIds.every(id => {
      const el = document.getElementById(id);
      return el && el.value.trim() !== '';
    });

    const durationSelected = document.querySelector('[name="duration_type"]:checked');

    const warningMsg = document.getElementById('warningMsg');
    const submitBtn = document.getElementById('submitBtn');

     if (allFilled && durationSelected) {
    submitBtn.disabled = false;
    submitBtn.style.backgroundColor = 'green'; // زر أخضر
    warningMsg.textContent = ''; // إخفاء الرسالة
    warningMsg.classList.remove('visible');
  } else {
    submitBtn.disabled = true;
    submitBtn.style.backgroundColor = ''; // يرجع للون الأصلي
    warningMsg.textContent = '⚠️ الرجاء إدخال جميع البيانات للمتابعة.';
    warningMsg.classList.add('visible');
  }
}

  // أربط كل حقل بحدث oninput أو onchange
  requiredFieldIds.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', checkFormCompletion);
    }
  });

  // لحقل الراديو
  document.querySelectorAll('[name="duration_type"]').forEach(radio => {
    radio.addEventListener('change', checkFormCompletion);
  });

  // تشغيل الفحص مبدأيًا إذا في بيانات محفوظة
  window.addEventListener('load', checkFormCompletion);
  //document.addEventListener('DOMContentLoaded', checkFormCompletion);


</script>

</body>
</html>
