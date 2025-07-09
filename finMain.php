<?php
session_start();
require_once("db_connect.php");

// Check login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: homepage.php");
    exit();
}

// Role redirect
if ($_SESSION['role'] === 'manager') {
    header("Location: validation.php");
    exit();
} elseif ($_SESSION['role'] === 'employee') {
    header("Location: empReqs.php");
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
        .vacation-table tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
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
    </style>
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
            <table class="vacation-table">
                <thead>
                    <tr>
                        <th>اسم الموظف</th>
                        <th>تاريخ الطلب</th>
                        <th>الحالة</th>
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
                        <tr onclick="window.location.href='empVecDet1.php?vac_id=<?= $vac['vac_id'] ?>'">
                            <td><?= htmlspecialchars($vac['employee_name']) ?></td>
                            <td><?= date('Y-m-d', strtotime($vac['application_date'])) ?></td>
                            <td><span class="status-badge <?= $class ?>"><?= $status ?></span></td>
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
