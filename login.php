<?php 
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = trim($_POST['id']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM sign WHERE emp_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === trim($row['password'])) {
            $_SESSION['emp_id'] = $row['emp_id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = $row['role'];

            // ✅ إذا فيه صفحة محفوظة للرجوع لها، نوجه لها
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect_to = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']); // نحذفها عشان ما تبقى
                header("Location: $redirect_to");
                exit();
            }

            // توجيه حسب الدور إذا ما فيه redirect مخصص
            if ($row['role'] === 'employee') {
                header("Location: empReqs.php");
                exit();
            } elseif ($row['role'] === 'finance') {
                header("Location: finMain.php");
                exit();
            } elseif ($row['role'] === 'manager') {
                header("Location: manMain.php");
                exit();
            } else {
                header("Location: login.php?error=" . urlencode("دور المستخدم غير معروف"));
                exit();
            }
        } else {
            $error = "كلمة المرور غير صحيحة!";
            header("Location: login.php?error=" . urlencode($error));
            exit();
        }
    } else {
        $error = "رقم الموظف غير موجود!";
        header("Location: login.php?error=" . urlencode($error));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php
include 'header.php';
?>
<h2 class="form-title">تسجيل الدخول</h2>

<div class="container">
<?php
if (isset($_GET['error_message'])) {
    echo "<div style='color: red; font-weight: bold; text-align: center; margin-bottom: 15px;'>" 
        .htmlspecialchars($_GET['error_message']) . 
        "</div>";
}
?>


<form class="logform" action="check_login.php" method="post">
    <label class="loglabels" for="emp_id">اسم المستخدم:</label>
    <input class="loginputs" type="text" id="emp_id" name="emp_id" placeholder="اسم المستخدم" required>

    <label class="loglabels" for="password">كلمة المرور:</label>
    <input class="loginputs" type="password" id="password" name="password" placeholder="كلمة المرور" required>

    <input class="buttons" type="submit" value="تسجيل الدخول">
  </form>
</div>

<?php
include 'footer.php';
?>

</body>
</html>
