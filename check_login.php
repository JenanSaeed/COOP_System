<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = trim($_POST['id']);
    $password = trim($_POST['password']);

    // ✅ تحقق إذا الحقول فاضية
    if (empty($emp_id) || empty($password)) {
        $error = "الرجاء إدخال رقم الموظف وكلمة المرور";
        header("Location: login.php?error=" . urlencode($error));
        exit();
    }

    // جلب بيانات الموظف مع الدور من جدول sign
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

            $role = $row['role']; // لازم نضيفه

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
    } else {
        $error = "رقم الموظف غير موجود!";
        header("Location: login.php?error=" . urlencode($error));
        exit();
    }
}
?>
