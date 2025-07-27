<?php
session_start();
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = trim($_POST['emp_id']);
    $password = trim($_POST['password']);

    if (empty($emp_id) || empty($password)) {
        header('Location: login.php?error=' . urlencode("الرجاء إدخال اسم المستخدم وكلمة المرور."));
        exit();
    }

    // التحقق من الموظف
    $stmt = $conn->prepare("SELECT * FROM employee WHERE emp_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $employeeResult = $stmt->get_result();

    if ($employeeResult->num_rows === 1) {
        $employee = $employeeResult->fetch_assoc();

        if ($password === trim($employee['password'])) {
            $_SESSION['emp_id'] = $employee['emp_id'];
            $_SESSION['name'] = $employee['name'];
            $_SESSION['role'] = $employee['role'];
            $_SESSION['logged_in'] = true;

            // دايمًا يروح للصفحة المحفوظة، وإذا ما فيه صفحة محفوظة يروح لـ c-main.php
            $redirect = $_SESSION['redirect_to'] ?? 'index.php';
            unset($_SESSION['redirect_to']);
            header("Location: $redirect");
            exit();
        } else {
            header("Location: login.php?error=" . urlencode("كلمة المرور غير صحيحة."));
            exit();
        }
    }

    // لو ما لقى موظف
    header("Location: login.php?error=" . urlencode("المستخدم غير موجود."));
    exit();
} else {
    header("Location: login.php");
    exit();
}
