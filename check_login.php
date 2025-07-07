<?php
session_start();
include "db_con.php"; // تأكدي أن الاتصال اسمه $conn

// تحقق من وجود البيانات
if (isset($_POST['emp_id']) && isset($_POST['password'])) {
    $emp_id = trim($_POST['emp_id']);
    $password = trim($_POST['password']);

    // تحقق من الاتصال بقاعدة البيانات
    if (!$conn) {
        header('Location: login.php?error_message=' . urlencode("تعذر الاتصال بالخادم."));
        exit;
    }

<<<<<<< HEAD
    // ⚠️ استعلام التحقق باستخدام emp_id و password
    $sql = "SELECT * FROM users WHERE emp_id='$emp_id' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['emp_id'] = $row['emp_id']; // حفظ رقم الموظف في السيشن
        header("Location: traveldestination.php");
        exit();
=======
    // جلب بيانات الموظف مع الدور من جدول sign
    $stmt = $conn->prepare("SELECT * FROM employee WHERE emp_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === trim($row['password'])) {
            $_SESSION['emp_id'] = $row['emp_id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = $row['role'];

            $role = $row['role'];

            // بناء على الدور نوجه المستخدم
            if ($role === 'employee') {
                header("Location: empReqs.php");
                exit();
            } elseif ($role === 'finance') {
                header("Location: finMain.php");
                exit();
            } elseif ($role === 'manager') {
                header("Location: manMain.php");
                exit();
            } else {
                $error = "دور المستخدم غير معروف";
                header("Location: login.php?error=" . urlencode($error));
                exit();
            }

        } else {
            $error = "كلمة المرور غير صحيحة!";
            header("Location: login.php?error=" . urlencode($error));
            exit();
        }
>>>>>>> f3cb3ca76cae25356f9b8cabd450a7fc6ac1e481
    } else {
        // رقم الموظف أو كلمة المرور غير صحيحة
        header('Location: login.php?error_message=' . urlencode("رقم الموظف أو كلمة المرور غير صحيحة."));
        exit();
    }
} else {
    // حقل أو أكثر ناقص
    header('Location: login.php?error_message=' . urlencode("الرجاء إدخال رقم الموظف وكلمة المرور."));
    exit();
}
?>
