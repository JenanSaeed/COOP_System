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
    $stmt = $conn->prepare("SELECT signature FROM employee WHERE name = ?");
    if (!$stmt) die("فشل في تحضير استعلام الموظف: " . $conn->error);
    $stmt->bind_param("s", $first_party_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $first_party_emp = $result->fetch_assoc();
    $stmt->close();

    if ($first_party_emp && !empty($first_party_emp['signature'])) {
        $imageData = $first_party_emp['signature'];
        if (@getimagesizefromstring($imageData)) {
            $employee_signature_path = __DIR__ . "/sig_first_party.png";
            file_put_contents($employee_signature_path, $imageData);
        }
    }
}

// 3. Get 2nd party name directly from contract
$second_party_name = !empty($contract['2nd_party']) ? $contract['2nd_party'] : "غير متوفر";

// 4. Fetch contract terms
$stmt = $conn->prepare("SELECT term FROM terms WHERE con_id = ?");
if (!$stmt) {
    die("فشل تحضير استعلام الشروط: " . $conn->error);
}
$stmt->bind_param("i", $con_id);
$stmt->execute();
$result = $stmt->get_result();
$terms = [];
while ($row = $result->fetch_assoc()) {
    $terms[] = $row['term'];
}
$stmt->close();

// 5. Start PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('نظام العقود');
$pdf->SetTitle('عقد');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetFont('aealarabiya', '', 14);

// 6. Contract HTML
$html = '
<h2 align="center">نموذج عقد</h2>
<p>بتاريخ: '.htmlspecialchars($contract['con_date']).'</p>
<p>تم الاتفاق بين كل من:</p>

<h4>الطرف الأول:</h4>
<p>
الاسم: '.htmlspecialchars($contract['1st_party'] ?? 'غير متوفر').'<br>
الوظيفة: مدير
</p>';

$html .= '
<h4>الطرف الثاني:</h4>
<p>
الاسم: '.htmlspecialchars($second_party_name).'<br>
البرنامج: '.htmlspecialchars($contract['program_name'] ?? 'غير متوفر').'
</p>

<p>مدة البرنامج: '.htmlspecialchars($contract['num_weeks']).' أسبوع، ابتداءً من تاريخ '.htmlspecialchars($contract['con_starting_date']).'</p>
<p>قيمة العقد: '.htmlspecialchars($contract['total']).' ريال</p>

<h4>الشروط:</h4>
<ol>';
foreach ($terms as $term) {
    $html .= '<li>'.htmlspecialchars($term).'</li>';
}
$html .= '</ol>

<br><br>
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
        الطرف الثاني: '.htmlspecialchars($second_party_name).'<br>
        _______________________
    </td>
</tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

// 7. Cleanup temp signature image
if ($employee_signature_path && file_exists($employee_signature_path)) {
    unlink($employee_signature_path);
}

// 8. Output
$pdf->Output('contract_'.$con_id.'.pdf', 'I');
?>
