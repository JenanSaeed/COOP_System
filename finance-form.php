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

// Fetch vacation info
$stmt = $conn->prepare("SELECT * FROM vacation WHERE vac_id = ?");
$stmt->bind_param("i", $vac_id);
$stmt->execute();
$result = $stmt->get_result();
$vac = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$vac) {
    echo "لم يتم العثور على الطلب.";
    exit();
}
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
    </div>

    <div class="form-group">
      <label>تاريخ الطلب:</label>
      <input type="text" value="<?= htmlspecialchars($vac['application_date']) ?>" disabled>
    </div>

    <div class="form-group">
      <label>اسم المكلف أثناء الإجازة:</label>
      <input type="text" value="<?= htmlspecialchars($vac['assigned_emp']) ?>" disabled>
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
