<?php
session_start();
require_once("db_connect.php");

$contract_code = $_SESSION['contract_code'] ?? '';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}

// Get filter and sort params
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'con_date_desc';
$status_filter = $_GET['status'] ?? 'all';
$guest_status_filter = $_GET['guest_status'] ?? 'all';

// Build WHERE clauses dynamically
$where_clauses = [];
$params = [];
$types = "";

// Search filter: search contract ID, parties, program name/id
if (!empty($search)) {
    $where_clauses[] = "(con_id LIKE ? OR `1st_party` LIKE ? OR `2nd_party` LIKE ? OR program_name LIKE ? OR program_id LIKE ?)";
    $search_like = "%$search%";
    for ($i = 0; $i < 5; $i++) {
        $params[] = $search_like;
        $types .= "s";
    }
}

// Filter by contract status
if ($status_filter !== 'all') {
    $where_clauses[] = "con_status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Filter by guest status
if ($guest_status_filter !== 'all') {
    $where_clauses[] = "guest_status = ?";
    $params[] = $guest_status_filter;
    $types .= "s";
}

// Compose WHERE clause
$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Sort options
$order_clause = "ORDER BY con_date DESC";
switch ($sort) {
    case 'con_date_asc':
        $order_clause = "ORDER BY con_date ASC";
        break;
    case 'con_id_asc':
        $order_clause = "ORDER BY con_id ASC";
        break;
    case 'con_id_desc':
        $order_clause = "ORDER BY con_id DESC";
        break;
    case 'con_date_desc':
    default:
        $order_clause = "ORDER BY con_date DESC";
        break;
}

// Fetch contracts
try {
    $sql = "SELECT 
        con_id,
        con_date,
        `1st_party`,
        `2nd_party`,
        con_duration,
        con_starting_date,
        program_name,
        program_id,
        total,
        con_status,
        guest_status
    FROM contract
    $where_sql
    $order_clause";

    $stmt = $conn->prepare($sql);

    if ($types !== "") {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $contracts = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "حدث خطأ أثناء تحميل العقود: " . $e->getMessage();
}

$conn->close();

$role = $_SESSION['role'] ?? null;

// Default URLs
$newContract = "#";
$contractRecords = "#";

// Role-based routing
switch ($role) {
    case 'employee':
    case 'finance':
    case 'manager':
        $newContract = "c-conTypes.php";
        $contractRecords = "c-adminRec.php";
        break;
}

function isSelected($value, $selected) {
    return $value === $selected ? "selected" : "";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>سجل العقود</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .r-container {
            max-width: 1140px;
            margin: auto;
            padding: 1rem;
        }
        .vacation-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f8f9fa;
        }
        .btn-det, .btn-prnt {
            display: inline-block;
            padding: 4px 10px;
            font-size: 0.9rem;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            margin: 2px;
        }
        .btn-det { background-color: #0d6efd; }
        .btn-prnt { background-color: #198754; }
        tr[data-href] {
            cursor: pointer;
        }
        tr[data-href]:hover {
            background-color: #f1f1f1;
        }
        form.filter-form {
            margin-bottom: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }
        form.filter-form input[type="text"] {
            max-width: 300px;
            flex-grow: 1;
        }
        form.filter-form select {
            width: 150px;
        }
        .btn-new-contract {
            margin-bottom: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
    </style>
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="r-container">
    <h2 class="mb-4">سجل العقود</h2>
    
    <!-- Add New Contract Button at the top -->
    <a class="buttons" href="<?= htmlspecialchars($newContract) ?>">+ إنشاء عقد جديد</a>
    <p></p>
    <!-- Filter/Search/Sort Form -->
    <form method="GET" class="filter-form" role="search" aria-label="فلترة العقود">
        <input
            type="text"
            name="search"
            placeholder="ابحث برقم العقد أو الطرف الأول أو الثاني أو اسم البرنامج أو رمز البرنامج"
            value="<?= htmlspecialchars($search) ?>"
            class="form-control"
            aria-label="بحث"
        />

        <select name="sort" class="form-select" aria-label="ترتيب">
            <option value="con_date_desc" <?= isSelected('con_date_desc', $sort) ?>>تاريخ العقد: الأحدث أولاً</option>
            <option value="con_date_asc" <?= isSelected('con_date_asc', $sort) ?>>تاريخ العقد: الأقدم أولاً</option>
            <option value="con_id_asc" <?= isSelected('con_id_asc', $sort) ?>>رقم العقد: تصاعديًا</option>
            <option value="con_id_desc" <?= isSelected('con_id_desc', $sort) ?>>رقم العقد: تنازليًا</option>
        </select>

        <button type="submit" class="btn btn-secondary">تطبيق</button>
    </form>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($contracts)): ?>
        <div class="alert alert-info">لا توجد عقود حاليًا</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="vacation-table">
                <thead>
                    <tr>
                        <th>رقم العقد</th>
                        <th>تاريخ العقد</th>
                        <th>اسم الطرف الأول</th>
                        <th>اسم الطرف الثاني</th>
                        <th>اسم البرنامج</th>
                        <th>رمز البرنامج</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts as $con): ?>
                    <tr data-href="c-adminForm1.php?con_id=<?= urlencode($con['con_id']) ?>&return_url=c-adminRec.php" onclick="window.location=this.dataset.href" style="cursor:pointer;">
                        <td><?= htmlspecialchars($con['con_id']) ?></td>
                        <td><?= htmlspecialchars($con['con_date']) ?></td>
                        <td><?= htmlspecialchars($con['1st_party']) ?></td>
                        <td><?= htmlspecialchars($con['2nd_party']) ?></td>
                        <td><?= htmlspecialchars($con['program_name']) ?></td>
                        <td><?= htmlspecialchars($con['program_id']) ?></td>
                        <td class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="c-contractDet1.php?con_id=<?= urlencode($con['con_id']) ?>" class="btn-det">تفاصيل</a>
                            <a href="c-pdf.php?con_id=<?= urlencode($con['con_id']) ?>" class="btn-prnt">PDF</a>
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
<!-- Font Awesome for the plus icon -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
