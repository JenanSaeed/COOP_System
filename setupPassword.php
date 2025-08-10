<?php
session_start();
require_once("db_connect.php");

$message = "";
$emp_id = "";

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    $stmt = $conn->prepare("SELECT emp_id, setup_expiry FROM employee WHERE setup_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $emp_id = $row['emp_id'];
        $expiry = strtotime($row['setup_expiry']);

        if (time() > $expiry) {
            $message = "<div class='alert alert-danger'>انتهت صلاحية الرابط. الرجاء طلب رابط جديد.</div>";
        } else {
            $message = "<div class='alert alert-success'>رابط صحيح، يمكنك الآن تعيين كلمة المرور.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>رابط غير صالح أو غير موجود.</div>";
    }
} else {
    $message = "<div class='alert alert-danger'>الرابط غير مكتمل.</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($emp_id)) {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm)) {
        $message = "<div class='alert alert-danger'>يرجى إدخال كلمة المرور وتأكيدها.</div>";
    } elseif ($password !== $confirm) {
        $message = "<div class='alert alert-danger'>كلمتا المرور غير متطابقتين.</div>";
    } else {
        // حفظ كلمة المرور بدون تشفير (مباشرة)
        $hashed_password = $password;

        $update_stmt = $conn->prepare("UPDATE employee SET password = ?, setup_token = '', setup_expiry = NULL WHERE emp_id = ?");
        $update_stmt->bind_param("ss", $hashed_password, $emp_id);
        if ($update_stmt->execute()) {
            $message = "<div class='alert alert-success'>تم تعيين كلمة المرور بنجاح. يمكنك الآن <a href='login.php'>تسجيل الدخول</a>.</div>";
            $emp_id = "";  // منع إعادة التعيين بعد النجاح
        } else {
            $message = "<div class='alert alert-danger'>حدث خطأ أثناء تعيين كلمة المرور.</div>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعيين كلمة المرور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: auto; padding: 2rem; }
    </style>
</head>
<body class="bg-light">
<div class="container">
    <h2 class="mb-4 text-center">إعداد كلمة المرور</h2>
    <?= $message ?>

    <?php if (!empty($emp_id) && strpos($message, 'success') !== false): ?>
        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label">كلمة المرور الجديدة</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">تأكيد كلمة المرور</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">حفظ كلمة المرور</button>
        </form>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
