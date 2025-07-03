<?php
session_start();
include 'db_connect.php'; // الاتصال بقاعدة البيانات coop_db

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = trim($_POST['id']); // اسم المستخدم في الفورم
    $password = trim($_POST['password']); // كلمة المرور

    // التحقق باستخدام prepared statements لمنع SQL Injection
    $stmt = $conn->prepare("SELECT * FROM sign WHERE emp_id = ?");
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === trim($row['password'])) {
            $_SESSION['emp_id'] = $row['emp_id'];
            $_SESSION['logged_in'] = true;

            // جلب أول صفين بناءً على emp_id كرقم (مرتب تصاعدي)
            $order = $conn->query("SELECT emp_id FROM sign ORDER BY emp_id ASC LIMIT 2");

            if (!$order) {
                die("خطأ في الاستعلام: " . $conn->error);
            }

            $topRows = $order->fetch_all(MYSQLI_ASSOC);

            $loggedEmp = trim((string)$row['emp_id']);
            $firstEmp = trim((string)$topRows[0]['emp_id'] ?? '');
            $secondEmp = trim((string)$topRows[1]['emp_id'] ?? '');

            if ($loggedEmp == $firstEmp) {
                header("Location: empReqs.php");
                exit();
            } elseif ($loggedEmp == $secondEmp) {
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
