<?php
session_start();
include "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $guest_id_input = (int)trim($_POST['guest_id'] ?? 0);
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($guest_id_input <= 0) {
            throw new Exception("يرجى إدخال رقم مستخدم صالح.");
        }
        if (empty($full_name)){
            throw new Exception("يرجى كتابة الاسم.");
        }
        if (empty($email)){
            throw new Exception("يرجى كتابة الايميل.");
        }
        if(empty($password)){
            throw new Exception("يرجى كتابة كلمة المرور.");
        }
        if(empty($confirm_password)){
            throw new Exception("يرجى تأكيد كلمة المرور.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("البريد الإلكتروني غير صالح.");
        }

        if ($password !== $confirm_password) {
            throw new Exception("كلمتا المرور غير متطابقتين.");
        }

        // تحقق من تكرار guest_id
        $stmt_check = $conn->prepare("SELECT guest_id FROM guest WHERE guest_id = ?");
        $stmt_check->bind_param("i", $guest_id_input);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            throw new Exception("رقم المستخدم مستخدم من قبل، يرجى اختيار رقم آخر.");
        }

        // تشفير كلمة المرور
        //$hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // إدخال البيانات
        $stmt = $conn->prepare("INSERT INTO guest (guest_id, guest_password, guest_name, guest_email) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("خطأ في قاعدة البيانات: " . $conn->error);
        }

        $stmt->bind_param("isss", $guest_id_input, $hashed_password, $full_name, $email);

        if ($stmt->execute()) {
            $success = "تم التسجيل بنجاح!";
            header("Refresh:1; url=login.php");
            exit();
        } else {
            throw new Exception("فشل في التسجيل: " . $stmt->error);
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>تسجيل مستخدم جديد</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    </head>
    <body class="bg-light">
        <?php include 'header.php'; ?>

        <h2 class="form-title">تسجيل كـ ضيف</h2>
        <div class="container">
            <form class="log-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return checkPasswords();">
                <label class="loglabels">الاسم الكامل:</label>
                <input class="loginputs" type="text" name="full_name" placeholder="الاسم الكامل" required>

                <label class="loglabels">اسم المستخدم:</label>
                <input class="loginputs" type="text" name="guest_id" placeholder="اسم المستخدم" required>

                <label class="loglabels">البريد الإلكتروني:</label>
                <input class="loginputs" type="email" name="email" placeholder="example@gmail.com" required>

                <label class="loglabels">كلمة المرور:</label>
                <input class="loginputs" type="password" id="password" name="password" placeholder="كلمة المرور" required>

                <label class="loglabels">تأكيد كلمة المرور:</label>
                <input class="loginputs" type="password" id="confirm_password" name="confirm_password" placeholder="اعد كتابة كلمة المرور" required>

                <div class="form-buttons">
                    <button class="buttons" type="submit">تسجيل</button>
                </div>          
            </form>
        </div> 

    <script>
        function checkPasswords() {
            var password = document.getElementById("password").value;
            var confirm = document.getElementById("confirm_password").value;
            if (password !== confirm) {
                alert("كلمتا المرور غير متطابقتين!");
                return false;
            }
            return true;
        }
    </script>

    <?php include 'footer.php'; ?>
    </body>
</html>