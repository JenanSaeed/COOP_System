<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}

require_once "db_connect.php";

// جلب con_id من الرابط
$con_id = $_GET['id'] ?? null;
if (!$con_id) {
    die("عذرًا، لم يتم تمرير رقم العقد.");
}

// جلب بيانات الطرف الثاني
$sql = "SELECT * FROM second_party WHERE con_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $con_id);
$stmt->execute();
$result = $stmt->get_result();

$second_party = $result->num_rows > 0 ? $result->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بيانات الطرف الثاني</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .data-row {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 150px;
        }
        .value {
            color: #333;
        }
    </style>
</head>
<body class="bg-light">

<?php include 'header.php'; ?>

<main>
    <div class="r-container">
        <h2 class="form-title">بيانات الطرف الثاني</h2>

        <?php if ($second_party): ?>
            <div class="data-row"><span class="label">الاسم:</span> <span class="value"><?= htmlspecialchars($second_party['name']) ?></span></div>
            <div class="data-row"><span class="label">رقم الهوية:</span> <span class="value"><?= htmlspecialchars($second_party['id_number']) ?></span></div>
            <div class="data-row"><span class="label">الصفة:</span> <span class="value"><?= htmlspecialchars($second_party['role']) ?></span></div>
            <div class="data-row"><span class="label">الجنسية:</span> <span class="value"><?= htmlspecialchars($second_party['nationality']) ?></span></div>
            <div class="data-row"><span class="label">مكان الإصدار:</span> <span class="value"><?= htmlspecialchars($second_party['issue_place']) ?></span></div>
            <div class="data-row"><span class="label">تاريخ الانتهاء:</span> <span class="value"><?= htmlspecialchars($second_party['expiry_date']) ?></span></div>
            <div class="data-row"><span class="label">العنوان:</span> <span class="value"><?= htmlspecialchars($second_party['address']) ?></span></div>
            <div class="data-row"><span class="label">رقم الجوال:</span> <span class="value"><?= htmlspecialchars($second_party['phone']) ?></span></div>
            <div class="data-row"><span class="label">البريد الإلكتروني:</span> <span class="value"><?= htmlspecialchars($second_party['email']) ?></span></div>
            <div class="data-row"><span class="label">البنك:</span> <span class="value"><?= htmlspecialchars($second_party['bank']) ?></span></div>
            <div class="data-row"><span class="label">رقم الآيبان:</span> <span class="value"><?= htmlspecialchars($second_party['iban']) ?></span></div>
        <?php else: ?>
            <p style="text-align:center; color: #666;">لا توجد بيانات للطرف الثاني لهذا العقد.</p>
        <?php endif; ?>
        <div class="form-buttons">
            <button class="buttons" onclick="location.href='c-adminRec.php'">عودة</button>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
