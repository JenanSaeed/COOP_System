<?php
require 'db_connect.php';

$showMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id']);
    $email = trim($_POST['email']);
    $recaptchaSecret = '6LcBJX0rAAAAAM4nvsH15keAEBNPLt2qBS1LL9uR'; // ← Replace with your actual reCAPTCHA secret key
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
    $responseData = json_decode($verify);

    if (!$responseData->success) {
        $showMessage = "فشل التحقق من reCAPTCHA، الرجاء المحاولة مرة أخرى.";
    } elseif (!empty($user_id) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("SELECT emp_id FROM employee WHERE emp_id = ? AND email = ?");
        $stmt->execute([$user_id, $email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);
            $expires = date('Y-m-d H:i:s', time() + 3600); // Token valid for 1 hour

            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $hashedToken, $expires]);

            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/COOP_System/reset-password.php?token=$token";
            $subject = "اعادة تعيين كلمة المرور";
            $message = "لإعادة تعيين كلمة المرور، اضغط على الرابط التالي:\n\n$resetLink\n\nالرابط صالح لمدة ساعة واحدة فقط.";

            // mail($email, $subject, $message); // Uncomment in production
            $showMessage = "تم إنشاء الرابط بنجاح. استخدم الرابط التالي لإعادة تعيين كلمة المرور:<br><a href='$resetLink'>$resetLink</a>";
        } else {
            $showMessage = "المستخدم غير موجود أو البيانات غير صحيحة.";
        }
    } else {
        $showMessage = "يرجى إدخال رقم وظيفي وبريد إلكتروني صحيح.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>اعادة تعيين كلمة المرور</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<?php include 'header.php'; ?>

<h2 class="form-title">اعادة تعيين كلمة المرور</h2>

<div class="reset-form">
  
  <form method="POST" action="forget-pass.php">
    <label class="loglabels" for="user_id">الرقم الوظيفي</label>
    <input class="loginputs" type="text" name="user_id" required>

    <label class="loglabels" for="email">البريد الالكتروني</label>
    <input class="loginputs" type="email" name="email" required>

    <div class="g-recaptcha" data-sitekey="6LcBJX0rAAAAAPlXvSpxDtB8icdEpDQX3FkqKjh-"></div><br>

    <input class="reset" type="submit" value="اعادة تعيين كلمة المرور">
    <?php if (!empty($showMessage)) echo "<p class='message'>$showMessage</p>"; ?>

  </form>
</div>

<?php include 'footer.php'; ?>

</body>

</html>
