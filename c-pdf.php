<?php
session_start();
require_once("db_connect.php");
require_once("tcpdf/tcpdf.php");

if (!isset($_GET['con_id'])) {
    die("رقم العقد غير موجود.");
}

$con_id = intval($_GET['con_id']);

// 1. Fetch contract
$stmt = $conn->prepare("SELECT * FROM contract WHERE con_id = ?");
if (!$stmt) {
    die("فشل تحضير استعلام العقد: " . $conn->error);
}
$stmt->bind_param("i", $con_id);
$stmt->execute();
$contract = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$contract) {
    die("العقد غير موجود.");
}

// 2. Get 1st_party signature from employee table
$first_party_name = trim($contract['1st_party'] ?? '');
$employee_signature_path = null;

if (!empty($first_party_name)) {
    // نستخدم BINARY للتطابق الدقيق + إزالة الفراغات المحتملة
    $stmt = $conn->prepare("SELECT signature FROM employee WHERE BINARY TRIM(name) = ?");
    if (!$stmt) die("فشل استعلام الموظف: " . $conn->error);
    $stmt->bind_param("s", $first_party_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $emp = $result->fetch_assoc();
    $stmt->close();

    if ($emp && !empty($emp['signature'])) {
        $imageData = $emp['signature'];
        if (@getimagesizefromstring($imageData)) {
            $employee_signature_path = __DIR__ . "/sig_first_party.png";
            file_put_contents($employee_signature_path, $imageData);
        }
    }
}

// 3. Get 2nd party name directly from contract
$second_party_name = !empty($contract['2nd_party']) ? $contract['2nd_party'] : "غير متوفر";

// 4. Fetch contract terms
$con_type = $contract['con_type'] ?? null;
$terms = [];
$extra_terms = "";

if ($con_type) {
    $stmt = $conn->prepare("SELECT con_terms, extra_terms FROM terms WHERE con_type = ?");
    if (!$stmt) die("فشل استعلام الشروط: " . $conn->error);
    $stmt->bind_param("s", $con_type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $terms = preg_split('/\r\n|\r|\n/', $row['con_terms']);
        $extra_terms = $row['extra_terms'];
    }
    $stmt->close();
}

// 5. Start PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('نظام العقود');
$pdf->SetTitle('عقد');
$pdf->SetMargins(15, 15, 15);
$pdf->setRTL(true); // محاذاة لليمين
$pdf->AddPage();
$pdf->SetFont('aealarabiya', '', 14);

// 6. Content
$html = '
<h2 align="center">جامعة الإمام عبدالرحمن بن فيصل<br>مركز التعليم المستمر</h2>
<h3 align="center">نموذج عقد</h3>

<table border="1" cellpadding="6" cellspacing="0" width="100%">
<tr>
    <td width="30%"><b>تاريخ العقد</b></td>
    <td>'.htmlspecialchars($contract['con_date'] ?? '').'</td>
</tr>
<tr>
    <td><b>البرنامج</b></td>
    <td>'.htmlspecialchars($contract['program_name'] ?? '').'</td>
</tr>
<tr>
    <td><b>مدة البرنامج</b></td>
    <td>'.htmlspecialchars($contract['con_duration'] ?? 'غير محددة').' ابتداءً من تاريخ '.htmlspecialchars($contract['con_starting_date'] ?? 'غير محدد').'</td>
</tr>
<tr>
    <td><b>قيمة العقد</b></td>
    <td>'.htmlspecialchars($contract['total']).' ريال</td>
</tr>
</table>

<h4>الشروط:</h4>
<ol>';
foreach ($terms as $term) {
    $trimmed = trim($term);
    if ($trimmed !== '') {
        $html .= '<li>'.htmlspecialchars($trimmed).'</li>';
    }
}
$html .= '</ol>';

if (!empty($extra_terms)) {
    $html .= '<p>'.nl2br(htmlspecialchars($extra_terms)).'</p>';
}

// 7. Signatures
$html .= '
<br><br><br>
<table width="100%" style="text-align:center;">
<tr>
    <td>
        الطرف الأول: '.htmlspecialchars($contract['1st_party'] ?? 'غير متوفر').'<br>';
if ($employee_signature_path && file_exists($employee_signature_path)) {
    $html .= '<img src="'.$employee_signature_path.'" width="80">';
} else {
    $html .= '_______________________';
}
$html .= '
    </td>
    <td>
        الطرف الثاني: '.htmlspecialchars($second_party_name).'<br>';
$second_sig_path = __DIR__ . "/secondPartySignature/sign.png";
if (file_exists($second_sig_path)) {
    $html .= '<img src="'.$second_sig_path.'" width="80">';
} else {
    $html .= '_______________________';
}
$html .= '
    </td>
</tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

// 8. Clean up
if ($employee_signature_path && file_exists($employee_signature_path)) {
    unlink($employee_signature_path);
}

@ob_end_clean();
$pdf->Output('contract_'.$con_id.'.pdf', 'I');
?>
