<?php
require_once('tcpdf/tcpdf.php');
require_once('db_connect.php');

$con_id = $_GET['con_id'] ?? null;
if (!$con_id) die("رقم العقد غير موجود.");

// 1. Fetch contract
$stmt = $conn->prepare("SELECT * FROM contract WHERE con_id = ?");
$stmt->bind_param("i", $con_id);
$stmt->execute();
$contract = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$contract) die("العقد غير موجود.");

// 2. Fetch guest (2nd party)
$stmt = $conn->prepare("SELECT * FROM guest WHERE guest_id = ?");
$stmt->bind_param("i", $contract['guest_id']);
$stmt->execute();
$guest = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 3. Fetch first party (manager)
$first_party_id = $contract['1st_party'];
$stmt = $conn->prepare("SELECT * FROM employee WHERE emp_id = ?");
$stmt->bind_param("i", $first_party_id);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 4. Fetch terms manually based on contract/program type
$stmt = $conn->prepare("SELECT con_terms, extra_terms FROM terms WHERE con_type = ?");
$stmt->bind_param("s", $contract['program_name']);
$stmt->execute();
$terms = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 5. Generate temporary signature file for first party
function createSignatureFile($blob, $name) {
    if (!$blob) return null;
    $path = __DIR__ . "/sig_{$name}.png";
    file_put_contents($path, $blob);
    return $path;
}
$signature_path = createSignatureFile($employee['signature'], 'emp');

// 6. Start PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetTitle('نموذج عقد رسمي');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('aealarabiya', '', 12);

// 7. Output HTML
ob_start();
?>

<div style="direction: rtl; text-align: right; font-size: 13px;">
    <h3 style="text-align: center;">جامعة الإمام عبدالرحمن بن فيصل<br>مركز التعليم المستمر</h3>
    <h4 style="text-align: center; border: 1px solid black; padding: 4px;">نموذج عقد رسمي</h4>

    <table border="1" cellpadding="4" width="100%">
        <tr>
            <td>رقم العقد: <?= $contract['con_id'] ?></td>
            <td>تاريخ العقد: <?= $contract['con_date'] ?></td>
        </tr>
        <tr>
            <td>اسم البرنامج: <?= $contract['program_name'] ?></td>
            <td>رقم البرنامج: <?= $contract['program_id'] ?></td>
        </tr>
        <tr>
            <td>مدة العقد: <?= $contract['con_duration'] ?> شهر</td>
            <td>تاريخ بدء العقد: <?= $contract['con_starting_date'] ?></td>
        </tr>
        <tr>
            <td colspan="2">القيمة الإجمالية: <?= number_format($contract['total']) ?> ريال سعودي</td>
        </tr>
    </table>

    <br>
    <h4>الطرف الأول:</h4>
    <p>
        الاسم: <?= $employee['name'] ?><br>
        الوظيفة: <?= $employee['role'] ?>
    </p>

    <h4>الطرف الثاني:</h4>
    <p>
        الاسم: <?= $guest['guest_name'] ?><br>
        البريد الإلكتروني: <?= $guest['guest_email'] ?><br>
        رقم الجوال: <?= $guest['phone'] ?? 'غير متوفر' ?>
    </p>

    <hr>
    <h4>الشروط والأحكام:</h4>
    <p><?= nl2br($terms['con_terms'] ?? 'لم يتم العثور على الشروط.') ?></p>
    <?php if (!empty($terms['extra_terms'])): ?>
        <h5>شروط إضافية:</h5>
        <p><?= nl2br($terms['extra_terms']) ?></p>
    <?php endif; ?>

    <br><br>
    <table width="100%">
        <tr>
            <td align="center">
                الطرف الأول: <?= $employee['name'] ?><br>
                <?php if ($signature_path): ?>
                    <img src="<?= $signature_path ?>" width="80">
                <?php else: ?>
                    _______________________
                <?php endif; ?>
            </td>
            <td align="center">
                الطرف الثاني: <?= $guest['guest_name'] ?><br>
                _______________________
            </td>
        </tr>
    </table>
</div>

<?php
$html = ob_get_clean();
$pdf->writeHTML($html, true, false, true, false, '');

// 8. Clean temp signature
@unlink($signature_path);

// 9. Output
$pdf->Output('official_contract.pdf', 'I');
?>
