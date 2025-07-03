<?php
session_start();
include 'db_connect.php'; // الاتصال بقاعدة البيانات coop_db

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = $_POST['id']; // اسم المستخدم في الفورم
    $password = $_POST['password']; // كلمة المرور

    // التحقق باستخدام prepared statements لمنع SQL Injection
    $stmt = $conn->prepare("SELECT * FROM sign WHERE emp_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === $row['password']) {
            $_SESSION['emp_id'] = $row['emp_id'];
            $_SESSION['logged_in'] = true;

            // احصل على أول صفين من جدول sign لمعرفة ترتيب المستخدم
            $order = $conn->query("SELECT emp_id FROM sign ORDER BY id ASC LIMIT 2");
            $topRows = $order->fetch_all(MYSQLI_ASSOC);

            if ($row['emp_id'] === $topRows[0]['emp_id']) {
                header("Location: empReqs.php");
                exit();
            } elseif ($row['emp_id'] === $topRows[1]['emp_id']) {
                header("Location: finMain.php");
                exit();
            } else {
                header("Location: validation.php");
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
