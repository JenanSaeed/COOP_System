<?php
session_start();
require_once("db_connect.php");

// التحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
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

<?php
  $role = $_SESSION['role'] ?? null;

  // Default URLs
  $newContract = "#";
  $contractRecords = "#";

  // Role-based routing
  switch ($role) {
      case 'employee':
          $newContract = "c-conTypes.php";
          $contractRecords = "c-adminRec.php";
          break;
      case 'finance':
          $newContract = "c-conTypes.php";
          $contractRecords = "c-adminRec.php";
          break;

      case 'manager':
          $newContract = "c-conTypes.php";
          $contractRecords = "c-adminRec.php";
          break;

      default:
          // fallback in case of invalid role
          $newContract = "#";
          $contractRecords = "#";
  }
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سجل العقود</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="r-container">
    <h2 class="mb-4">سجل العقود</h2>

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
                <tr onclick="window.location.href='c-adminForm1.php?con_id=<?= $con['con_id'] ?>&return_url=c-adminRec.php'" style="cursor:pointer;">
                    <td><?= $con['con_id'] ?></td>
                    <td><?= htmlspecialchars($con['con_date']) ?></td>
                    <td><?= htmlspecialchars($con['1st_party']) ?></td>
                    <td><?= htmlspecialchars($con['2nd_party']) ?></td>
                    <td><?= htmlspecialchars($con['program_name']) ?></td>
                    <td><?= htmlspecialchars($con['program_id']) ?></td>
                    <td class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="c-adminForm.php?con_id=<?= $con['con_id'] ?>" class="btn-det">تفاصيل</a>
                        <a href="c-pdf.php?con_id=<?= $con['con_id'] ?>" class="btn-prnt">PDF</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
                </div>
    <?php endif; ?>
    <div class="text-center mt-4">
  <a href="<?= $newContract ?>" class="btn btn-sm btn-success px-4">
    إنشاء عقد جديد
  </a>
</div>

</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>