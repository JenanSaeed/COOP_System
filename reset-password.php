<?php
require 'db_connect.php';

$token = $_GET['token'] ?? '';
$showMessage = "";
$tokenValid = false;

// If form submitted (POST) — process new password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['password'];
    $hashedToken = hash('sha256', $token);

    // Check if token exists and not expired
    $stmt = $pdo->prepare("SELECT user_id, expires_at FROM password_resets WHERE token_hash = ?");
    $stmt->execute([$hashedToken]);
    $reset = $stmt->fetch();

    if ($reset && strtotime($reset['expires_at']) > time()) {
        // Update password
        $hashedPassword = hash('sha256',$newPassword);
        $stmt = $pdo->prepare("UPDATE employee SET password = ? WHERE emp_id = ?");
        $stmt->execute([$hashedPassword, $reset['user_id']]);

        // Delete the token
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token_hash = ?");
        $stmt->execute([$hashedToken]);

        $showMessage = "تم تغيير كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.";
    } else {
        $showMessage = "الرابط غير صالح أو انتهت صلاحيته.";
    }
} else {
    // If GET request, validate token before showing the form
    if (!empty($token)) {
        $hashedToken = hash('sha256', $token);
        $stmt = $pdo->prepare("SELECT expires_at FROM password_resets WHERE token_hash = ?");
        $stmt->execute([$hashedToken]);
        $reset = $stmt->fetch();

        if ($reset && strtotime($reset['expires_at']) > time()) {
            $tokenValid = true;
        } else {
            $showMessage = "الرابط غير صالح أو انتهت صلاحيته.";
        }
    } else {
        $showMessage = "الرابط غير صالح.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إعادة تعيين كلمة المرور</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>

<h2 class="form-title">إعادة تعيين كلمة المرور</h2>

<div class="reset-form">
  <?php if (!empty($showMessage)) echo "<p class='message'>$showMessage</p>"; ?>

  <?php if ($tokenValid): ?>
  <form method="POST" action="">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

    <label class="loglabels" for="password">كلمة المرور الجديدة</label>
    <input class="loginputs" type="password" name="password" required minlength="6">

    <input class="reset" type="submit" value="تحديث كلمة المرور">
  </form>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
