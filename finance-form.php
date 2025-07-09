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
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f7f7f7;
    }
    .request-page {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .form-title {
      text-align: center;
      margin-bottom: 25px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      font-weight: bold;
      display: block;
      margin-bottom: 6px;
    }
    .form-group input {
      width: 100%;
      padding: 8px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .form-buttons {
      text-align: center;
      margin-top: 30px;
    }
    .submit-button, .cancel-button {
      padding: 10px 25px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .submit-button {
      background-color: #007bff;
      color: white;
      margin-right: 10px;
    }
    .cancel-button {
      background-color: #6c757d;
      color: white;
    }
    .radio-group {
      display: flex;
      gap: 15px;
      margin-top: 10px;
    }
    .radio-group label {
      font-weight: normal;
    }
    .error-message {
      color: red;
      text-align: center;
      margin-bottom: 20px;
    }
  </style>
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
      <a href="empVecDet1.php?vac_id=<?= $vac['vac_id'] ?>" class="cancel-button">عودة</a>
      <button type="submit" class="submit-button">إرسال الطلب</button>
    </div>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
