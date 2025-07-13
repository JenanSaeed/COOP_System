<?php
session_start();
require_once("db_connect.php");

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$vac_id = $_GET['vac_id'] ?? null;
if (!$vac_id) {
    echo "رقم الطلب غير موجود.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $approval = $_POST['approval'] ?? null;

    if ($approval === 'مقبول' || $approval === 'مرفوض') {
        $stmt = $conn->prepare("UPDATE vacation SET fin_approval = ? WHERE vac_id = ?");
        $stmt->bind_param("si", $approval, $vac_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header("Location: finMain.php");
        exit();
    } else {
        $error = "الرجاء تحديد حالة الإجازة.";
    }
}

// جلب بيانات الإجازة الحالية
$stmt = $conn->prepare("SELECT v.*, e.used_days, e.remaining_days FROM vacation v JOIN employee e ON v.emp_id = e.emp_id WHERE v.vac_id = ?");
$stmt->bind_param("i", $vac_id);
$stmt->execute();
$result = $stmt->get_result();
$vac = $result->fetch_assoc();
$stmt->close();

if (!$vac) {
    echo "لم يتم العثور على الطلب.";
    exit();
}

// جلب آخر إجازة سابقة لهذا الموظف (غير الحالية)
$emp_id = $vac['emp_id'];
$stmt = $conn->prepare("
    SELECT * FROM vacation 
    WHERE emp_id = ? AND man_approval = 'مقبول' AND vac_id != ?
    ORDER BY end_date DESC LIMIT 1
");
$stmt->bind_param("ii", $emp_id, $vac_id);
$stmt->execute();
$last_result = $stmt->get_result();
$last_vac = $last_result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>تفاصيل الطلب - مركز التعليم المستمر</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />
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
      <label>رصيد الموظف المستهلك:</label>
      <input type="text" value="<?= htmlspecialchars($vac['used_days']) ?> يومًا" disabled>
    </div>

    <div class="form-group">
      <label>رصيد الموظف المتبقي:</label>
      <input type="text" value="<?= htmlspecialchars($vac['remaining_days']) ?> يومًا" disabled>
    </div>

    <div class="form-group">
      <label>تاريخ آخر إجازة تمتع بها الموظف:</label>
      <input type="text" value="<?php 
        if ($last_vac) {
          echo htmlspecialchars("بتاريخ {$last_vac['start_date']} إلى {$last_vac['end_date']} ومدتها {$last_vac['days']} يومًا");
        } else {
          echo "لا توجد إجازات سابقة.";
        }
      ?>" disabled>
    </div>

    <div class="form-group">
      <label>هل الإجازة مستحقة نظاماً؟</label>
      <div class="radio-group">
        <label><input type="radio" name="approval" value="مقبول"> الإجازة مستحقة نظاماً</label>
        <label><input type="radio" name="approval" value="مرفوض"> الإجازة غير مستحقة نظاماً</label>
      </div>
    </div>

    <div class="form-buttons">
      <a href="empVecDet1.php?vac_id=<?= $vac['vac_id'] ?>" class="buttons">عودة</a>
      <button type="submit" class="buttons">إرسال الطلب</button>
    </div>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
