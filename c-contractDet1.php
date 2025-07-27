<?php
session_start();
include 'db_connect.php';



$contract_code = $_SESSION['contract_code'] ?? null;
$contract_type = $_SESSION['contract_type'] ?? null;
$emp_id=$_SESSION['emp_id']??null;


if(!empty($contract_code)&&!empty($contract_type)){
$stmt = $conn->prepare("SELECT * FROM contract WHERE con_id = ?");
$stmt->bind_param("i", $contract_code);
$stmt->execute();
$result = $stmt->get_result();
$contract = $result->fetch_assoc();


// --- Get contract terms based on type ---
$stmt2 = $conn->prepare("SELECT con_terms, extra_terms FROM terms WHERE con_type = ?");
$stmt2->bind_param("s", $contract_type);
$stmt2->execute();
$result2 = $stmt2->get_result();
$terms = $result2->fetch_assoc();

$stmt3 = $conn->prepare("SELECT * FROM employee  WHERE name = ?");
$stmt3->bind_param("s", $contract['1st_party']);
$stmt3->execute();
$result3 = $stmt3->get_result();
$firstParty= $result3->fetch_assoc();

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_invite'])) {
    $inviteEmail = filter_var($_POST['invite_email'], FILTER_VALIDATE_EMAIL);

    if ($inviteEmail && !empty($contract_code) && !empty($contract_type)) {
        // Construct the URL to this page
        $baseURL = "http://COOP_System/c-cotractDet1.php"; // Replace with your actual URL
        $link = $baseURL . "?code=" . urlencode($contract_code) . "&type=" . urlencode($contract_type);

        // Send email
        $subject = "دعوة لمراجعة العقد";
        $message = "يرجى مراجعة العقد عبر الرابط التالي:\n\n" . $link;
        $headers = "From: contracts@yourdomain.com\r\n";  // Change this to your actual sender

        if (mail($inviteEmail, $subject, $message, $headers)) {
            echo "<script>alert('تم إرسال الدعوة بنجاح.');</script>";
        } else {
            echo "<script>alert('حدث خطأ أثناء إرسال الدعوة.');</script>";
        }
    } else {
        echo "<script>alert('يرجى إدخال بريد إلكتروني صحيح.');</script>";
    }
}


// --- Show the data ---
?>
<!DOCTYPE html>
<html lang=\"ar\">
<head>
  <meta charset=\"UTF-8\">
  <title>مراجعة العقد</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    html {
      scroll-behavior: smooth;
    }
    section {
      margin: 40px 0;
    }
    #warningMsg {
  opacity: 0;
  transition: opacity 0.3s ease;
}
#warningMsg.visible {
  opacity: 1;
}


  </style>
</head>
<body>


<?php include 'header.php' ?>

<!-- القسم الأول -->
<section id="section1">
  <div class="form-box">
    <h2 class="form-title">تفاصيل تنفيذ العقد</h2>
    <form>
     <div class="form-group">
  <label>التاريخ الميلادي:</label>
  <input type="text" class="form-control" id="gregorianDate" value="<?= htmlspecialchars($contract['con_date']) ?>" readonly>
</div>

<div class="form-group">
  <label>التاريخ الهجري:</label>
  <input type="text" class="form-control" id="hijriDate" readonly>
</div>

<div class="form-group">
  <label>اليوم:</label>
  <input type="text" class="form-control" id="dayName" readonly>
</div>

      <div class="form-group">
        <label>الطرف الأول:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($contract['1st_party']) ?>" readonly>
      </div>

    </form>
  </div>
</section>

<!-- القسم الثاني -->
<section id="section2">
  <div class="form-box">
    <h2 class="form-title">بيانات العقد</h2>
    <form>
      <div class="form-group">
        <label>رمز العقد:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($contract['con_id']) ?>" readonly>
      
      </div>

      <div class="form-group">
        <label>اسم البرنامج:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($contract['program_name']) ?>" readonly>
      </div>

      <div class="form-group">
        <label>رمز البرنامج:</label>
        <input type="textarea" class="form-control" value="<?= htmlspecialchars($contract['program_id']) ?>" readonly>
        <small id="code-msg"></small>
      </div>


      <div class="form-group">
        <label>إجمالي العقد:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($contract['total']) ?>" readonly>
      </div>

<div class="form-group">
  <label>مدة العقد:</label>
  <div style="display: flex; align-items: center; gap: 30px; flex-wrap: wrap;">
  <input type="text" class="form-control" value="<?= htmlspecialchars($contract['con_duration'])?>" readonly  >
  </div>
</div>



      <div class="form-group">
        <label>تاريخ بداية العقد بالميلادي:</label>
          <input type="text" class="form-control" id="contractStartDate" value="<?= htmlspecialchars($contract['con_starting_date']) ?>" readonly>
      </div>

      <div class="form-group">
        <label>تاريخ بداية العقد بالهجري:</label>
        <input type="text" class="form-control" id="startHijri" readonly>
      </div>
    </form>
  </div>
</section>

<!-- القسم الثالث -->
<section id="section3">
  <div class="form-box">
    <h2 class="form-title">بيانات الطرف الأول</h2>
    <form id="contractFormAll" method="POST">
      <div class="form-group">
        <label>الاسم:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($firstParty['name']) ?>" readonly>
      </div>

      <div class="form-group">
        <label>الصفة:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($firstParty['role']) ?>" readonly>
      </div>

      <div class="form-group">
        <label>العنوان:</label>
        <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($firstParty['address']) ?></textarea>
      </div>

      <div class="form-group">
        <label>رقم الهاتف:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($firstParty['phone']) ?>" readonly>
      </div>

      <div class="form-group">
        <label>البريد الإلكتروني:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($firstParty['email']) ?>" readonly>
      </div>

    </form>
  </div>
</section>

<section id="section4">
  <div class="t-container">
    <h2 class="form-title">شروط العقد</h2>
    <div><?= nl2br(htmlspecialchars($terms['con_terms'] ?? '')) ?></div></br>
    <div><?= nl2br(htmlspecialchars($terms['extra_terms'] ?? '')) ?></div>
  </div>
</section> 
<!---جزء الدعوة---->
<section id="inviteSection">
  <div class="form-box">
    <h2 class="form-title">دعوة لمراجعة العقد</h2>
    <form method="POST">
      <div class="form-group">
        <label>البريد الإلكتروني للطرف الثاني :</label>
        <input type="email" name="invite_email" class="form-control" required>
      </div>
      <input class="reset" type="submit" name="send_invite" value="إرسال الدعوة">
    </form>
  </div>
</section>
<button type="button" class="nextCD" onclick="location.href='c-adminRec.php'">التالي</button>
</br>
<?php include 'footer.php'; ?>
<script>
function convertToHijri(gregorianDateStr, outputId) {
  const gregorianDate = new Date(gregorianDateStr);
  if (isNaN(gregorianDate)) {
    document.getElementById(outputId).value = 'تاريخ غير صالح';
    return;
  }

  const hijriFormatter = new Intl.DateTimeFormat('ar-SA-u-ca-islamic', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });

  const formattedHijri = hijriFormatter.format(gregorianDate);
  document.getElementById(outputId).value = formattedHijri;
}

function getDayName(gregorianDateStr) {
  const date = new Date(gregorianDateStr);
  const weekdayFormatter = new Intl.DateTimeFormat('ar-SA', { weekday: 'long' });
  return weekdayFormatter.format(date);
}

// When page loads
window.onload = function () {
  const contractDate = document.getElementById("gregorianDate").value;
  const startDate = document.getElementById("contractStartDate").value;

  convertToHijri(contractDate, "hijriDate");
  convertToHijri(startDate, "startHijri");

  // Show day name as well
  document.getElementById("dayName").value = getDayName(contractDate);
}
</script>

</body>
</html> 
