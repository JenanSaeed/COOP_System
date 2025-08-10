<?php
require_once('tcpdf/tcpdf.php');
require_once('db_connect.php');

$vac_id = $_GET['vac_id'] ?? null;
if (!$vac_id) {
    die("رقم الطلب غير موجود.");
}

// استخراج بيانات الإجازة والموظف
$stmt = $conn->prepare("
    SELECT v.*, e.name AS emp_name, e.role, e.emp_id, e.used_days, e.remaining_days, e.signature AS emp_signature
    FROM vacation v
    JOIN employee e ON v.emp_id = e.emp_id
    WHERE v.vac_id = ?
");
$stmt->bind_param("i", $vac_id);
$stmt->execute();
$result = $stmt->get_result();
$vac = $result->fetch_assoc();
$stmt->close();

// التحقق من وجود الطلب
if (!$vac) {
    die("الطلب غير موجود.");
}

// استخراج آخر إجازة مقبولة سابقة
$stmt = $conn->prepare("SELECT start_date, end_date, days FROM vacation WHERE emp_id = ? AND vac_id != ? AND man_approval = 'مقبول' ORDER BY end_date DESC LIMIT 1");
$stmt->bind_param("ii", $vac['emp_id'], $vac_id);
$stmt->execute();
$last_vac = $stmt->get_result()->fetch_assoc();
$stmt->close();

// استخراج توقيعات المدير والمالية
function getSignatureByRole($conn, $role) {
    $stmt = $conn->prepare("SELECT signature FROM employee WHERE role = ?");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $stmt->bind_result($sig);
    $stmt->fetch();
    $stmt->close();
    return $sig;
}

$finance_signature = getSignatureByRole($conn, 'finance');
$manager_signature = getSignatureByRole($conn, 'manager');

$conn->close();

// تحديد هل الإجازة مستحقة نظامًا
$eligibility = match($vac['fin_approval']) {
    'مقبول' => '✔ مستحقة نظامًا',
    'مرفوض' => '✘ غير مستحقة نظامًا',
    default  => '— لم يتم تقييمها'
};

// إنشاء ملف PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetTitle('نموذج الإجازة');
$pdf->SetMargins(10, 10, 10);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false); 

$pdf->AddPage();
$pdf->SetFont('aealarabiya', '', 11);

// إعداد التوقيعات المؤقتة (Base64 -> ملف مؤقت)
function createImageFile($blob, $name) {
    if (!$blob) return null;
    $path = __DIR__ . "/tmp_sig_{$name}.png";
    file_put_contents($path, $blob);
    return $path;
}
$emp_sig_path = createImageFile($vac['emp_signature'], 'emp');
$fin_sig_path = createImageFile($finance_signature, 'fin');
$mgr_sig_path = createImageFile($manager_signature, 'mgr');

// HTML
ob_start();
?>
<div style="height: 90px;"> <br><br><br></div>
<hr style="margin:20px 0; border:1px solid #666;">

<div style="direction: rtl; text-align: right; font-size: 11px;">
    <h4>خاص بالموظفـ/ـة:</h4>
    <p>
        سعادة/ مديرة مركز التعليم المستمر سلمهـا الله<br>
        السلام عليكم ورحمة الله وبركاته،،،<br>
        أتقدم بطلب إجازة (<?= $vac['type'] ?>)،
        وذلك لمدة (<?= $vac['days'] ?>) يوم، من <?= $vac['start_date'] ?> إلى <?= $vac['end_date'] ?>.
    </p>
    <p>
        الاسم: <?= $vac['emp_name'] ?> |
        الوظيفة: <?= $vac['role'] ?> |
        الرقم الوظيفي: <?= $vac['emp_id'] ?>
    </p>
    <p>توقيع الموظف:</p>
    <?php if ($emp_sig_path): ?>
        <img src="<?= $emp_sig_path ?>" width="60">
    <?php endif; ?>
    <p>تاريخ التقديم: <?= $vac['application_date'] ?></p>
    <p>الشخص المكلف أثناء الإجازة: <?= $vac['assigned_emp'] ?> | التوقيع: ______________</p>

<hr style="margin:20px 0; border:1px solid #666;">

    <h4>خاص بالشؤون الإدارية والمالية:</h4>
    <p>رصيد الإجازات المستخدم: <?= $vac['used_days'] ?> يوم</p>
    <p>رصيد الإجازات المتبقي: <?= $vac['remaining_days'] ?> يوم</p>
    <?php if ($last_vac): ?>
        <p>آخر إجازة من <?= $last_vac['start_date'] ?> إلى <?= $last_vac['end_date'] ?> (<?= $last_vac['days'] ?> يوم)</p>
    <?php else: ?>
        <p>لم يسجل إجازات سابقة</p>
    <?php endif; ?>
    <p>الإجازة: <?= $eligibility ?></p>
    <p>مدير الشؤون الإدارية والمالية:</p>
    <?php if ($fin_sig_path): ?>
        <img src="<?= $fin_sig_path ?>" width="60">
    <?php endif; ?>

<hr style="margin:20px 0; border:1px solid #666;">

    <h4>اعتماد صاحب الصلاحية:</h4>
    <p>مديرة مركز التعليم المستمر:</p>
    <?php if ($mgr_sig_path): ?>
        <img src="<?= $mgr_sig_path ?>" width="60">
    <?php endif; ?>
        <hr style="margin:100px 0; border:1px solid #666;">
    <div style="height: 80px;"> <br><br><br></div>
</div>

<?php
$html = ob_get_clean();
$pdf->writeHTML($html, true, false, true, false, '');

// حذف ملفات التواقيع المؤقتة
@unlink($emp_sig_path);
@unlink($fin_sig_path);
@unlink($mgr_sig_path);

$pdf->Output('vacation_form.pdf', 'I');
