<?php
session_start();
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = trim($_POST['emp_id']);
    $password = trim($_POST['password']);

    if (empty($emp_id) || empty($password)) {
        header('Location: login.php?error=' . urlencode("الرجاء إدخال رقم المستخدم وكلمة المرور."));
        exit();
    }

    // First: Check employee table
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
            $_SESSION['name'] = $row['name'];

            if (isset($_SESSION['redirect_to']) &&
                $_SESSION['redirect_to'] !== 'login.php' &&
                $_SESSION['redirect_to'] !== basename(__FILE__)) {
                $redirectPage = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
                header("Location: $redirectPage");
                exit();
            } else {
                header("Location: c-adminMain.php");
                exit();
            }
        } else {
            header("Location: login.php?error=" . urlencode("كلمة المرور غير صحيحة."));
            exit();
        }
    }

    // Second: Check guest table
    $stmt = $conn->prepare("SELECT * FROM guest WHERE guest_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === trim($row['guest_password'])) {
            $_SESSION['guest_id'] = $row['guest_id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = 'guest';
            $_SESSION['name'] = $row['guest_name'];

            header("Location: c-main.php");
            exit();
        } else {
            header("Location: login.php?error=" . urlencode("كلمة المرور غير صحيحة."));
            exit();
        }
    }

    header("Location: login.php?error=" . urlencode("المستخدم غير موجود."));
    exit();

} else {
    header("Location: login.php");
    exit();
}
