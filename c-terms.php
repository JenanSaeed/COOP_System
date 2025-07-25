<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

include 'db_connect.php';
session_start();
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']); // نضمن عرضها مرة واحدة فقط


$term_added = isset($_GET['added']) && $_GET['added'] == 1;

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$contract_type = $_GET['type'] ?? $_SESSION['contract_type'] ?? '';
$_SESSION['contract_type'] = $contract_type;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_extra') {
        $new_term = trim($_POST['new_term'] ?? '');

        if (!empty($new_term) && !empty($contract_type)) {
            $stmt = $conn->prepare("SELECT extra_terms FROM terms WHERE con_type = ?");
            $stmt->bind_param("s", $contract_type);
            $stmt->execute();
            $result = $stmt->get_result();

            $current_terms = '';
            if ($row = $result->fetch_assoc()) {
                $current_terms = $row['extra_terms'] ?? '';
            }
            $stmt->close();

            $updated_terms = $current_terms . ($current_terms ? "\n" : "") . $new_term;

            $stmt = $conn->prepare("UPDATE terms SET extra_terms = ? WHERE con_type = ?");
            $stmt->bind_param("ss", $updated_terms, $contract_type);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "تم إضافة البند بنجاح!";
                header("Location: c-terms.php?type=" . urlencode($contract_type));
                exit();
            } else {
                echo "<script>alert('❌ فشل في إضافة البند');</script>";
            }
            $stmt->close();
        }
    }
//delete term
    if ($action === 'delete_extra' && isset($_POST['index'])) {
        $index = (int) $_POST['index'];
        $stmt = $conn->prepare("SELECT extra_terms FROM terms WHERE con_type = ?");
        $stmt->bind_param("s", $contract_type);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $lines = explode("\n", $row['extra_terms']);
            if (isset($lines[$index])) {
                unset($lines[$index]);
                $updated = implode("\n", $lines);
                $stmt2 = $conn->prepare("UPDATE terms SET extra_terms = ? WHERE con_type = ?");
                $stmt2->bind_param("ss", $updated, $contract_type);
                $stmt2->execute();
                $stmt2->close();
            }
        }
        $stmt->close();
        header("Location: c-terms.php?type=" . urlencode($contract_type));
        exit();
    }
}

// جلب البنود (بعد الإضافة أو الحذف)
$con_terms = '';
$extra_terms = [];

if (!empty($contract_type)) {
    $stmt = $conn->prepare("SELECT con_terms, extra_terms FROM terms WHERE con_type = ?");
    $stmt->bind_param("s", $contract_type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $con_terms = $row['con_terms'];
        $extra_raw = $row['extra_terms'] ?? '';
        $extra_terms = explode("\n", $extra_raw);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>بنود العقد - <?= htmlspecialchars($contract_type) ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
<?php include 'header.php'; ?>
<div class="t-container">
    <h3 class="mb-4">بنود العقد - <?= htmlspecialchars($contract_type) ?></h3>

    <!-- البنود الأساسية -->
    <?php if (!empty($con_terms)): ?>
    <div class="terms-box">
        <h3>البنود الأساسية:</h3>
        <ul>
        <?php
        $lines = explode("\n", $con_terms);
        foreach ($lines as $line) {
            echo "<li>" . htmlspecialchars($line) . "</li>";
        }
        ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- البنود الإضافية -->
    <?php if (!empty($extra_terms)): ?>
    <div class="terms-box mt-4">
        <h3>البنود الإضافية:</h3>
        <ul class="list-group">
            <?php foreach ($extra_terms as $index => $term): ?>
                <?php $term = trim($term); if ($term === '') continue; ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($term) ?>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="action" value="delete_extra">
                            <input type="hidden" name="index" value="<?= $index ?>">
                            <button type="submit" class="delete-button" title="حذف البند">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>


    <hr>

    <!-- زر إضافة بند -->
    <div class="text-center mt-4">
        <button id="showAddForm" class="buttons">
            <i class="fas fa-plus"></i> إضافة بند جديد
        </button>
    </div>

    <!-- نموذج الإضافة -->
    <div id="addTermForm" class="mt-3" style="display:none;">
        <form method="POST">
            <input type="hidden" name="action" value="add_extra">
            <input type="text" name="new_term" class="form-control mb-2" required placeholder="أدخل البند الجديد هنا">
            <div class="t-buttons-form">
                <button type="submit" class="t-buttons">حفظ</button>
                <button type="button" class="t-buttons" id="cancelAdd">إلغاء</button>
            </div>
        </form>
    </div>

    <!-- رسالة النجاح -->
    <?php if (!empty($success_message)): ?>
    <div class="alert alert-success">
        <?= $success_message ?>
    </div>
    <?php endif; ?>
    

    <!-- زر المتابعة دائم -->
    <div class="text-center mt-4">
        <a href="c-contractDet1.php" class="btn btn-outline-dark">متابعة</a>
    </div>
</div>

<script>
    document.getElementById('showAddForm')?.addEventListener('click', function() {
        document.getElementById('addTermForm').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancelAdd')?.addEventListener('click', function() {
        document.getElementById('addTermForm').style.display = 'none';
        document.getElementById('showAddForm').style.display = 'inline-block';
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>
