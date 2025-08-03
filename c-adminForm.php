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

// AJAX check program_id
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_check'])) {
    $code = $_POST['program_code'];
    $stmt = $conn->prepare("SELECT * FROM contract WHERE program_id = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    echo ($result->num_rows > 0) ? 'exists' : 'available';
    exit();
}

$currentYear = date("Y");
$result = mysqli_query($conn, "SELECT con_id FROM contract WHERE con_id LIKE '%-$currentYear' ORDER BY con_id DESC LIMIT 1");

if ($row = mysqli_fetch_assoc($result)) {
    $lastNumber = (int) explode('-', $row['con_id'])[0];
    $nextNumber = $lastNumber + 1;
} else {
    $nextNumber = 1;
}

$contract_code = str_pad($nextNumber, 4, "0", STR_PAD_LEFT) . "-$currentYear";
$_SESSION['contract_code'] = $contract_code;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['ajax_check'])) {
    $con_id = $contract_code;
    $con_date = $_POST['gregorian_date'];
    $party1 = $_POST['party1'];
    $party2 = $_POST['party2'];
    $duration_value = $_POST['con_duration_value'];
    $duration_type = $_POST['duration_type'];
    $con_duration = $duration_value . ' ' . $duration_type;
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

$employee = [];
$query = mysqli_query($conn, "SELECT name, emp_id, role AS role, email AS email, address AS address, phone AS phone FROM employee WHERE role = 'manager'");
while ($row = mysqli_fetch_assoc($query)) {
    $employee[$row['name']] = $row;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<title>إضافة عقد جديد</title>
<link rel="stylesheet" href="style.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>
</head>
<body>

<?php include 'header.php'; ?>

<h3 class="mb-3">إضافة عقد جديد</h3>

<ul class="nav nav-tabs" id="contractTabs">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#step1">الخطوة 1</a></li>
  <li class="nav-item"><a class="nav-link disabled" data-bs-toggle="tab" href="#step2">الخطوة 2</a></li>
  <li class="nav-item"><a class="nav-link disabled" data-bs-toggle="tab" href="#step3">الخطوة 3</a></li>
</ul>

<form id="form-box" method="POST">
  <div class="r-container">

    <!-- خطوة 1 -->
    <div class="f-container" id="step1">
      <div class="form-group mb-3">
        <label>التاريخ الميلادي:</label>
        <input type="date" class="form-control" id="gregorianDate" name="gregorian_date" required>
      </div>
      <div class="form-group mb-3">
        <label>التاريخ الهجري:</label>
        <input type="text" class="form-control" id="hijriDate" readonly>
      </div>
      <div class="form-group mb-3">
        <label>اليوم:</label>
        <input type="text" class="form-control" id="dayName" readonly>
      </div>
      <div class="form-group mb-3">
        <label>الطرف الأول:</label>
        <select id="party1Name" name="party1" class="form-control" required>
          <?php foreach ($employee as $name => $data): ?>
            <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-buttons">
        <button type="button" class="buttons" onclick="goStep(2)">التالي</button>
      </div>
    </div> <!-- نهاية خطوة 1 -->

    <!-- خطوة 2 -->
    <div class="f-container" id="step2">
      <div class="form-group mb-3">
        <label>رمز العقد:</label>
        <input type="text" class="form-control" value="<?php echo $contract_code; ?>" readonly>
      </div>
      <div class="form-group mb-3">
        <label>اسم البرنامج:</label>
        <input type="text" class="form-control" id="program_name" name="program_name" required>
      </div>
      <div class="form-group mb-3">
        <label>رمز البرنامج:</label>
        <input type="text" class="form-control" id="program_code" name="program_code" required>
        <small id="code-msg"></small>
      </div>
      <div class="form-group mb-3">
        <label>إجمالي العقد:</label>
        <input type="number" class="form-control" id="contract_total" name="contract_total" min="0" max="20000" step="0.01" required>
      </div>
      <div class="form-group mb-3">
        <label>مدة العقد:</label>
        <div style="display: flex; align-items: center; gap: 30px; flex-wrap: wrap;">
          <input type="text" class="form-control" name="con_duration_value" style="width: 100px; margin-bottom: 0;" required>
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
      <div class="form-group mb-3">
        <label>تاريخ بداية العقد بالميلادي:</label>
        <input type="date" class="form-control" id="startGregorian" name="start_gregorian" required>
      </div>
      <div class="form-group mb-3">
        <label>تاريخ بداية العقد بالهجري:</label>
        <input type="text" class="form-control" id="startHijri" readonly>
      </div>
      <div class="form-buttons">
        <button type="button" class="buttons" onclick="goStep(1)">السابق</button>
        <button type="button" class="buttons" onclick="goStep(3)">التالي</button>
      </div>
    </div> <!-- نهاية خطوة 2 -->

    <!-- خطوة 3 -->
    <div class="f-container" id="step3">
      <div class="form-group mb-3">
        <label>الاسم:</label>
        <select id="party3Name" name="party3" class="form-control">
          <?php foreach ($employee as $name => $data): ?>
            <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group mb-3">
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
      <div class="form-buttons">
        <button type="button" class="buttons" onclick="goStep(2)">السابق</button>
        <button type="submit" class="buttons">إرسال</button>
      </div>
    </div> <!-- نهاية خطوة 3 -->

  </div> <!-- نهاية r-container -->
</form>


<script>

  window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('party3Name').dispatchEvent(new Event('change'));
  });
  document.addEventListener("DOMContentLoaded", function () {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const formattedToday = `${yyyy}-${mm}-${dd}`;
    document.getElementById('gregorianDate').value = formattedToday;
    document.getElementById('hijriDate').value = moment(formattedToday, 'YYYY-MM-DD').format('iYYYY/iMM/iDD');
    const daysArabic = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    document.getElementById('dayName').value = daysArabic[today.getDay()];
    
    goStep(1);
  });

  document.getElementById('gregorianDate').addEventListener('change', function () {
    const date = this.value;
    document.getElementById('hijriDate').value = moment(date, 'YYYY-MM-DD').format('iYYYY/iMM/iDD');
    const daysArabic = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    document.getElementById('dayName').value = daysArabic[new Date(date).getDay()];
  });

  document.getElementById('startGregorian').addEventListener('change', function () {
    const date = this.value;
    document.getElementById('startHijri').value = moment(date, 'YYYY-MM-DD').format('iYYYY/iMM/iDD');
  });

  function goStep(step) {
    // إخفاء كل خطوات النموذج
    document.querySelectorAll('.f-container').forEach(container => {
      container.style.display = 'none';
    });

    // إظهار الخطوة المطلوبة فقط
    const currentStep = document.getElementById(`step${step}`);
    if (currentStep) {
      currentStep.style.display = 'block';
    }

    // تعطيل كل تبويبات التنقل أولاً
    document.querySelectorAll('#contractTabs .nav-link').forEach(link => {
      link.classList.add('disabled');
      link.classList.remove('active');
    });

    // تفعيل التبويب الخاص بالخطوة الحالية
    const activeTab = document.querySelector(`#contractTabs a[href="#step${step}"]`);
    if (activeTab) {
      activeTab.classList.remove('disabled');
      activeTab.classList.add('active');
    }
  }


  document.querySelectorAll('#contractTabs .nav-link.disabled').forEach(el => {
    el.addEventListener('click', e => e.preventDefault());
  });

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
</body>
</html>
