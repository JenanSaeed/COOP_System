<?php
session_start();
require_once("db_connect.php");

// Authentication checks
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$emp_id = $_SESSION['emp_id'] ?? null;
if (!$emp_id) {
    header("Location: login.php");
    exit();
}

// Fetch vacation history
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
    <style>
        .vacation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .vacation-table th, .vacation-table td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .vacation-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .vacation-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .vacation-table tr:hover {
            background-color: #f1f1f1;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .new-request-btn {
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .vacation-table {
                font-size: 0.85rem;
            }
            .vacation-table th, .vacation-table td {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'header.php'; ?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>طلبات الإجازات</h2>
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
                <table class="vacation-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>نوع الإجازة</th>
                            <th>المدة</th>
                            <th>من تاريخ</th>
                            <th>إلى تاريخ</th>
                            <th>تاريخ الطلب</th>
                            <th>اسم المكلف</th>
                            <th>حالة الموافقة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vacations as $vac): ?>
                            <tr>
                                <td><?= $vac['vac_id'] ?></td>
                                <td><?= htmlspecialchars($vac['type']) ?></td>
                                <td><?= $vac['days'] ?> يوم</td>
                                <td><?= $vac['start_date'] ?></td>
                                <td><?= $vac['end_date'] ?></td>
                                <td><?= $vac['app_date'] ?></td>
                                <td><?= htmlspecialchars($vac['assigned_emp'] ?? '—') ?></td>
                                <td>
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
                                    <span class="status-badge <?= $class ?>"><?= $status ?></span>
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