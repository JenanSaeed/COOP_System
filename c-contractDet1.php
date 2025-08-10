<?php
session_start();
include 'db_connect.php';

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';

// حذف الرسائل من السيشن بعد الحفظ
unset($_SESSION['success_message'], $_SESSION['error_message']);

$con_id = $_GET['id'] ?? ($_SESSION['contract_code'] ?? null);
if (!$con_id) {
    die("عذرًا، لم يتم تمرير رمز العقد.");
}

// جلب بيانات العقد
$stmt = $conn->prepare("SELECT * FROM contract WHERE con_id = ?");
$stmt->bind_param("s", $con_id);
$stmt->execute();
$result = $stmt->get_result();
$contract = $result->fetch_assoc();
if (!$contract) {
    die("عذرًا، لم يتم العثور على بيانات العقد.");
}

// حذف الرسائل بعد عرضها لمرة واحدة
unset($_SESSION['success_message'], $_SESSION['error_message']);

// جلب نوع العقد من بيانات العقد
$contract_type = $contract['con_type'];

// جلب الشروط
$stmt2 = $conn->prepare("SELECT con_terms, extra_terms FROM terms WHERE con_type = ?");
$stmt2->bind_param("s", $contract_type);
$stmt2->execute();
$result2 = $stmt2->get_result();
$terms = $result2->fetch_assoc();

// جلب بيانات الطرف الأول
$firstParty = null;
if (!empty($contract['1st_party'])) {
    $stmt3 = $conn->prepare("SELECT * FROM employee WHERE name = ?");
    $stmt3->bind_param("s", $contract['1st_party']);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    $firstParty = $result3->fetch_assoc();
}
?>

<?php
//----- reqiured for sending invite via email----
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_invite'])) {
    $inviteEmail = $_POST['invite_email'];

    // 1. Encrypt the con_id
    function encrypt($data, $key) {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return urlencode(base64_encode($iv . $encrypted));
    }

    $secretKey = 'f7d9a2d91e47fcb2e3c98602c858c901';
    $contractId = $con_id;  // Already set at the top of the page
    $encryptedId = encrypt($contractId, $secretKey);

    // 2. Build the guest page link
    $link = "http://localhost/COOP_System/c-guestContractDet.php?id=" . $encryptedId;

    // 3. Send email (no change here)
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; //replace with your smpt
        $mail->SMTPAuth   = true;
        $mail->Username   = 'fatemah36618@gmail.com'; //replace with your designated email
        $mail->Password   = 'yzat lisb xubr ggvq';//replace with your password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->CharSet = 'UTF-8'; 
        $mail->Encoding = 'base64';

        $mail->setFrom('fatemah36618@gmail.com', 'مركز التعليم المستمر');
        $mail->addAddress($inviteEmail);
        $mail->Subject = 'دعوة لمراجعة العقد';
        $mail->Body    = "يرجى مراجعة العقد عبر الرابط التالي:\n\n" . $link;

        $mail->send();
        $_SESSION['success_message'] = "✅ تم إرسال الدعوة بنجاح.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $contractId); // Refresh
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "❌ حدث خطأ أثناء الإرسال: " . $mail->ErrorInfo;
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $contractId);
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset=\"UTF-8\">
  <title>مراجعة العقد</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>
</head>
<body class="bg-light">


<?php include 'header.php' ?>

<section id="contractFullView">
  <div class="r-container">

   <!-- رسائل النجاح والخطأ --> 
<div>
<?php if (!empty($success_message)): ?>
  <div class="alert alert-success text-center mt-3">
    <?= $success_message ?>
  </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
  <div class="alert alert-danger text-center mt-3">
    <?= $error_message ?>
  </div>
<?php endif; ?>
</div>

    <h2 class="form-title">مراجعة بيانات العقد</h2>

    <!-- التاريخ واليوم -->
    <div class="form-group">
      <label>التاريخ الميلادي:</label>
      <p class="form-control-static" id="gregorianDate"><?= htmlspecialchars($contract['con_date']) ?></p>
    </div>

    <div class="form-group">
      <label>التاريخ الهجري:</label>
      <p class="form-control-static" id="hijriDate"></p>
    </div>

    <div class="form-group">
      <label>اليوم:</label>
      <p class="form-control-static" id="dayName"></p>
    </div>

    <!-- بيانات العقد -->
    <div class="form-group">
      <label>رمز العقد:</label>
      <p class="form-control-static"><?= htmlspecialchars($contract['con_id']) ?></p>
    </div>

    <div class="form-group">
      <label>اسم البرنامج:</label>
      <p class="form-control-static"><?= htmlspecialchars($contract['program_name']) ?></p>
    </div>

    <div class="form-group">
      <label>رمز البرنامج:</label>
      <p class="form-control-static"><?= htmlspecialchars($contract['program_id']) ?></p>
    </div>

    <div class="form-group">
      <label>إجمالي العقد:</label>
      <p class="form-control-static"><?= htmlspecialchars($contract['total']) ?></p>
    </div>

    <div class="form-group">
      <label>مدة العقد:</label>
      <p class="form-control-static"><?= htmlspecialchars($contract['con_duration']) ?></p>
    </div>

    <div class="form-group">
      <label>تاريخ بداية العقد بالميلادي:</label>
      <p class="form-control-static" id="contractStartDate"><?= htmlspecialchars($contract['con_starting_date']) ?></p>
    </div>

    <div class="form-group">
      <label>تاريخ بداية العقد بالهجري:</label>
      <p class="form-control-static" id="startHijri"></p>
    </div>

    <!-- الطرف الأول -->
    <hr>
    <h2 class="form-title">بيانات الطرف الأول</h2>
    <?php if (!empty($firstParty)): ?>

    <div class="form-group">
      <label>الاسم:</label>
      <p class="form-control-static"><?= htmlspecialchars($firstParty['name']) ?></p>
    </div>

    <div class="form-group">
      <label>الصفة:</label>
      <p class="form-control-static"><?= htmlspecialchars($firstParty['role']) ?></p>
    </div>

    <div class="form-group">
      <label>العنوان:</label>
      <p class="form-control-static"><?= nl2br(htmlspecialchars($firstParty['address'])) ?></p>
    </div>

    <div class="form-group">
      <label>رقم الهاتف:</label>
      <p class="form-control-static"><?= htmlspecialchars($firstParty['phone']) ?></p>
    </div>

    <div class="form-group">
      <label>البريد الإلكتروني:</label>
      <p class="form-control-static"><?= htmlspecialchars($firstParty['email']) ?></p>
    </div>
    <?php else: ?>
    <p class="text-danger">لا توجد بيانات للطرف الأول.</p>
<?php endif; ?>

    <!-- الشروط -->
    <hr>
    <h2 class="form-title">بنود العقد</h2>
    <div class="form-group">
  <ul>
    <?php
    if (!empty($terms['con_terms'])) {
      $conTermsList = preg_split('/\r\n|\n|\r|•|-/', $terms['con_terms']);
      foreach ($conTermsList as $term) {
        $term = trim($term);
        if (!empty($term)) {
          echo '<li>' . htmlspecialchars($term) . '</li>';
        }
      }
    }
    if (!empty($terms['extra_terms'])) {
      $extraTermsList = preg_split('/\r\n|\n|\r|•|-/', $terms['extra_terms']);
      foreach ($extraTermsList as $term) {
        $term = trim($term);
        if (!empty($term)) {
          echo '<li>' . htmlspecialchars($term) . '</li>';
        }
      }
    }
    ?>

  </ul>
  
</div>


<div class="form-buttons">
<a href="c-terms.php"  class="buttons" >عودة</a>
<a class="buttons" href="c-adminRec.php">متابعة إلى سجل العقود</a>
<button id="showInviteBtn" class="buttons">إرسال دعوة</button>
</div>

</section> 


<!---جزء الدعوة---->
<section id="inviteSection" style="display:none;">
  <div class="r-container">
    <h2 class="form-title">إرسال دعوة للطرف الثاني</h2>
    <form method="POST" action="" accept-charset="UTF-8">
      <div class="form-group">
        <label for="invite_email">البريد الإلكتروني للطرف الثاني:</label>
        <input type="email" id="invite_email" name="invite_email" class="form-control" placeholder="secondParty@gmail.com" required>
      </div>
      <input class="reset" type="submit" name="send_invite" value="إرسال الدعوة">
      
    </form>
  </div>
</section>

</br>

<script>
function convertToHijri(gregorianDateStr, outputId) {
  const gregorianDate = new Date(gregorianDateStr);
  if (isNaN(gregorianDate)) {
    document.getElementById(outputId).innerText = 'تاريخ غير صالح';
    return;
  }

  const hijriFormatter = new Intl.DateTimeFormat('ar-SA-u-ca-islamic', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });

  const formattedHijri = hijriFormatter.format(gregorianDate);
  document.getElementById(outputId).innerText = formattedHijri;
}

function getDayName(gregorianDateStr) {
  const date = new Date(gregorianDateStr);
  const weekdayFormatter = new Intl.DateTimeFormat('ar-SA', { weekday: 'long' });
  return weekdayFormatter.format(date);
}

// When page loads
window.onload = function () {
  const contractDate = document.getElementById("gregorianDate").innerText;
  const startDate = document.getElementById("contractStartDate").innerText;

  convertToHijri(contractDate, "hijriDate");
  convertToHijri(startDate, "startHijri");

  document.getElementById("dayName").innerText = getDayName(contractDate);
}
  // عند الضغط على الزر
document.getElementById('showInviteBtn').addEventListener('click', function() {
  const inviteSection = document.getElementById('inviteSection');
  if (inviteSection.style.display === 'none' || inviteSection.style.display === '') {
    inviteSection.style.display = 'block';
    this.style.display = 'none';  // يخفي الزر بعد العرض
  }
});

</script>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html> 
