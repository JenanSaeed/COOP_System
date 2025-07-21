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
    // Get contract info + names from employee & guest tables
    $stmt = $conn->prepare("
        SELECT 
            c.con_id, 
            c.con_date, 
            c.con_duration, 
            c.con_starting_date, 
            c.program_name, 
            c.program_id, 
            c.num_weeks, 
            c.total,
            e.name AS first_party_name,
            g.guest_name AS second_party_name
        FROM contract c
        JOIN employee e ON c.`1st_party` = e.emp_id
        JOIN guest g ON c.guest_id = g.guest_id
        WHERE c.guest_id = ?
    ");

    if (!$stmt) {
        throw new Exception("فشل الاستعلام: " . $conn->error);
    }

    $stmt->bind_param("s", $guest_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contracts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

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
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        .btn {
            padding: 6px 12px;
            background-color: #006699;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #004d80;
        }

        .contract-table {
            border-collapse: collapse;
            margin-top: 20px;
        }

        .contract-table th, .contract-table td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        .contract-table th {
            background-color: #f2f2f2;
        }

        .container {
            width: 90%;
            margin: auto;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h2>مرحباً، <?= htmlspecialchars($guestName) ?></h2>
    <h3>سجل العقود الخاصة بك</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($contracts)): ?>
        <p>لا توجد عقود مسجلة.</p>
    <?php else: ?>
        <table class="contract-table" style="width:100%; text-align:right;">
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
                    <th>تحميل</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $contract): ?>
                <tr>
                    <td><?= htmlspecialchars($contract['con_id']) ?></td>
                    <td><?= htmlspecialchars($contract['con_date']) ?></td>
                    <td><?= htmlspecialchars($contract['first_party_name']) ?></td>
                    <td><?= htmlspecialchars($contract['second_party_name']) ?></td>
                    <td><?= htmlspecialchars($contract['con_duration']) ?> شهر</td>
                    <td><?= htmlspecialchars($contract['con_starting_date']) ?></td>
                    <td><?= htmlspecialchars($contract['program_name']) ?></td>
                    <td><?= htmlspecialchars($contract['num_weeks']) ?></td>
                    <td><?= number_format($contract['total']) ?> ريال</td>
                    <td>
                        <a href="contract_pdf.php?con_id=<?= urlencode($contract['con_id']) ?>" target="_blank" class="btn">
                            تحميل PDF
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
