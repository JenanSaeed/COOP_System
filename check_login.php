<?php
session_start();
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = trim($_POST['emp_id']);
    $password = trim($_POST['password']);

    // التحقق من الحقول
    if (empty($emp_id) || empty($password)) {
        header('Location: login.php?error=' . urlencode("الرجاء إدخال رقم الموظف وكلمة المرور."));
        exit();
    }

    // استخدم prepared statement لحماية من SQL Injection
    // جلب بيانات الموظف مع الدور من جدول sign
    $stmt = $conn->prepare("SELECT * FROM employee WHERE emp_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // تحقق من كلمة المرور
        if ($password === trim($row['password'])) {
            $_SESSION['emp_id'] = $row['emp_id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];

            // توجيه حسب الدور
            switch ($row['role']) {
                case 'employee':
                    header("Location: empReqs.php");
                    break;
                case 'finance':
                    header("Location: finMain.php");
                    break;
                case 'manager':
                    header("Location: manMain.php");
                    break;
                default:
                    header("Location: login.php?error=" . urlencode("دور المستخدم غير معروف."));
                    break;
            }
            exit();
        } else {
            header("Location: login.php?error=" . urlencode("كلمة المرور غير صحيحة."));
            exit();
        }
    } else {
        header("Location: login.php?error=" . urlencode("رقم الموظف غير موجود."));
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
