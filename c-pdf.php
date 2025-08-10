<?php
session_start();
require_once("db_connect.php");
require_once("tcpdf/tcpdf.php");

function decrypt($data, $key) {
    $data = base64_decode($data);
    if ($data === false || strlen($data) <= 16) return false;
    $iv = substr($data, 0, 16);
    $encryptedData = substr($data, 16);
    return openssl_decrypt($encryptedData, 'AES-256-CBC', $key, 0, $iv);
}

$secretKey = 'f7d9a2d91e47fcb2e3c98602c858c901'; // استخدم نفس المفتاح الذي استخدمته في التشفير

$encryptedId = $_GET['id'] ?? null;

if (!$encryptedId) {
    die("رقم العقد غير موجود.");
}

$con_id = decrypt($encryptedId, $secretKey);
if (!$con_id) {
    die("فشل في فك تشفير رقم العقد.");
}

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

// 3. Get 2nd party name
// 3. Get 2nd party name from second_party table
$second_party_name = "غير متوفر";
$stmt = $conn->prepare("SELECT name FROM second_party WHERE con_id = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param("i", $con_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $second_party_name = $row['name'];
    }
    $stmt->close();
}


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
$pdf->setRTL(true);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false); 
$pdf->AddPage();
$pdf->SetFont('aealarabiya', '', 14);

// 6. Content
$html = '
    <div style="height: 70px;"><br></div>
    <hr style="margin:100px 0; border:1px solid #666;">
<h3 align="center">نموذج عقد</h3>

<table border="1" cellpadding="6" cellspacing="0" width="100%">
<tr>
    <td width="30%"><b>رمز العقد</b></td>
    <td>'.htmlspecialchars($contract['con_id'] ?? '').'</td>
</tr>
<tr>
    <td><b>رمز البرنامج</b></td>
    <td>'.htmlspecialchars($contract['program_id'] ?? '').'</td>
</tr>
<tr>
    <td><b>تاريخ العقد</b></td>
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

if (!empty($extra_terms)) {
    $html .= '<li>'.nl2br(htmlspecialchars($extra_terms)).'</li>';
}

$html .= '</ol>';
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
    $html .= '<img src="'.$second_sig_path.'"width="80">';
} else {
    $html .= '_______________________';
}
$html .= '
    </td>
</tr>
</table>
    <hr style="margin:100px 0; border:1px solid #666;">
    <div style="height: 80px;"> <br><br><br></div>
';

$pdf->writeHTML($html, true, false, true, false, '');

// 8. Clean up
if ($employee_signature_path && file_exists($employee_signature_path)) {
    unlink($employee_signature_path);
}

@ob_end_clean();
$pdf->Output('contract_'.$con_id.'.pdf', 'I');
?>