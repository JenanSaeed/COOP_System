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
  <title>Login</title>
  <link rel='stylesheet' href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  </head>
  
<body>
<?php
  include 'header.php';
  ?>

  <h2 class="form-title">تسجيل الدخول</h2>

<div class="container">
<?php
if (isset($_GET['error'])) {
    echo "
    <div style='
        background-color: #ffe0e0;
        color: #b00020;
        padding: 12px;
        border: 1px solid #b00020;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
    '>
        " . htmlspecialchars($_GET['error']) . "
    </div>";
}
?>


  <form class="logform" action="check_login.php" method="post">
    <label class="loglabels" for="emp_id">اسم المستخدم:</label>
    <input class="loginputs" type="text" id="emp_id" name="id" placeholder="اسم المستخدم" required>

    <label class="loglabels" for="emp_pass">كلمة المرور:</label>
    <input class="loginputs" type="password" id="emp_pass" name="password" placeholder="كلمة المرور" required>

    <input class="buttons" type="submit" value="تسجيل الدخول">
  </form>
  </div>

  <?php
  include 'footer.php';
  ?>

</body>
</html>