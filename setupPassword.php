<?php
session_start();
require_once("db_connect.php");

$message = "";

// تحقق من وجود التوكن
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $message = "<div class='alert alert-danger'>رابط غير صالح.</div>";
} else {
    $token = $_GET['token'];

    // تحقق من وجود الموظف بهذا التوكن وصلاحية الرابط
    $stmt = $conn->prepare("SELECT emp_id, name, setup_expiry FROM employee WHERE setup_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $emp_id = $row['emp_id'];
        $name = $row['name'];
        $expiry = strtotime($row['setup_expiry']);

        if (time() > $expiry) {
            $message = "<div class='alert alert-danger'>انتهت صلاحية الرابط. يرجى التواصل مع الإدارة.</div>";
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);

            if (empty($new_password) || empty($confirm_password)) {
                $message = "<div class='alert alert-danger'>يرجى إدخال كلمة المرور وتأكيدها.</div>";
            } elseif ($new_password !== $confirm_password) {
                $message = "<div class='alert alert-danger'>كلمتا المرور غير متطابقتين.</div>";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // تحديث كلمة المرور ومسح التوكن
                $update = $conn->prepare("UPDATE employee SET password = ?, setup_token = NULL, setup_expiry = NULL WHERE emp_id = ?");
                $update->bind_param("ss", $hashed_password, $emp_id);
                if ($update->execute()) {
                    $message = "<div class='alert alert-success'>تم تعيين كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>حدث خطأ أثناء تعيين كلمة المرور.</div>";
                }
            }
        }
    } else {
        $message = "<div class='alert alert-danger'>رابط غير صالح أو غير موجود.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>إعداد كلمة المرور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">إعداد كلمة المرور</h2>
    <?php echo $message; ?>

    <?php if (isset($emp_id) && time() <= $expiry && !isset($_POST['new_password'])): ?>
        <form method="POST">
            <div class="mb-3">
                <label for="new_password" class="form-label">كلمة المرور الجديدة</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary">حفظ</button>
        </form>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
