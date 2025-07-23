<?php
// Start output buffering to prevent any accidental output
ob_start();
session_start();

require_once('tcpdf/tcpdf.php');
require_once('db_connect.php');

$con_id = $_GET['con_id'] ?? null;
if (!$con_id) {
    ob_end_clean();
    die("رقم العقد غير موجود.");
}

// Ensure user is logged in
if (!isset($_SESSION['emp_id'])) {
    ob_end_clean();
    die("يجب تسجيل الدخول.");
}

$logged_emp_id = $_SESSION['emp_id'];

try {
    // 1. Fetch contract
    $stmt = $conn->prepare("SELECT * FROM contract WHERE con_id = ?");
    if (!$stmt) throw new Exception("Failed to prepare contract query: " . $conn->error);
    $stmt->bind_param("i", $con_id);
    if (!$stmt->execute()) throw new Exception("Failed to execute contract query: " . $stmt->error);
    $contract = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$contract) {
        ob_end_clean();
        die("العقد غير موجود.");
    }

    // 2. Fetch terms
    $stmt = $conn->prepare("SELECT con_terms, extra_terms FROM terms WHERE con_type = ?");
    if (!$stmt) throw new Exception("Failed to prepare terms query: " . $conn->error);
    $stmt->bind_param("s", $contract['program_name']);
    if (!$stmt->execute()) throw new Exception("Failed to execute terms query: " . $stmt->error);
    $terms = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // 3. Get logged-in employee's signature
    $stmt = $conn->prepare("SELECT signature FROM employee WHERE emp_id = ?");
    if (!$stmt) throw new Exception("Failed to prepare employee query: " . $conn->error);
    $stmt->bind_param("i", $logged_emp_id);
    if (!$stmt->execute()) throw new Exception("Failed to execute employee query: " . $stmt->error);
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    $stmt->close();

    // Create temporary signature file
    $signature_path = null;
    if (!empty($employee['signature'])) {
        $signature_path = __DIR__ . "/sig_emp.png";
        file_put_contents($signature_path, $employee['signature']);
    }

    $second_party_signature = __DIR__ . "/secondPartySignature/sign.png";

    // 4. Format total
    $total_amount = (float)($contract['total'] ?? 0);
    $formatted_total = number_format($total_amount, 2);

    // 5. Create PDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetTitle('نموذج عقد رسمي');
    $pdf->SetMargins(10, 10, 10);
    $pdf->setHeaderMargin(0);
    $pdf->setFooterMargin(0);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();
    $pdf->SetFont('aealarabiya', '', 12);

    // 6. Build HTML
    $html = '
    <div style="direction: rtl; text-align: right; font-size: 13px;">
        <h3 style="text-align: center;">جامعة الإمام عبدالرحمن بن فيصل<br>مركز التعليم المستمر</h3>
        <h4 style="text-align: center; border: 1px solid black; padding: 4px;">نموذج عقد رسمي</h4>

        <table border="1" cellpadding="4" cellspacing="0" width="100%">
            <tr>
                <td>رمز العقد: '.htmlspecialchars($contract['con_id']).'</td>
                <td>تاريخ العقد: '.htmlspecialchars($contract['con_date']).'</td>
            </tr>
            <tr>
                <td>اسم البرنامج: '.htmlspecialchars($contract['program_name']).'</td>
                <td>رمز البرنامج: '.htmlspecialchars($contract['program_id']).'</td>
            </tr>
            <tr>
                <td>مدة العقد: '.htmlspecialchars($contract['con_duration']).'</td>
                <td>تاريخ بدء العقد: '.htmlspecialchars($contract['con_starting_date']).'</td>
            </tr>
            <tr>
                <td colspan="2">القيمة الإجمالية: '.$formatted_total.' ريال سعودي</td>
            </tr>
        </table>

        <br>
        <h4>الطرف الأول:</h4>
        <p>
            الاسم: '.htmlspecialchars($contract['1st_party'] ?? 'غير متوفر').'<br>
            الوظيفة: مدير
        </p>

        <h4>الطرف الثاني:</h4>
        <p>
            الاسم: '.htmlspecialchars($contract['2nd_party'] ?? 'غير متوفر').'
        </p>

        <hr>
        <h4>البنود والالتزامات:</h4>
        <p>'.nl2br(htmlspecialchars($terms['con_terms'] ?? 'لم يتم العثور على الشروط.')).'</p>';

    if (!empty($terms['extra_terms'])) {
        $html .= '<p>'.nl2br(htmlspecialchars($terms['extra_terms'])).'</p>';
    }

    $html .= '
        <br><br>
        <table width="100%" cellspacing="0">
            <tr>
                <td align="center">
                    الطرف الأول: '.htmlspecialchars($contract['1st_party'] ?? 'غير متوفر').'<br>';

    // Add signature of الطرف الأول
    if ($signature_path && file_exists($signature_path)) {
        $html .= '<img src="'.$signature_path.'" width="80">';
    } else {
        $html .= '_______________________';
    }

    $html .= '
                </td>
                <td align="center">
                    الطرف الثاني: '.htmlspecialchars($contract['2nd_party'] ?? 'غير متوفر').'<br>';

    // Add static signature image for الطرف الثاني
    if (file_exists($second_party_signature)) {
        $html .= '<img src="'.$second_party_signature.'" width="80">';
    } else {
        $html .= '_______________________';
    }

    $html .= '
                </td>
            </tr>
        </table>
    </div>';

    // Clean buffer
    ob_end_clean();

    // Output PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('contract_'.$con_id.'.pdf', 'I');

    // Cleanup
    if ($signature_path && file_exists($signature_path)) {
        unlink($signature_path);
    }

    exit;

} catch (Exception $e) {
    ob_end_clean();
    die("حدث خطأ: " . $e->getMessage());
}
