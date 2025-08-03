<?php
include 'header.php';
include 'db_connect.php';
 
$message = '';

$contractId = $_GET['id'] ?? null;
if (!$contractId) {
    die("رمز العقد مفقود. الرجاء الرجوع إلى الصفحة السابقة.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $required = ['name', 'role', 'nationality', 'id_number', 'issue_place', 'expiry_date', 'address', 'phone', 'email', 'bank', 'iban', 'signature_date', 'hijri_date'];
  $all_filled = true;

  foreach ($required as $field) {
    if (empty($_POST[$field])) {
      $all_filled = false;
      break;
    }
  }

  if (!$all_filled) {
    $message = '<div class="GSmessage error">يرجى تعبئة جميع الحقول</div>';
  } elseif (!isset($_FILES['signature']) || $_FILES['signature']['error'] !== UPLOAD_ERR_OK) {
    $message = '<div class="GSmessage error">يرجى إرفاق التوقيع بشكل صحيح</div>';
  } else {
    $uploadDir = 'secondPartySignature/';
    $fileTmpPath = $_FILES['signature']['tmp_name'];
    $destPath = $uploadDir . 'sign.png'; // Always save as 'sign.png'

    if (move_uploaded_file($fileTmpPath, $destPath)) {
      // Prepare data from POST
      $con_id = $_POST['con_id'];
      $id_number = $_POST['id_number'];
      $name = $_POST['name'];
      $role = $_POST['role'];
      $nationality = $_POST['nationality'];
      $issue_place = $_POST['issue_place'];
      $expiry_date = $_POST['expiry_date'];
      $address = $_POST['address'];
      $phone = $_POST['phone'];
      $email = $_POST['email'];
      $bank = $_POST['bank'];
      $iban = $_POST['iban'];

      // Insert into DB
      $stmt = $conn->prepare("INSERT INTO second_party 
        (id_number, con_id, name, role, nationality, issue_place, expiry_date, address, phone, email, bank, iban)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

      $stmt->bind_param("ssssssssssss",
        $id_number, $con_id, $name, $role, $nationality, $issue_place, $expiry_date, $address, $phone, $email, $bank, $iban);

      if ($stmt->execute()) {
        session_start();
        $_SESSION['second_party_data'] = [
          'name' => $name,
          'role' => $role,
          'nationality' => $nationality,
          'id_number' => $id_number,
          'issue_place' => $issue_place,
          'expiry_date' => $expiry_date,
          'address' => $address,
          'phone' => $phone,
          'email' => $email,
          'bank' => $bank,
          'iban' => $iban,
          'signature_date' => $_POST['signature_date'],
          'hijri_date' => $_POST['hijri_date'],
          'signature_path' => $destPath
        ];

        header("Location: c-pdf.php?con_id=" . urlencode($contractId));

        exit;
      } else {
        $message = '<div class="GSmessage error">خطأ في حفظ البيانات في قاعدة البيانات: ' . htmlspecialchars($stmt->error) . '</div>';
      }

      $stmt->close();
    } else {
      $message = '<div class="GSmessage error">فشل في نقل الملف إلى المجلد المحدد</div>';
    }
  }
}


?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>نموذج توقيع الطرف الثاني</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="GScontainer">
  <form method="POST" enctype="multipart/form-data" class="GSform">

    <!-- Right Section -->
    
      <div class="GSright-section">
        <label class="GSlabel" for="signature_input">الرجاء إرفاق توقيع الطرف الثاني:</label>
        <label class="GSsignature-upload" for="signature_input">📎 اضغط هنا لإرفاق التوقيع</label>
        <input type="file" name="signature" id="signature_input" accept="image/*" style="display: none;">
        <img id="signature_preview" style="max-width: 100%; margin-top: 10px; display: none;" />

        <label class="GSlabel" for="hijri_date">التاريخ الهجري:</label>
        <input class="GSinput" type="text" name="hijri_date" id="hijri_date" readonly>

        <label class="GSlabel" for="signature_date">التاريخ الميلادي:</label>
        <input class="GSinput" type="date" name="signature_date" value="<?= date('Y-m-d'); ?>">
        <input type="hidden" name="con_id" value="<?= htmlspecialchars($contractId) ?>">


      <?php if (!empty($message)) echo $message; ?>
   
      </div>
   

    <!-- Left Section -->
    <div class="GSleft-section">
      <?php
      $fields = [
        'name' => 'الاسم',
        'role' => 'الصفة',
        'nationality' => 'الجنسية',
        'id_number' => 'رقم الهوية',
        'issue_place' => 'مكان الإصدار',
        'expiry_date' => 'تاريخ الانتهاء',
        'address' => 'العنوان',
        'phone' => 'رقم الهاتف',
        'email' => 'البريد الإلكتروني',
        'bank' => 'اسم المصرف',
        'iban' => 'رقم الايبان'
      ];

      foreach ($fields as $name => $label) {
        echo "<label class='GSlabel' for='$name'>$label:</label>";
        if ($name === 'address') {
          echo "<textarea class='GStextarea' name='$name' rows='2'></textarea>";
        } else {
          $type = ($name === 'expiry_date') ? 'date' : 'text';
          echo "<input class='GSinput' type='$type' name='$name'>";
        }
      }
      ?>
    <div class="GSform-buttons"> 
    
      <button type="submit" class="GSbutton">إتمام الطلب</button>
    </div>  
      </div>
     
    
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.js"></script>
<script>
  window.onload = function () {
    const hijriInput = document.querySelector('input[name="hijri_date"]');
    const todayHijri = moment().format('iYYYY-iMM-iDD');
    if (hijriInput) hijriInput.value = todayHijri;
  };

  const input = document.getElementById('signature_input');
  const preview = document.getElementById('signature_preview');
  input.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      preview.style.display = 'block';
      preview.src = URL.createObjectURL(file);
    }
  });
</script>
  <?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
