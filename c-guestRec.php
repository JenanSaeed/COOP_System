<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'guest') {
    header("Location: login.php");
    exit();
}

require_once("db_connect.php");

$guest_id = $_SESSION['guest_id'];
$guestName = $_SESSION['name'];

try {
    $stmt = $conn->prepare("SELECT con_id, con_date, 1st_party, 2nd_party, con_duration, con_starting_date, program_name, program_id, num_weeks, total 
                            FROM contract 
                            WHERE guest_id = ?");
    $stmt->bind_param("s", $guest_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contracts = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "حدث خطأ أثناء جلب بيانات العقود: " . $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>سجل العقود - <?= htmlspecialchars($guestName) ?></title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet" />
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h2>مرحبا، <?= htmlspecialchars($guestName) ?></h2>
    <h3>سجل العقود الخاصة بك</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($contracts)): ?>
        <p>لا توجد عقود مسجلة.</p>
    <?php else: ?>
        <table class="contract-table" border="1" cellpadding="10" style="width:100%; text-align:right;">
            <thead>
                <tr>
                    <th>رقم العقد</th>
                    <th>تاريخ العقد</th>
                    <th>الطرف الأول</th>
                    <th>الطرف الثاني</th>
                    <th>مدة العقد</th>
                    <th>تاريخ البداية</th>
                    <th>اسم البرنامج</th>
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
                    <td><?= htmlspecialchars($contract['num_weeks']) ?></td>
                    <td><?= htmlspecialchars($contract['total']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
