<?php
session_start();
require_once("db_connect.php");

// Ensure only logged-in guests can access this page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'guest') {
    header("Location: login.php");
    exit();
}

$guest_id = $_SESSION['guest_id'];
$guest_name = $_SESSION['guest_name'];

try {
    // Get all contracts belonging to this guest
    $stmt = $conn->prepare("SELECT * FROM contract WHERE guest_id = ?");
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
    <h2 class="mb-4">العقود الخاصة بك</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($contracts)): ?>
        <div class="alert alert-info">لا توجد عقود حتى الآن.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>رقم العقد</th>
                        <th>تاريخ العقد</th>
                        <th>اسم الطرف الأول</th>
                        <th>اسم الطرف الثاني</th>
                        <th>مدة العقد</th>
                        <th>تاريخ البداية</th>
                        <th>اسم البرنامج</th>
                        <th>رقم البرنامج</th>
                        <th>عدد الأسابيع</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts as $con): ?>
                        <tr>
                            <td><?= $con['con_id'] ?></td>
                            <td><?= date('Y-m-d', strtotime($con['con_date'])) ?></td>
                            <td><?= htmlspecialchars($con['1st_party']) ?></td>
                            <td><?= htmlspecialchars($con['2nd_party']) ?></td>
                            <td><?= htmlspecialchars($con['con_duration']) ?></td>
                            <td><?= date('Y-m-d', strtotime($con['con_starting_date'])) ?></td>
                            <td><?= htmlspecialchars($con['program_name']) ?></td>
                            <td><?= htmlspecialchars($con['program_id']) ?></td>
                            <td><?= htmlspecialchars($con['num_weeks']) ?></td>
                            <td><?= htmlspecialchars($con['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
