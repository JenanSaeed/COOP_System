<?php

require 'db_connect.php';

$showMessage="";
if($_SERVER[ 'REQUEST_METHOD']=== 'POST'){
  $user_id = trim($_POST['user_id']);
  $email= trim($_POST['email']);
  $showMessage= "سيتم ارسال رابط اعادة تعيين كلمة المرور للبريد الالكتروني";
  if (!empty($user_id) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Query to verify match
        $stmt = $pdo->prepare("SELECT id FROM employee WHERE emp_id = ? AND email = ?");
        $stmt->execute([$user_id, $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate secure token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiration
            $hashedToken = hash('sha256', $token);

            // Store reset info in password_resets table
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $hashedToken, $expires]);

            // Send reset email
            $resetLink = "https://yourdomain.com/reset-password.php?token=$token";
            $subject = "اعادة تعيين كلمة المرور";
            $message = "لاعادة تعيين كلمة المرور :\n\n$resetLink\n\nThis link will expire in 1 hour.";
            // mail($email, $subject, $message); // Uncomment this in production
        }
    }
}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>اعادة تعيين كلمة المرور</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
<?php
include 'header.php';
?>
<h2 class="form-title">اعادة تعيين كلمة المرور</h2>
<div class="reset-form">
<?php if (!empty($showMessage)) echo "<p>$showMessage</p>"; ?>
   <form method="POST" action="">
        <label class="loglabels" for="user_id">الرقم الوظيفي</label><br>
        <input class="loginputs" type="text" name="user_id" required><br><br>

        <label class="loglabels" for="email">البريد الالكتروني</label><br>
        <input class="loginputs" type="email" name="email" required><br><br>

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Add this inside your form before the submit button -->
<div class="g-recaptcha" data-sitekey="6LcBJX0rAAAAAPlXvSpxDtB8icdEpDQX3FkqKjh-"></div>

        <input class= "reset" type="submit" value="اعادة تعيين كلمة المرور">
    </form>
</div>



<?php
include 'footer.php';
?>  
</body>
</html>
