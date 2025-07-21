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

$stmt = $conn->prepare("SELECT vac_id FROM vacation WHERE vac_id = ?");
$stmt->bind_param("i", $vac_id);
$stmt->execute();
$result = $stmt->get_result();
$vacation = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$vacation) {
    echo "الطلب غير موجود.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تحميل نموذج الإجازة</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container py-5 text-center">
    <h2 class="mb-4">تحميل نموذج الإجازة</h2>

    <a href="v-pdf.php?vac_id=<?= urlencode($vacation['vac_id']) ?>" class="btn btn-outline-primary btn-lg" target="_blank">
        تحميل نموذج الإجازة PDF
    </a>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
