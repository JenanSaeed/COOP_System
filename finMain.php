<?php
session_start();
require_once("db_connect.php");

// Check login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'finance') {
    header("Location: homepage.php");
    exit();
}

// Get filters from GET
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'app_date_desc'; // default sorting
$status_filter = $_GET['status'] ?? 'all';

// Build WHERE clauses dynamically
$where_clauses = [];
$params = [];
$types = "";

// Search by employee name, vac_id, or date
if (!empty($search)) {
    $where_clauses[] = "(e.name LIKE ? OR v.vac_id LIKE ? OR DATE_FORMAT(v.application_date, '%Y-%m-%d') LIKE ?)";
    $search_like = "%$search%";
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
    $types .= "sss";
}

// Filter by status
if ($status_filter === 'approved') {
    $where_clauses[] = "v.fin_approval = 'مقبول' AND v.man_approval = 'معتمد'";
} elseif ($status_filter === 'rejected') {
    $where_clauses[] = "v.fin_approval = 'مرفوض' AND v.man_approval = 'معتمد'";
} elseif ($status_filter === 'pending') {
    $where_clauses[] = "v.fin_approval = 'معلق' AND v.man_approval = 'معلق'";
}

// Compose final WHERE clause
$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Determine ORDER BY clause
$order_clause = "ORDER BY v.application_date DESC";
if ($sort === 'app_date_asc') $order_clause = "ORDER BY v.application_date ASC";
elseif ($sort === 'vac_id_asc') $order_clause = "ORDER BY v.vac_id ASC";
elseif ($sort === 'vac_id_desc') $order_clause = "ORDER BY v.vac_id DESC";

try {
    $sql = "SELECT 
        v.vac_id,
        v.application_date,
        v.fin_approval,
        v.man_approval,
        e.name AS employee_name
        FROM vacation v
        JOIN employee e ON v.emp_id = e.emp_id
        $where_sql
        $order_clause";

    $stmt = $conn->prepare($sql);

    if ($types !== "") {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $vacations = $result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $error = "خطأ في تحميل الطلبات: " . $e->getMessage();
}

$conn->close();

$current_query = http_build_query($_GET);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>طلبات الإجازات - الشؤون المالية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />
    <style>
        body { font-family: 'Cairo', sans-serif; }
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
    </style>
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="r-container">
    <h2 class="mb-4">طلبات الإجازات لجميع الموظفين</h2>

    <!-- Search, Filter & Sort Form -->
    <form method="GET" class="mb-4 d-flex flex-wrap gap-2 align-items-center">
        <input
            type="text"
            name="search"
            placeholder="ابحث باسم الموظف، رقم الطلب، أو التاريخ"
            value="<?= htmlspecialchars($search) ?>"
            class="form-control"
            style="max-width: 300px;"
        />

        <select name="status" class="form-select" style="width: 150px;">
            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>كل الحالات</option>
            <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>مقبول</option>
            <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>مرفوض</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>معلق</option>
        </select>

        <select name="sort" class="form-select" style="width: 150px;">
            <option value="app_date_desc" <?= $sort === 'app_date_desc' ? 'selected' : '' ?>>الأحدث أولاً</option>
            <option value="app_date_asc" <?= $sort === 'app_date_asc' ? 'selected' : '' ?>>الأقدم أولاً</option>
            <option value="vac_id_asc" <?= $sort === 'vac_id_asc' ? 'selected' : '' ?>>رقم الطلب تصاعديًا</option>
            <option value="vac_id_desc" <?= $sort === 'vac_id_desc' ? 'selected' : '' ?>>رقم الطلب تنازليًا</option>
        </select>

        <button type="submit" class="btn btn-secondary">تطبيق</button>
    </form>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($vacations)): ?>
        <div class="alert alert-info">لا توجد طلبات إجازة</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="vacation-table table table-bordered text-center" style="min-width: 600px;">
                <thead class="table-light">
                    <tr>
                        <th>اسم الموظف</th>
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
                            <td><?= htmlspecialchars($vac['employee_name']) ?></td>
                            <td><?= date('Y-m-d', strtotime($vac['application_date'])) ?></td>
                            <td><span class="status-badge <?= $class ?>"><?= $status ?></span></td>
                            <td class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="empVacDet1.php?vac_id=<?= $vac['vac_id'] ?>&<?= $current_query ?>" class="btn-det">تفاصيل</a>
                                <?php if (($vac['fin_approval'] === 'مقبول' && $vac['man_approval'] === 'معتمد') || 
                                        ($vac['fin_approval'] === 'مرفوض' && $vac['man_approval'] === 'معتمد')): ?>
                                <a href="v-pdf.php?vac_id=<?= urlencode($vacation['vac_id']) ?>" class="btn-prnt" target="_blank">PDF</a>
                                <a href="empVacDet3.php?vac_id=<?= $vac['vac_id'] ?>&<?= $current_query ?>" class="btn-prnt" target="_blank">PDF</a>
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
