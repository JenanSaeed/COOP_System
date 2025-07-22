<?php
include 'db_connect.php';
session_start();

$contract_type = $_GET['type'] ?? '';

$con_terms = '';
$extra_terms = [];

// التعامل مع الإضافة والحذف (نفس الصفحة)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_role === 'admin') {
    if ($_POST['action'] === 'add_extra') {
        $new_term = trim($_POST['new_term']);
        if (!empty($new_term)) {
            $stmt = $conn->prepare("SELECT extra_terms FROM terms WHERE con_type = ?");
            $stmt->bind_param("s", $contract_type);
            $stmt->execute();
            $result = $stmt->get_result();
            $current = '';
            if ($row = $result->fetch_assoc()) {
                $current = $row['extra_terms'];
            }
            $stmt->close();

            $updated = $current . ($current ? "\n" : "") . $new_term;
            $stmt = $conn->prepare("UPDATE terms SET extra_terms = ? WHERE con_type = ?");
            $stmt->bind_param("ss", $updated, $contract_type);
            $stmt->execute();
            $stmt->close();
        }
    }

    if ($_POST['action'] === 'delete_extra' && isset($_POST['index'])) {
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
    }

    header("Location: c-terms.php?type=" . urlencode($contract_type));
    exit;
}

// جلب البنود
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<?php include 'header.php'; ?>
<div class="container my-5">
    <h2 class="mb-4">بنود العقد - <?= htmlspecialchars($contract_type) ?></h2>

    <!-- البنود الأساسية -->
    <?php if (!empty($con_terms)): ?>
        <div class="term mb-4"><?= nl2br(htmlspecialchars($con_terms)) ?></div>
    <?php endif; ?>

    <!-- البنود الإضافية -->
    <?php if (!empty($extra_terms)): ?>
        <?php foreach ($extra_terms as $index => $term): ?>
            <?php $term = trim($term); if ($term === '') continue; ?>
            <div class="term border p-3 mb-2">
                <?= htmlspecialchars($term) ?>
                <?php if ($user_role === 'admin'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_extra">
                        <input type="hidden" name="index" value="<?= $index ?>">
                        <button type="submit" class="btn btn-sm btn-danger float-end" title="حذف البند">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>
            <form method="POST" class="mt-4">
                <input type="hidden" name="action" value="add_extra">
                <div class="mb-3">
                    <label for="new_term" class="form-label">إضافة بند جديد:</label>
                    <textarea name="new_term" id="new_term" rows="3" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">إضافة</button>
            </form>
</div>
</body>
</html>
