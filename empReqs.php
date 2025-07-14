<?php
session_start();
require_once("db_connect.php");

// تحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename('PHP_SELF');
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'employee') {
    header("Location: homepage.php");
    exit();
}

$emp_id = $_SESSION['emp_id'] ?? null;
if (!$emp_id) {
    header("Location: login.php");
    exit();
}

// جلب بيانات الإجازات
try {
    $stmt = $conn->prepare("SELECT 
        vac_id, 
        type, 
        days, 
        DATE_FORMAT(start_date, '%Y-%m-%d') as start_date,
        DATE_FORMAT(end_date, '%Y-%m-%d') as end_date,
        DATE_FORMAT(application_date, '%Y-%m-%d') as app_date,
        fin_approval, 
        man_approval,
        assigned_emp
        FROM vacation 
        WHERE emp_id = ? 
        ORDER BY application_date DESC");

    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $vacations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "حدث خطأ في تحميل البيانات: " . $e->getMessage();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات الإجازات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'header.php'; ?>


    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>طلبات الإجازات</h2>
        </div>
        
        <div class="newreqs">
            <a href="emp-form.php" class="btn btn-primary new-request-btn">
            <i class="fas fa-plus"></i> طلب إجازة جديدة
            </a>
            </div>


    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($vacations)): ?>
        <div class="alert alert-info">لا توجد طلبات إجازة مسجلة</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="vacation-table table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>رقم الطلب</th>
                        <th>تاريخ الطلب</th>
                        <th>الحالة</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vacations as $vac): ?>
                        <?php
                            $status = 'معلق';
                            $class = 'status-pending';
                            if ($vac['fin_approval'] === 'مقبول' && $vac['man_approval'] === 'مقبول') {
                                $status = 'مقبول';
                                $class = 'status-approved';
                            } elseif ($vac['fin_approval'] === 'مرفوض' || $vac['man_approval'] === 'مرفوض') {
                                $status = 'مرفوض';
                                $class = 'status-rejected';
                            }
                        ?>
                        <tr>
                            <td><?= $vac['vac_id'] ?></td>
                            <td><?= $vac['app_date'] ?></td>
                            <td><span class="status-badge <?= $class ?>"><?= $status ?></span></td>
                            <td class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="empVecDet2.php?vac_id=<?= $vac['vac_id'] ?>" class="btn btn-sm btn-primary">تفاصيل</a>
                                <a href="empVecDet3.php?vac_id=<?= $vac['vac_id'] ?>" class="btn btn-sm btn-outline-secondary" target="_blank">تحميل PDF</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
