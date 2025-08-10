<?php
session_start();
require_once("db_connect.php");

// PHPMailer includes
require_once(__DIR__ . "/PHPMailer/src/PHPMailer.php");
require_once(__DIR__ . "/PHPMailer/src/SMTP.php");
require_once(__DIR__ . "/PHPMailer/src/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure logged-in manager
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$emp_message = "";
$con_message = "";
$emp_id = $name = $role = $email = "";
$con_type = $con_terms = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_employee'])) {
        $emp_id = trim($_POST['emp_id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $displayName = htmlspecialchars($name ?: 'غير معروف');

        if (empty($emp_id) || empty($name) || empty($role) || empty($email)) {
            $emp_message = "<div class='alert alert-danger'>جميع الحقول مطلوبة.</div>";
        } else {
            try {
                $token = bin2hex(random_bytes(16));
                $expiry = date('Y-m-d H:i:s', time() + 86400);

                $stmt = $conn->prepare("INSERT INTO employee 
                (emp_id, name, role, email, setup_token, setup_expiry, password, signature, last_vac, used_days, remaining_days, address, phone)
                VALUES (?, ?, ?, ?, ?, ?, '', '', CURDATE(), 0, 0, '', '')");

                if (!$stmt) throw new Exception("فشل تحضير الاستعلام: " . $conn->error);

                $stmt->bind_param("ssssss", $emp_id, $name, $role, $email, $token, $expiry);
                if (!$stmt->execute()) {
                    throw new Exception("فشل تنفيذ الاستعلام: " . $stmt->error);
                }

                if ($stmt->affected_rows === 0) {
                    throw new Exception("لم يتم إدخال الموظف (affected_rows = 0).");
                }

                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                $domain = $_SERVER['HTTP_HOST'];
                $link = $protocol . $domain . "/COOP_System/setupPassword.php?token=" . urlencode($token);

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'fatemah36618@gmail.com';
                $mail->Password = 'yzat lisb xubr ggvq';  // secure this
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';

                $mail->setFrom('fatemah36618@gmail.com', 'COOP System');
                $mail->addAddress($email, $name);
                $mail->Subject = "إعداد حسابك - نظام الموارد البشرية";
                $mail->isHTML(true);
                $mail->Body = "
                    <p>مرحباً $name،</p>
                    <p>تم تسجيلك في النظام، يمكنك إعداد كلمة المرور عبر الرابط التالي:</p>
                    <p><a href='$link'>$link</a></p>
                    <p>هذا الرابط صالح لمدة 24 ساعة.</p>
                    <p>مع التحية،<br>فريق الموارد البشرية</p>";

                $mail->send();
                $emp_message = "<div class='alert alert-success'>تمت إضافة الموظف <strong>$displayName</strong> وإرسال رابط إعداد كلمة المرور بنجاح.</div>";
                $emp_id = $name = $role = $email = "";

            } catch (Exception $e) {
                $emp_message = "<div class='alert alert-warning'>خطأ: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }

    if (isset($_POST['add_con_type'])) {
        $con_type = trim($_POST['con_type'] ?? '');
        $con_terms = trim($_POST['con_terms'] ?? '');
        $extra_terms = "";

        try {
            if (empty($con_type) || empty($con_terms)) {
                throw new Exception("جميع حقول العقد مطلوبة");
            }

            $stmt = $conn->prepare("INSERT INTO terms (con_type, con_terms, extra_terms) VALUES (?, ?, ?)");
            if (!$stmt) throw new Exception("تحضير الاستعلام فشل: " . $conn->error);
            $stmt->bind_param("sss", $con_type, $con_terms, $extra_terms);
            if (!$stmt->execute()) throw new Exception("فشل تنفيذ الاستعلام: " . $stmt->error);

            $con_message = "<div class='alert alert-success'>تمت إضافة نوع العقد بنجاح</div>";
        } catch (Exception $e) {
            $con_message = "<div class='alert alert-danger'>خطأ: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم المدير</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; }
        .r-container { max-width: 1140px; margin: auto; padding: 2rem; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .card-header { background-color: #0d6efd; color: white; border-radius: 10px 10px 0 0 !important; }
        .form-select, .form-control { margin-bottom: 15px; }
        textarea.form-control { min-height: 120px; }
    </style>
</head>
<body class="bg-light">

<?php include 'header.php'; ?>

<div class="r-container">
    <h2 class="mb-4 text-center">لوحة تحكم المدير</h2>

    <div class="card">
        <div class="card-header">
            <h4>إضافة موظف جديد</h4>
        </div>
        <div class="card-body">
            <?= $emp_message ?>
            <form method="POST" novalidate>
                <input type="hidden" name="add_employee" value="1">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">رقم الموظف</label>
                        <input type="text" name="emp_id" class="form-control" required value="<?= htmlspecialchars($emp_id) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($name) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الوظيفة</label>
                        <select name="role" class="form-select" required>
                            <option value="">اختر الوظيفة</option>
                            <option value="employee" <?= ($role === 'employee') ? 'selected' : '' ?>>موظف</option>
                            <option value="manager" <?= ($role === 'manager') ? 'selected' : '' ?>>مدير</option>
                            <option value="finance" <?= ($role === 'finance') ? 'selected' : '' ?>>مالية</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($email) ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="new-request-btn">إضافة الموظف</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>إضافة نوع عقد جديد</h4>
        </div>
        <div class="card-body">
            <?= $con_message ?>
            <form method="POST" novalidate>
                <input type="hidden" name="add_con_type" value="1">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">نوع العقد</label>
                        <input type="text" name="con_type" class="form-control" required value="<?= htmlspecialchars($con_type) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">بنود العقد</label>
                        <textarea name="con_terms" class="form-control" required><?= htmlspecialchars($con_terms) ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="new-request-btn">إضافة نوع العقد</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
