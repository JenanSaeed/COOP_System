<?php
session_start();
require_once("db_connect.php");

// Check login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename('PHP_SELF');
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'manager') {
    header("Location: homepage.php");
    exit();
}

// Fetch vacations pending manager approval
try {
    $stmt = $conn->prepare("SELECT 
        v.vac_id,
        v.application_date,
        v.fin_approval,
        v.man_approval,
        e.name AS employee_name
    FROM vacation v
    JOIN employee e ON v.emp_id = e.emp_id
    WHERE v.fin_approval != 'معلق'
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
    <title>طلبات الإجازات - المدير</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">طلبات الإجازات</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($vacations)): ?>
        <div class="alert alert-info">لا توجد طلبات</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="vacation-table table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>رقم الطلب</th>
                        <th>اسم الموظف</th>
                        <th>تاريخ الطلب</th>
                        <th>الموافقة المالية</th>
                        <th>الموافقة الإدارية</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                  <?php foreach ($vacations as $vac): ?>
                    <?php
                        $fin_status = $vac['fin_approval'] === 'معلق' ? 
                            '<span class="status-badge status-pending">معلق</span>' :
                            ($vac['fin_approval'] === 'مقبول' ? 
                            '<span class="status-badge status-approved">مقبول</span>' : 
                            '<span class="status-badge status-rejected">مرفوض</span>');

                        $man_status = $vac['man_approval'] === 'معلق' ? 
                            '<span class="status-badge status-pending">معلق</span>' :
                            ($vac['man_approval'] === 'مقبول' ? 
                            '<span class="status-badge status-approved">مقبول</span>' : 
                            '<span class="status-badge status-rejected">مرفوض</span>');
                    ?>
                    <tr>
                        <td><?= $vac['vac_id'] ?></td>
                        <td><?= htmlspecialchars($vac['employee_name']) ?></td>
                        <td><?= date('Y-m-d', strtotime($vac['application_date'])) ?></td>
                        <td><?= $fin_status ?></td>
                        <td><?= $man_status ?></td>
                        <td class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="validation.php?vac_id=<?= $vac['vac_id'] ?>&return_url=managerMain.php" class="btn btn-sm btn-primary">
                                تفاصيل
                            </a>
                            <a href="empVecDet3.php?vac_id=<?= $vac['vac_id'] ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                                تحميل PDF
                            </a>
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
