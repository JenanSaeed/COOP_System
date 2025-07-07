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

    // ⚠️ استعلام التحقق باستخدام emp_id و password
    $sql = "SELECT * FROM users WHERE emp_id='$emp_id' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['emp_id'] = $row['emp_id']; // حفظ رقم الموظف في السيشن
        header("Location: traveldestination.php");
        exit();
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
