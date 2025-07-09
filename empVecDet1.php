<?php
session_start();
require_once("db_connect.php");

// Check login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Check for vac_id in URL
$vac_id = $_GET['vac_id'] ?? null;
if (!$vac_id) {
    echo "رقم الطلب غير موجود.";
    exit();
}

// Fetch vacation + employee name
try {
    $stmt = $conn->prepare("
        SELECT v.*, 
               DATE_FORMAT(v.start_date, '%Y-%m-%d') as start_date,
               DATE_FORMAT(v.end_date, '%Y-%m-%d') as end_date,
               DATE_FORMAT(v.application_date, '%Y-%m-%d') as app_date,
               e.name AS employee_name
        FROM vacation v
        JOIN employee e ON v.emp_id = e.emp_id
        WHERE v.vac_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $vac_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vac = $result->fetch_assoc();

    if (!$vac) {
        echo "لم يتم العثور على الطلب.";
        exit();
    }
} catch (Exception $e) {
    echo "حدث خطأ أثناء تحميل الطلب: " . $e->getMessage();
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل طلب الإجازة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        .detail-label {
            font-weight: bold;
            margin-top: 10px;
        }
        .detail-value {
            margin-bottom: 15px;
            color: #333;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">تفاصيل طلب الإجازة رقم <?= htmlspecialchars($vac['vac_id']) ?></h2>

    <div class="mb-3">
        <div class="detail-label">اسم الموظف:</div>
        <div class="detail-value"><?= htmlspecialchars($vac['employee_name']) ?></div>

        <div class="detail-label">نوع الإجازة:</div>
        <div class="detail-value"><?= htmlspecialchars($vac['type']) ?></div>

        <div class="detail-label">عدد الأيام:</div>
        <div class="detail-value"><?= htmlspecialchars($vac['days']) ?> يوم</div>

        <div class="detail-label">من تاريخ:</div>
        <div class="detail-value"><?= $vac['start_date'] ?></div>

        <div class="detail-label">إلى تاريخ:</div>
        <div class="detail-value"><?= $vac['end_date'] ?></div>

        <div class="detail-label">تاريخ تقديم الطلب:</div>
        <div class="detail-value"><?= $vac['app_date'] ?></div>

        <div class="detail-label">اسم المكلف أثناء الإجازة:</div>
        <div class="detail-value"><?= htmlspecialchars($vac['assigned_emp'] ?? '—') ?></div>

        <div class="detail-label">حالة الموافقة من المدير:</div>
        <div class="detail-value">
            <span class="<?= $vac['man_approval'] === 'مقبول' ? 'status-approved' : ($vac['man_approval'] === 'مرفوض' ? 'status-rejected' : 'status-pending') ?>">
                <?= $vac['man_approval'] ?? 'معلق' ?>
            </span>
        </div>

        <div class="detail-label">حالة الموافقة من الشؤون المالية:</div>
        <div class="detail-value">
            <span class="<?= $vac['fin_approval'] === 'مقبول' ? 'status-approved' : ($vac['fin_approval'] === 'مرفوض' ? 'status-rejected' : 'status-pending') ?>">
                <?= $vac['fin_approval'] ?? 'معلق' ?>
            </span>
        </div>
    </div>

<div class="mt-4">
    <a href="javascript:history.back()" class="btn btn-secondary">عودة</a>
    <a href="finance-form.php?vac_id=<?= $vac['vac_id'] ?>" class="btn btn-primary ms-2">متابعة</a>
</div>
</div>
<div class="container py-4 pb-5">
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
