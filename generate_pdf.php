<?php
require_once('tcpdf/tcpdf.php');
require_once("db_connect.php");

$vac_id = $_GET['vac_id'] ?? null;
if (!$vac_id) {
    die("رقم الطلب غير موجود.");
}

// Fetch vacation and employee data for this request
$stmt = $conn->prepare("
    SELECT v.*, e.name AS emp_name, e.role, e.emp_id AS emp_id, e.used_days, e.remaining_days, e.signature AS emp_signature
    FROM vacation v
    JOIN employee e ON v.emp_id = e.emp_id
    WHERE v.vac_id = ?
");
$stmt->bind_param("i", $vac_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
if (!$data) die("الطلب غير موجود.");

// Get last vacation of this employee, if any (excluding current request)
$stmt2 = $conn->prepare("
    SELECT start_date, days FROM vacation
    WHERE emp_id = ? AND vac_id <> ?
    ORDER BY start_date DESC LIMIT 1
");
$stmt2->bind_param("ii", $data['emp_id'], $vac_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$last_vac = $result2->fetch_assoc();
$stmt2->close();

$last_vac_date = $last_vac ? $last_vac['start_date'] : 'لا يوجد';
$last_vac_days = $last_vac ? $last_vac['days'] : '0';

// Fetch finance person and manager info
function getEmployeeByRole($conn, $role) {
    $stmt = $conn->prepare("SELECT name, signature FROM employee WHERE role = ? LIMIT 1");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $res = $stmt->get_result();
    $emp = $res->fetch_assoc();
    $stmt->close();
    return $emp;
}
$finance = getEmployeeByRole($conn, 'finance');
$manager = getEmployeeByRole($conn, 'manager');

$conn->close();

// Hijri date converter (no intl)
function toHijri($gDate)
{
    $timestamp = strtotime($gDate);
    $day = date('j', $timestamp);
    $month = date('n', $timestamp);
    $year = date('Y', $timestamp);

    $jd = gregoriantojd($month, $day, $year);

    $l = $jd - 1948440 + 10632;
    $n = (int)(($l - 1) / 10631);
    $l = $l - 10631 * $n + 354;
    $j = (int)(((10985 - $l) / 5316)) * (int)((50 * $l) / 17719) +
         (int)($l / 5670) * (int)((43 * $l) / 15238);
    $l = $l - (int)((30 - $j) / 15) * (int)((17719 * $j) / 50) -
         (int)($j / 16) * (int)((15238 * $j) / 43) + 29;

    $m = (int)((24 * $l) / 709);
    $d = $l - (int)((709 * $m) / 24);
    $y = 30 * $n + $j - 30;

    $months = [
        1 => 'محرم', 2 => 'صفر', 3 => 'ربيع الأول', 4 => 'ربيع الآخر',
        5 => 'جمادى الأولى', 6 => 'جمادى الآخرة', 7 => 'رجب', 8 => 'شعبان',
        9 => 'رمضان', 10 => 'شوال', 11 => 'ذو القعدة', 12 => 'ذو الحجة'
    ];

    return "$d " . $months[$m] . " $y هـ";
}

// Convert signatures (blob) to base64 for embedding
function getSignatureImageTag($signature_blob) {
    if (!$signature_blob) {
        return '<span style="font-style: italic; color: #999;">(لا يوجد توقيع)</span>';
    }
    $img64 = base64_encode($signature_blob);
    return '<img src="@' . $img64 . '" style="height:60px;" />';
}

// Extract info
$name       = $data['emp_name'];
$role       = $data['role'];
$emp_id     = $data['emp_id'];
$type       = $data['type'];
$days       = $data['days'];
$start_date = $data['start_date'];
$end_date   = $data['end_date'];
$assigned   = $data['assigned_emp'];
$applied_on = $data['application_date'];
$used       = $data['used_days'];
$remaining  = $data['remaining_days'];

$hijri_start = toHijri($start_date);
$hijri_end   = toHijri($end_date);

$emp_signature_tag = getSignatureImageTag($data['emp_signature']);
$fin_signature_tag = getSignatureImageTag($finance['signature'] ?? null);
$man_signature_tag = getSignatureImageTag($manager['signature'] ?? null);

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setRTL(true);
$pdf->SetTitle('طلب إجازة');
$pdf->SetMargins(15, 30, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(15);
$pdf->SetAutoPageBreak(true, 25);
$pdf->SetFont('aealarabiya', '', 11);
$pdf->AddPage();

// Add logo top-right
$img_file = 'logo_black.jpg';
$pdf->Image($img_file, 160, 10, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);


$html = '
<h3 align="center">خاص بالموظفـ/ـة:</h3>
<p align="right">سعادة/ مديرة مركز التعليم المستمر                   سلمه الله</p>
<p align="right">السلام عليكم ورحمة الله وبركاته،،،</p>
<p align="right">أتقدم بطلب إجازة <strong>(' . htmlspecialchars($type) . ')</strong></p>
<p align="right">وذلك لمدة: <strong>(' . $days . ')</strong> يوم اعتبارًا من تاريخ <strong>(' . $hijri_start . ')</strong> الموافق <strong>(' . $start_date . ')</strong>
وحتى تاريخ <strong>(' . $hijri_end . ')</strong> الموافق <strong>(' . $end_date . ')</strong></p>
<p align="right">الاسم: ' . htmlspecialchars($name) . ' | الوظيفة: ' . htmlspecialchars($role) . ' | الرقم الوظيفي: ' . htmlspecialchars($emp_id) . '</p>

<p align="right" style="margin:0 0 10px 0;">
    التوقيع:  <br>
    ' . $emp_signature_tag . '<br>
    تاريخ التقديم: ' . date('Y-m-d', strtotime($applied_on)) . '
</p>

<p align="right">اسم الشخص المكلف: ' . htmlspecialchars($assigned) . ' | التوقيع: ________________</p>
<hr>
<h3 align="center">خاص بالشؤون الإدارية والمالية:</h3>
<p align="right">- رصيد الموظف المستهلك: ' . $used . ' يوم</p>
<p align="right">- رصيد الموظف المتبقي: ' . $remaining . ' يوم</p>
<p align="right">- تاريخ آخر إجازة: ' . $last_vac_date . ' | مدتها: ' . $last_vac_days . ' يوم</p>
<p align="right">الإجازة: [ ] مستحقة نظامًا    [ ] غير مستحقة نظامًا</p>
<p align="right">مكتب مدير الشؤون الإدارية والمالية</p>
<table cellpadding="5" cellspacing="0" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
<tr>
    <td style="width: 50%; vertical-align: top; border-top: 1px solid #000; text-align: center; padding-top: 8px;">
        الاسم: ' . htmlspecialchars($finance['name'] ?? '________________') . '<br />
        ' . $fin_signature_tag . '<br />
        التوقيع: 
    </td>
</tr>
<table cellpadding="5" cellspacing="0" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
<tr>
    <td style="width: 50%; vertical-align: top; border-top: 1px solid #000; text-align: center; padding-top: 8px;">
        <strong>الاعتماد</strong><br />
        مديرة مركز التعليم المستمر<br />
        الاسم: ' . htmlspecialchars($manager['name'] ?? '________________') . '<br />
        ' . $man_signature_tag . '<br />
        التوقيع: 
    </td>
</tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('vacation_request_' . $vac_id . '.pdf', 'I');
