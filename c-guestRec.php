<?php
session_start();
require_once("db_connect.php");

// Check if user is logged in and is a guest
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'guest') {
    header("Location: login.php");
    exit();
}

$guest_id = $_SESSION['guest_id'];

try {
    $stmt = $conn->prepare("SELECT con_id, con_date, 1st_party, 2nd_party, con_duration, con_starting_date, program_name, program_id, num_weeks, total 
                            FROM contract 
                            WHERE guest_id = ? 
                            ORDER BY con_date DESC");
    $stmt->bind_param("i", $guest_id);
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
    <title>عقودي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .table th, .table td { vertical-align: middle; text-align: center; }
    </style>
</head>
<body class="bg-light">

<?php include 'header.php'; ?>

<div class="container py-4">
    <h2 class="mb-4">عقودي</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($contracts)): ?>
        <div class="alert alert-info">لا توجد عقود متاحة.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>رقم العقد</th>
                        <th>تاريخ العقد</th>
                        <th>الطرف الأول</th>
                        <th>الطرف الثاني</th>
                        <th>مدة العقد</th>
                        <th>تاريخ بداية العقد</th>
                        <th>اسم البرنامج</th>
                        <th>رقم البرنامج</th>
                        <th>عدد الأسابيع</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts as $contract): ?>
                        <tr>
                            <td><?= htmlspecialchars($contract['con_id']) ?></td>
                            <td><?= htmlspecialchars($contract['con_date']) ?></td>
                            <td><?= htmlspecialchars($contract['1st_party']) ?></td>
                            <td><?= htmlspecialchars($contract['2nd_party']) ?></td>
                            <td><?= htmlspecialchars($contract['con_duration']) ?></td>
                            <td><?= htmlspecialchars($contract['con_starting_date']) ?></td>
                            <td><?= htmlspecialchars($contract['program_name']) ?></td>
                            <td><?= htmlspecialchars($contract['program_id']) ?></td>
                            <td><?= htmlspecialchars($contract['num_weeks']) ?></td>
                            <td><?= htmlspecialchars($contract['total']) ?></td>
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
