<?php
session_start();
require_once("db_connect.php");

// التحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}

// منع الزوار من الدخول
if ($_SESSION['role'] == 'guest') {
    header("Location: homepage.php");
    exit();
}

// جلب العقود
try {
    $stmt = $conn->prepare("SELECT 
        con_id,
        con_date,
        1st_party,
        2nd_party,
        con_duration,
        con_starting_date,
        program_name,
        program_id,
        total,
        con_status,
        guest_status
    FROM contract
    ORDER BY con_date DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $contracts = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "حدث خطأ أثناء تحميل العقود: " . $e->getMessage();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سجل العقود</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .vacation-table {
            width: 100%;
            border-collapse: collapse;
        }
        .vacation-table th, .vacation-table td {
            padding: 12px;
            text-align: right;
            border: 1px solid #ddd;
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
        .btn-det {
            display: inline-block;
            padding: 6px 12px;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-left: 5px;
        }
        .btn-det:hover {
            background-color: #0b5ed7;
            color: white;
        }
        .btn-pdf {
            display: inline-block;
            padding: 6px 12px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-pdf:hover {
            background-color: #bb2d3b;
            color: white;
        }
    </style>
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">سجل العقود</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($contracts)): ?>
        <div class="alert alert-info">لا توجد عقود حاليًا</div>
    <?php else: ?>
        <table class="vacation-table">
            <thead>
                <tr>
                    <th>رقم العقد</th>
                    <th>تاريخ العقد</th>
                    <th>اسم الطرف الأول</th>
                    <th>اسم الطرف الثاني</th>
                    <th>مدة العقد</th>
                    <th>تاريخ البداية</th>
                    <th>اسم البرنامج</th>
                    <th>الإجمالي</th>
                    <th>خيارات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $con): ?>
                <tr onclick="window.location.href='c-adminForm1.php?con_id=<?= $con['con_id'] ?>&return_url=c-adminRec.php'" style="cursor:pointer;">
                    <td><?= $con['con_id'] ?></td>
                    <td><?= htmlspecialchars($con['con_date']) ?></td>
                    <td><?= htmlspecialchars($con['1st_party']) ?></td>
                    <td><?= htmlspecialchars($con['2nd_party']) ?></td>
                    <td><?= htmlspecialchars($con['con_duration']) ?></td>
                    <td><?= htmlspecialchars($con['con_starting_date']) ?></td>
                    <td><?= htmlspecialchars($con['program_name']) ?></td>
                    <td><?= htmlspecialchars($con['total']) ?></td>
                    <td>
                        <a href="c-adminForm1.php?con_id=<?= $con['con_id'] ?>" class="btn-det">تفاصيل</a>
                        <a href="c-pdf.php?con_id=<?= $con['con_id'] ?>" class="btn-pdf">PDF</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>