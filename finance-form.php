<?php
session_start();
require_once("db_connect.php");

// تحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$vac_id = $_GET['vac_id'] ?? null;
if (!$vac_id) {
    echo "رقم الطلب غير موجود.";
    exit();
}

$error = '';

// معالجة التقديم
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $approval = $_POST['approval'] ?? null;

    if ($approval === 'مقبول' || $approval === 'مرفوض') {
        $stmt = $conn->prepare("UPDATE vacation SET fin_approval = ? WHERE vac_id = ?");
        $stmt->bind_param("si", $approval, $vac_id);
        $stmt->execute();
        $stmt->close();
        header("Location: finMain.php");
        exit();
    } else {
        $error = "الرجاء تحديد حالة الإجازة.";
    }
}

// جلب بيانات الإجازة + الموظف
$stmt = $conn->prepare("SELECT v.*, e.used_days, e.remaining_days, v.emp_id FROM vacation v JOIN employee e ON v.emp_id = e.emp_id WHERE v.vac_id = ?");
$stmt->bind_param("i", $vac_id);
$stmt->execute();
$result = $stmt->get_result();
$vac = $result->fetch_assoc();
$stmt->close();

if (!$vac) {
    echo "لم يتم العثور على الطلب.";
    $conn->close();
    exit();
}

// جلب آخر إجازة سابقة
$emp_id = $vac['emp_id'];
$stmt = $conn->prepare("
    SELECT start_date, end_date, days 
    FROM vacation 
    WHERE emp_id = ? AND man_approval = 'مقبول' AND vac_id != ?
    ORDER BY end_date DESC 
    LIMIT 1
");
$stmt->bind_param("ii", $emp_id, $vac_id);
$stmt->execute();
$last_result = $stmt->get_result();
$last_vac = $last_result->fetch_assoc();
$stmt->close();

// إغلاق الاتصال بعد الانتهاء من جميع العمليات
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>تفاصيل الطلب - مركز التعليم المستمر</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>

<main class="request-page">
  <h2 class="form-title">تفاصيل الطلب رقم <?= htmlspecialchars($vac['vac_id']) ?></h2>

  <?php if (!empty($error)): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" class="leave-form">
    <div class="form-group">
      <label>نوع الإجازة:</label>
      <input type="text" value="<?= htmlspecialchars($vac['type']) ?>" disabled>
    </div>

    <div class="form-group">
      <label>عدد الأيام:</label>
      <input type="text" value="<?= htmlspecialchars($vac['days']) ?> يوم" disabled>
    </div>

    <div class="form-group">
      <label>من تاريخ:</label>
      <input type="text" value="<?= htmlspecialchars($vac['start_date']) ?>" disabled>
    </div>

    <div class="form-group">
      <label>إلى تاريخ:</label>
      <input type="text" value="<?= htmlspecialchars($vac['end_date']) ?>" disabled>
      <label>رصيد الموظف المستهلك:</label>
      <input type="text" value="<?= htmlspecialchars($vac['used_days']) ?> يومًا" disabled>
    </div>

    <div class="form-group">
      <label>تاريخ الطلب:</label>
      <input type="text" value="<?= htmlspecialchars($vac['application_date']) ?>" disabled>
      <label>رصيد الموظف المتبقي:</label>
      <input type="text" value="<?= htmlspecialchars($vac['remaining_days']) ?> يومًا" disabled>
    </div>

    <div class="form-group">
      <label>اسم المكلف أثناء الإجازة:</label>
      <input type="text" value="<?= htmlspecialchars($vac['assigned_emp']) ?>" disabled>
      <label>تاريخ آخر إجازة تمتع بها الموظف:</label>
      <input type="text" value="<?php 
        if ($last_vac) {
          echo "بتاريخ {$last_vac['start_date']} إلى {$last_vac['end_date']} ومدتها {$last_vac['days']} يومًا";
        } else {
          echo "لا توجد إجازات سابقة.";
        }
      ?>" disabled>
    </div>

    <div class="form-group">
      <label>هل الإجازة مستحقة نظامًا؟</label>
      <div class="radio-group">
        <label><input type="radio" name="approval" value="مقبول"> الإجازة مستحقة نظامًا</label>
        <label><input type="radio" name="approval" value="مرفوض"> الإجازة غير مستحقة نظامًا</label>
      </div>
    </div>

    <div class="form-buttons">
      <button type="button" class="buttons" onclick="location.href='empVecDet1.php?vac_id=<?= $vac['vac_id'] ?>'">عودة</button>
      <button type="submit" class="buttons">إرسال الطلب</button>
    </div>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
