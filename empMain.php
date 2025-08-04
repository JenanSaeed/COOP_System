<?php
session_start();
require_once("db_connect.php");
include_once("reset_days.php");


// تحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'employee') {
    header("Location: homepage.php");
    exit();
}

$emp_id = $_SESSION['emp_id'] ?? null;
if (!$emp_id) {
    header("Location: login.php");
    exit();
}

$action = $_POST['action'] ?? '';

// --- Handle delete action ---
if ($action === 'delete_vac' && isset($_POST['vac_id'])) {
    $vac_id = (int) $_POST['vac_id'];

    // حذف الإجازة من قاعدة البيانات
    $stmt = $conn->prepare("DELETE FROM vacation WHERE vac_id = ?");
    $stmt->bind_param("i", $vac_id);
    $stmt->execute();
    $stmt->close();

    // إعادة التوجيه لصفحة الإجازات
    header("Location: empMain.php");
    exit();
}

// --- Handle search, filter, sort ---
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'app_date_desc'; // default sorting
$status_filter = $_GET['status'] ?? 'all';

$where_clauses = ["emp_id = ?"];
$params = [$emp_id];
$types = "s";

if (!empty($search)) {
    $where_clauses[] = "(vac_id LIKE ? OR DATE_FORMAT(application_date, '%Y-%m-%d') LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

if ($status_filter === 'approved') {
    $where_clauses[] = "fin_approval = 'مقبول' AND man_approval = 'معتمد'";
} elseif ($status_filter === 'rejected') {
    $where_clauses[] = "fin_approval = 'مرفوض' AND man_approval = 'معتمد'";
} elseif ($status_filter === 'pending') {
    $where_clauses[] = "fin_approval = 'معلق' AND man_approval = 'معلق'";
}

$order_clause = "ORDER BY application_date DESC";
if ($sort === 'app_date_asc') $order_clause = "ORDER BY application_date ASC";
elseif ($sort === 'vac_id_asc') $order_clause = "ORDER BY vac_id ASC";
elseif ($sort === 'vac_id_desc') $order_clause = "ORDER BY vac_id DESC";

$where_sql = implode(" AND ", $where_clauses);
$sql = "SELECT 
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
    WHERE $where_sql 
    $order_clause";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $vacations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "حدث خطأ في تحميل البيانات: " . $e->getMessage();
}

$conn->close();

$current_query = http_build_query($_GET);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>طلبات الإجازات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .status-pending { background: #f0ad4e; color: #fff; padding: 3px 8px; border-radius: 4px; }
        .status-approved { background: #5cb85c; color: #fff; padding: 3px 8px; border-radius: 4px; }
        .status-rejected { background: #d9534f; color: #fff; padding: 3px 8px; border-radius: 4px; }
        .btn-det, .btn-prnt {
            display: inline-block;
            padding: 4px 10px;
            font-size: 0.9rem;
            color: white;
            background-color: #0d6efd;
            border-radius: 4px;
            text-decoration: none;
            margin: 2px;
        }
        .btn-prnt { background-color: #198754; }
        .delete-button {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 1.1rem;
        }
    </style>
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="r-container container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>طلبات الإجازات</h2>
    </div>
    
    <div class="newreqs mb-3">
        <a href="emp-form.php" class="btn btn-primary new-request-btn">
            <i class="fas fa-plus"></i> طلب إجازة جديدة
        </a>
    </div>

    <!-- Search, Filter & Sort Form -->
    <form method="GET" class="mb-4 d-flex flex-wrap gap-2 align-items-center">
        <input
            type="text"
            name="search"
            placeholder="ابحث برقم الطلب أو التاريخ"
            value="<?= htmlspecialchars($search) ?>"
            class="form-control w-auto"
        />
        
        <select name="status" class="form-select w-auto">
            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>كل الحالات</option>
            <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>مقبول</option>
            <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>مرفوض</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>معلق</option>
        </select>

        <select name="sort" class="form-select w-auto">
            <option value="app_date_desc" <?= $sort === 'app_date_desc' ? 'selected' : '' ?>>الأحدث أولًا</option>
            <option value="app_date_asc" <?= $sort === 'app_date_asc' ? 'selected' : '' ?>>الأقدم أولًا</option>
            <option value="vac_id_asc" <?= $sort === 'vac_id_asc' ? 'selected' : '' ?>>رقم الطلب تصاعديًا</option>
            <option value="vac_id_desc" <?= $sort === 'vac_id_desc' ? 'selected' : '' ?>>رقم الطلب تنازليًا</option>
        </select>

        <button type="submit" class="btn btn-secondary">تطبيق</button>
    </form>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($vacations)): ?>
        <div class="alert alert-info">لا توجد طلبات إجازة مسجلة</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="vacation-table table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>رقم الطلب</th>
                        <th>تاريخ الطلب</th>
                        <th>الحالة</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vacations as $vac): ?>
                        <?php
                            $status = 'معلق';
                            $class = 'status-pending';
                            if ($vac['fin_approval'] === 'مقبول' && $vac['man_approval'] === 'معتمد') {
                                $status = 'مقبول';
                                $class = 'status-approved';
                            } elseif ($vac['fin_approval'] === 'مرفوض' && $vac['man_approval'] === 'معتمد') {
                                $status = 'مرفوض';
                                $class = 'status-rejected';
                            }
                        ?>
                        <tr>
                            <td><?= $vac['vac_id'] ?></td>
                            <td><?= $vac['app_date'] ?></td>
                            <td><span class="status-badge <?= $class ?>"><?= $status ?></span></td>
                            <td class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="empVacDet2.php?vac_id=<?= $vac['vac_id'] ?>&<?= $current_query ?>" class="btn-det">تفاصيل</a>
                                <?php if (($vac['fin_approval'] === 'مقبول' && $vac['man_approval'] === 'معتمد') || 
                                        ($vac['fin_approval'] === 'مرفوض' && $vac['man_approval'] === 'معتمد')): ?>
                                <a href="v-pdf.php?vac_id=<?= urlencode($vacation['vac_id']) ?>" class="btn-prnt" target="_blank">PDF</a>
                                <?php endif; ?>   
                                <?php if ($vac['man_approval'] === 'معلق' && $vac['fin_approval'] === 'معلق'): ?>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="action" value="delete_vac" />
                                    <input type="hidden" name="vac_id" value="<?= $vac['vac_id'] ?>" />
                                    <button type="submit" class="delete-button" title="حذف الإجازة" onclick="return confirm('هل أنت متأكد من حذف الإجازة؟');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                <?php endif; ?>                         
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
