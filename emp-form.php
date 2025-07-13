<?php
session_start();
require_once("db_connect.php");

// Authentication checks
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_after_login'] = 'emp-form.php';
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'employee') {
    header("Location: " . ($_SESSION['role'] === 'finance' ? 'finMain.php' : 'manMain.php'));
    exit();
}

$emp_id = $_SESSION['emp_id'] ?? null;
if (!$emp_id) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $type = $_POST['type'] ?? '';
        $other_type = $_POST['other'] ?? '';
        $days = (int)($_POST['days'] ?? 0);
        $start_date = $_POST['fromDate'] ?? '';
        $end_date = $_POST['toDate'] ?? '';
        $delegate = $_POST['delegate'] ?? '';

        if (empty($type)) throw new Exception("يجب تحديد نوع الإجازة");
        if ($type === 'أخرى' && empty($other_type)) throw new Exception("يجب تحديد نوع الإجازة عندما تختار 'أخرى'");
        if ($days < 1) throw new Exception("يجب أن تكون مدة الإجازة يوم على الأقل");
        if (empty($start_date) || empty($end_date)) throw new Exception("يجب تحديد تاريخ بداية ونهاية الإجازة");
        if (strtotime($start_date) > strtotime($end_date)) throw new Exception("تاريخ النهاية يجب أن يكون بعد تاريخ البداية");

        $final_type = ($type === 'أخرى') ? $other_type : $type;

        $man_approval = 'معلق';
        $fin_approval = 'معلق';


        $stmt = $conn->prepare("INSERT INTO vacation 
        (emp_id, type, days, start_date, end_date, application_date, assigned_emp, man_approval, fin_approval) 
        VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)");

          if (!$stmt) {
              throw new Exception("Database error: " . $conn->error);
          }

        $stmt->bind_param("ssisssss", $emp_id, $final_type, $days, $start_date, $end_date, $delegate, $man_approval, $fin_approval);
        if ($stmt->execute()) {
            $success = "تم تقديم طلب الإجازة بنجاح";
            header("Refresh: 2; url=empReqs.php");
        } else {
            throw new Exception("فشل في تقديم الطلب: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>طلب إجازة جديدة</title>

    <!-- Bootstrap RTL + Tajawal font -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css"
    />
    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet"
    />

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
</head>
<body class="bg-light">
    <?php include 'header.php'; ?>

    <div class="container py-5">
        <div class="leave-form">
            <h2 class="form-title">طلب إجازة جديدة</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <div class="text-center">سيتم تحويلك إلى صفحة طلبات الإجازات خلال ثانيتين...</div>
            <?php endif; ?>

            <?php if (empty($success)): ?>
                <form method="POST" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label class="form-label">نوع الإجازة:</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input
                                    type="radio"
                                    name="type"
                                    value="اعتيادية"
                                    id="regular"
                                    <?= (isset($_POST['type']) && $_POST['type'] === 'اعتيادية' ? 'checked' : 'checked') ?>
                                />
                                <label for="regular">اعتيادية</label>
                            </div>
                            <div class="radio-option">
                                <input
                                    type="radio"
                                    name="type"
                                    value="مرضية"
                                    id="sick"
                                    <?= (isset($_POST['type']) && $_POST['type'] === 'مرضية' ? 'checked' : '') ?>
                                />
                                <label for="sick">مرضية</label>
                            </div>
                            <div class="radio-option">
                                <input
                                    type="radio"
                                    name="type"
                                    value="أخرى"
                                    id="other"
                                    <?= (isset($_POST['type']) && $_POST['type'] === 'أخرى' ? 'checked' : '') ?>
                                />
                                <label for="other">أخرى</label>
                            </div>
                        </div>
                        <input
                            type="text"
                            name="other"
                            id="otherType"
                            class="form-control other-type mt-2"
                            placeholder="حدد نوع الإجازة"
                            value="<?= htmlspecialchars($_POST['other'] ?? '') ?>"
                        />
                    </div>

                    <div class="form-group">
                        <label for="days" class="form-label">المدة:</label>
                        <div class="input-group">
                            <input
                                type="number"
                                min="1"
                                name="days"
                                id="days"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['days'] ?? '1') ?>"
                                required
                            />
                            <span class="input-group-text">يوم</span>
                        </div>
                    </div>

                    <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="fromDate" class="form-label">من تاريخ:</label>
            <input type="date" name="fromDate" id="fromDate" class="form-control" 
              value="<?= htmlspecialchars($_POST['fromDate'] ?? '') ?>" required>
            <small id="fromDateHijri" class="hijri-date"></small>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="toDate" class="form-label">إلى تاريخ:</label>
            <input type="date" name="toDate" id="toDate" class="form-control"
              value="<?= htmlspecialchars($_POST['toDate'] ?? '') ?>" required>
            <small id="toDateHijri" class="hijri-date"></small>
          </div>
        </div>
      </div>

                    <div class="form-group">
                        <label for="delegate" class="form-label">اسم الشخص المكلف:</label>
                        <input
                            type="text"
                            name="delegate"
                            id="delegate"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['delegate'] ?? '') ?>"
                            required
                        />
                    </div>

                    <div class="form-buttons">
                        <a href="empReqs.php" class="btn btn-secondary btn-cancel">إلغاء</a>
                        <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- moment.js + moment-hijri -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>

<script>
  function updateHijriDisplay(dateInputId, hijriDisplayId) {
    const input = document.getElementById(dateInputId);
    const display = document.getElementById(hijriDisplayId);

    function update() {
      if (!input.value) {
        display.textContent = '';
        return;
      }
      const hijriDate = moment(input.value, 'YYYY-MM-DD').format('iYYYY/iMM/iDD');
      display.textContent = `التاريخ الهجري: ${hijriDate}`;
    }

    input.addEventListener('change', update);

    // Update immediately on page load
    update();
  }

  updateHijriDisplay('fromDate', 'fromDateHijri');
  updateHijriDisplay('toDate', 'toDateHijri');
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('otherType').style.display =
                    this.value === 'أخرى' ? 'block' : 'none';
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const otherRadio = document.querySelector('input[name="type"][value="أخرى"]');
            document.getElementById('otherType').style.display =
                otherRadio.checked ? 'block' : 'none';
        });

        // Validate form inputs
        function validateForm() {
            const type = document.querySelector('input[name="type"]:checked').value;
            const otherType = document.getElementById('otherType').value.trim();
            const days = parseInt(document.getElementById('days').value);
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            const delegate = document.getElementById('delegate').value.trim();

            if (type === 'أخرى' && otherType === '') {
                alert('يجب تحديد نوع الإجازة عندما تختار "أخرى"');
                return false;
            }

            if (days < 1) {
                alert('يجب أن تكون مدة الإجازة يوم على الأقل');
                return false;
            }

            if (!fromDate || !toDate) {
                alert('يجب تحديد تاريخ بداية ونهاية الإجازة');
                return false;
            }

            if (new Date(fromDate).getTime() > new Date(toDate).getTime()) {
                alert('تاريخ النهاية يجب أن يكون بعد تاريخ البداية');
                return false;
            }

            if (!delegate) {
                alert('يجب تحديد الشخص المكلف');
                return false;
            }

            return true;
        }

        // Sync min date on toDate and reset if invalid
        document.getElementById('fromDate').addEventListener('change', function () {
            const fromDateVal = this.value;
            const toDateInput = document.getElementById('toDate');
            toDateInput.min = fromDateVal;
            if (toDateInput.value && toDateInput.value < fromDateVal) {
                toDateInput.value = fromDateVal;
            }
        });

        // Calculate days automatically
        function calculateDays() {
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            if (!fromDate || !toDate) return;
            const diff =
                (new Date(toDate).getTime() - new Date(fromDate).getTime()) /
                (1000 * 60 * 60 * 24);
            document.getElementById('days').value = diff + 1 > 0 ? diff + 1 : 1;
        }
        document.getElementById('fromDate').addEventListener('change', calculateDays);
        document.getElementById('toDate').addEventListener('change', calculateDays);
    </script>
</body>
</html>
