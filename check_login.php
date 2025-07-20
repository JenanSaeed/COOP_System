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

    // 1. Check if user is an employee
    $stmt = $conn->prepare("SELECT * FROM employee WHERE emp_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $employeeResult = $stmt->get_result();

    if ($employeeResult->num_rows === 1) {
        $employee = $employeeResult->fetch_assoc();

        if ($password === trim($employee['password'])) {
            $_SESSION['emp_id'] = $employee['emp_id'];
            $_SESSION['name'] = $employee['name'];
            $_SESSION['role'] = $employee['role']; // e.g., employee, manager, finance
            $_SESSION['logged_in'] = true;

            $redirect = $_SESSION['redirect_to'] ?? 'c-adminMain.php';
            unset($_SESSION['redirect_to']);
            header("Location: $redirect");
            exit();
        } else {
            header("Location: login.php?error=" . urlencode("كلمة المرور غير صحيحة."));
            exit();
        }
    }

    // 2. Check if user is a guest
    $stmt = $conn->prepare("SELECT * FROM guest WHERE guest_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $guestResult = $stmt->get_result();

    if ($guestResult->num_rows === 1) {
        $guest = $guestResult->fetch_assoc();

        if ($password === trim($guest['guest_password'])) {
            $_SESSION['guest_id'] = $guest['guest_id'];
            $_SESSION['name'] = $guest['guest_name'];
            $_SESSION['role'] = 'guest';
            $_SESSION['logged_in'] = true;

            header("Location: c-main.php");
            exit();
        } else {
            header("Location: login.php?error=" . urlencode("كلمة المرور غير صحيحة."));
            exit();
        }
    }

    // 3. If neither employee nor guest found
    header("Location: login.php?error=" . urlencode("المستخدم غير موجود."));
    exit();
} else {
    header("Location: login.php");
    exit();
}
