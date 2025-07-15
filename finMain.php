<?php
session_start();
require_once("db_connect.php");

// Check login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'finance') {
    header("Location: homepage.php");
    exit();
}

// Fetch vacations
try {
    $stmt = $conn->prepare("SELECT 
        v.vac_id,
        v.application_date,
        v.fin_approval,
        v.man_approval,
        e.name AS employee_name
    FROM vacation v
    JOIN employee e ON v.emp_id = e.emp_id
    ORDER BY v.application_date DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $vacations = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "خطأ في تحميل الطلبات: " . $e->getMessage();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلبات الإجازات - الشؤون المالية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">طلبات الإجازات لجميع الموظفين</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($vacations)): ?>
        <div class="alert alert-info">لا توجد طلبات إجازة</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="vacation-table table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>اسم الموظف</th>
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

                            if ($vac['fin_approval'] === 'مقبول') {
                                $status = 'مقبول';
                                $class = 'status-approved';
                            } elseif ($vac['fin_approval'] === 'مرفوض') {
                                $status = 'مرفوض';
                                $class = 'status-rejected';
                            }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($vac['employee_name']) ?></td>
                            <td><?= date('Y-m-d', strtotime($vac['application_date'])) ?></td>
                            <td><span class="status-badge <?= $class ?>"><?= $status ?></span></td>
                            <td class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="empVacDet1.php?vac_id=<?= $vac['vac_id'] ?>" class="btn-det">تفاصيل</a>
                                <?php if (($vac['fin_approval'] === 'مقبول' && $vac['man_approval'] === 'معتمد') || 
                                        ($vac['fin_approval'] === 'مرفوض' && $vac['man_approval'] === 'معتمد')): ?>
                                <a href="empVacDet3.php?vac_id=<?= $vac['vac_id'] ?>" class="btn-prnt" target="_blank">تحميل PDF</a>
                                <?php endif; ?>
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
