<?php
session_start();
require_once("db_connect.php");

// تحقق من تسجيل الدخول والدور
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'manager') {
    header("Location: homepage.php");
    exit();
}

$vac_id = $_GET['vac_id'] ?? null;
$return_url = $_GET['return_url'] ?? 'manMain.php'; // الصفحة اللي يرجع لها بعد الإجراء

if (!$vac_id) {
    die("رقم الطلب غير موجود.");
}

$error = '';
$success = '';

// معالجة اعتماد الطلب فقط
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
    try {
        // 1. تحديث الموافقة
        $stmt = $conn->prepare("UPDATE vacation SET man_approval = 'معتمد' WHERE vac_id = ?");
        $stmt->bind_param("i", $vac_id);
        $stmt->execute();
        $stmt->close();

        // 2. جلب بيانات الإجازة لتحديث جدول الموظف
        $stmt = $conn->prepare("SELECT emp_id, days, end_date FROM vacation WHERE vac_id = ?");
        $stmt->bind_param("i", $vac_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vac = $result->fetch_assoc();
        $stmt->close();

        if ($vac) {
            $emp_id = $vac['emp_id'];
            $days = $vac['days'];
            $end_date = $vac['end_date'];

            // 3. تحديث جدول الموظف
            $stmt = $conn->prepare("
                UPDATE employee 
                SET 
                    used_days = used_days + ?, 
                    remaining_days = remaining_days - ?, 
                    last_vac = ?
                WHERE emp_id = ?
            ");
            $stmt->bind_param("iisi", $days, $days, $end_date, $emp_id);
            $stmt->execute();
            $stmt->close();
        }

        // 4. إعادة التوجيه
        header("Location: manMain.php");
        exit();

    } catch (Exception $e) {
        $error = "حدث خطأ: " . $e->getMessage();
    }
}

// جلب بيانات الطلب
try {
    $stmt = $conn->prepare("SELECT v.*, e.name AS employee_name FROM vacation v JOIN employee e ON v.emp_id = e.emp_id WHERE v.vac_id = ?");
    $stmt->bind_param("i", $vac_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vacation = $result->fetch_assoc();

    if (!$vacation) {
        die("الطلب غير موجود.");
    }
    $stmt->close();
} catch (Exception $e) {
    die("خطأ في جلب بيانات الطلب: " . $e->getMessage());
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>تفاصيل طلب الإجازة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">تفاصيل طلب الإجازة</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">

        <div class="card p-4 mb-4">
            <h4>اسم الموظف: <?= htmlspecialchars($vacation['employee_name']) ?></h4>
            <p><strong>نوع الإجازة:</strong> <?= htmlspecialchars($vacation['type']) ?></p>
            <p><strong>مدة الإجازة:</strong> <?= htmlspecialchars($vacation['days']) ?> يوم</p>
            <p><strong>من تاريخ:</strong> <?= htmlspecialchars($vacation['start_date']) ?></p>
            <p><strong>إلى تاريخ:</strong> <?= htmlspecialchars($vacation['end_date']) ?></p>
            <p><strong>الشخص المكلف:</strong> <?= htmlspecialchars($vacation['assigned_emp']) ?></p>
            <p><strong>تاريخ التقديم:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($vacation['application_date']))) ?></p>
            <p><strong>حالة الموافقة الإدارية:</strong> <?= htmlspecialchars($vacation['man_approval']) ?></p>
            <p><strong>حالة الموافقة المالية:</strong> <?= htmlspecialchars($vacation['fin_approval']) ?></p>
        </div>
        <div class="form-buttons">
            <?php if ($vacation['man_approval'] === 'معلق'): ?>
            <button type="submit" name="approve" class="buttons">اعتماد</button>
            <?php endif; ?>
            <button type="button" class="buttons" onclick="location.href='manMain.php'">إلغاء</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
