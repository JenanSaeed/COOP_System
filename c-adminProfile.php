<?php
session_start();
require_once('db_connect.php');

// التأكد من أن المستخدم مسجل دخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$emp_id = $_SESSION['emp_id'];
$message = "";

// عند الإرسال، يتم التحديث
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // تحديث البيانات عدا الاسم
    $stmt = $conn->prepare("UPDATE employee SET email = ?, address = ?, phone = ?, password = ? WHERE emp_id = ?");
    $stmt->bind_param("ssssi", $email, $address, $phone, $password, $emp_id);

    if ($stmt->execute()) {
        $message = "تم تحديث المعلومات بنجاح.";
    } else {
        $message = "حدث خطأ أثناء التحديث: " . $conn->error;
    }
}

// جلب معلومات المستخدم
$stmt = $conn->prepare("SELECT name, email, address, phone, password FROM employee WHERE emp_id = ?");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الملف الشخصي</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

</head>
<body class="bg-light">
<?php include 'header.php'; ?>

<div class="r-container">
    <h2 class="mb-4">الملف الشخصي</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">الاسم</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">العنوان</label>
            <textarea name="address" class="form-control" required><?= htmlspecialchars($user['address']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">رقم الجوال</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>
        <div class="mb-3">
    <label class="form-label">كلمة المرور</label>
    <div class="input-group">
        <input type="password" name="password" id="passwordField" class="form-control" value="<?= htmlspecialchars($user['password']) ?>" required>
        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()" tabindex="-1">
            <i class="fa-solid fa-eye" id="eyeIcon"></i>
        </button>
    </div>
</div>


        <button type="submit" class="btn-prnt">حفظ التغييرات</button>
    </form>
</div>

<?php include 'footer.php'; ?>
<script> //for invisible password 
function togglePassword() {
    const field = document.getElementById("passwordField");
    const icon = document.getElementById("eyeIcon");
    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>
