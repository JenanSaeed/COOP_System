<?php
session_start();
require_once("db_connect.php");


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_after_login'] = 'empReqs.php';
    header("Location: login.php");
    exit();
}


if ($_SESSION['role'] === 'finance') {
    header("Location: finMain.php");
    exit();
} elseif ($_SESSION['role'] === 'manager') {
    header("Location: manMain.php");
    exit();
}

$user_id = $_SESSION['emp_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit();
}


$error = '';
$vacation = [];


try {
    $select_sql = "SELECT type, application_date, approval FROM vacation WHERE emp_id = ? ORDER BY application_date DESC";
    $stmt = $conn->prepare($select_sql);
    if (!$stmt) throw new Exception("Database error: " . $conn->error);

    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vacation = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "حدث خطأ أثناء جلب سجل الإجازات: " . $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلبات الإجازات</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .status-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-left: 8px;
        }
        .status-pending { background-color: coral; }
        .status-approved { background-color: green; }
        .status-rejected { background-color: red; }
        .error-message { color: red; margin: 15px 0; }
        .vacation-card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .vac-date {
            margin-right: auto;
            color: #666;
        }
        .new-vacation-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f2f2f2;
            border-radius: 8px;
            font-weight: bold;
            color: black;
            transition: background-color 0.3s ease;
            text-decoration: none;
            font-size: 18px;
        }
        .new-vacation-link:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main class="vacation-page">
    <div class="vacation-container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>الإجازات السابقة</h1>
            <a href="emp-form.php" class="new-vacation-link">طلب إجازة جديدة +</a>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (empty($vacation)): ?>
            <p>لا توجد طلبات إجازة مسجلة.</p>
        <?php else: ?>
            <?php foreach ($vacation as $vac): ?>
                <?php
                $status_class = match($vac['approval']) {
                    'مقبول' => 'status-approved',
                    'مرفوض' => 'status-rejected',
                    default => 'status-pending'
                };
                ?>
                <div class="vacation-card">
                    <span class="status-dot <?= $status_class ?>"></span>
                    <span>
    <?= htmlspecialchars(!empty($vac['approval']) ? $vac['approval'] : 'معلق') ?>
</span>                    <span class="vac-date">
                        النوع: <?= htmlspecialchars($vac['type']) ?> |
                        التاريخ: <?= htmlspecialchars($vac['application_date']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
