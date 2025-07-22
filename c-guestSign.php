<?php
include 'header.php';
include 'db_connect.php';


$message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $required = ['name', 'role', 'nationality', 'id_number', 'issue_place', 'expiry_date', 'address', 'phone', 'email', 'bank', 'iban', 'signature_date', 'hijri_date'];
  foreach ($required as $field) {
    if (empty($_POST[$field])) {
      $message'<div class="message error">يرجى تعبئة جميع الحقول</div>';
    }
}

    // DEBUGGING BLOCK
    if (!isset($_FILES['signature'])) {
        $message = '<div class="message error">لم يتم العثور على ملف مرفق</div>';
    } else {
        switch ($_FILES['signature']['error']) {
            case UPLOAD_ERR_OK:
                // this is the good case – do nothing here
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message = '<div class="message error">حجم الملف كبير جدًا</div>';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = '<div class="message error">تم تحميل الملف جزئيًا فقط</div>';
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = '<div class="message error">لم يتم تحميل أي ملف</div>';
                break;
            default:
                $message = '<div class="message error">خطأ غير معروف في تحميل الملف</div>';
        }
    }

    // Continue only if file is good
    if (isset($_FILES['signature']) && $_FILES['signature']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'secondPartySignature/';
        $fileTmpPath = $_FILES['signature']['tmp_name'];
        $fileName = basename($_FILES['signature']['name']);
        $fileName = preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName); // sanitize
        $destPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $message = "<div class='message success'>تم رفع التوقيع بنجاح</div> <a href='$destPath' target='_blank'>عرض التوقيع</a>";
        } else {
            $message = '<div class="message error">فشل في نقل الملف إلى المجلد المحدد</div>';
        }
    }
}



?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>نموذج توقيع الطرف الثاني</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      direction: rtl;
      background-color: #f4f4f4;
      overflow-y: scroll;
    }

    .container {
      max-width: 1200px;
      margin: 30px auto;
      background: white;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
    }

    .left-section {
      flex: 1;
      min-width: 300px;
    }
    .right-section {
    flex: 1;
    min-width: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center; /* 👈 This centers vertically */
    }


    h2 {
      margin-bottom: 20px;
      text-align: right;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }

    input[type="text"],
    input[type="date"],
    textarea {
      width: 100%;
      padding: 8px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    textarea {
      resize: vertical;
    }

    .signature-upload {
      border: 1px dashed #aaa;
      padding: 30px;
      text-align: center;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 10px;
      font-size: 24px;
    }

    .form-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    .form-buttons button {
      background-color: #154746;
      color: white;
      border: none;
      padding: 10px 30px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    .form-buttons button:hover {
      background-color: #0f3d3c;
    }

  .message {
  margin-top: 20px;
  padding: 10px;
  border-radius: 4px;
  font-size: 14px;
  }

  .message.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
  }

  .message.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  }

  </style>
</head>
<script src="https://cdn.jsdelivr.net/npm/hijri-date/lib/hijri-date.js"></script>


<body>

  <div class="container">
     <form method="POST" action="" enctype="multipart/form-data">
    <!-- Right Section: Signature and Dates -->
      <div class="right-section">
  
          <label>الرجاء إرفاق توقيع الطرف الثاني:</label>
          <label class="signature-upload" for="signature_input">📎 </label>
          <input type="file" name="signature" id="signature_input" accept="image/*" style="display: none;">
          <img id="signature_preview" style="max-width: 100%; margin-top: 10px; display: none;" />


          <label>التاريخ الهجري:</label>
          <input type="text" name="hijri_date" id="hijri_date" readonly>
        
          <label>التاريخ الميلادي:</label>
          <input type="date" name="signature_date" value="<?= date('Y-m-d'); ?>">
          <?php if (!empty($message)) echo $message; ?>
      
      </div>

    <!-- Left Section: Other Info -->
      <div class="left-section">
      
       
          <label>الاسم:</label>
          <input type="text" name="name">
    
          <label>الصفة:</label>
          <input type="text" name="role">
    
    
          <label>الجنسية:</label>
          <input type="text" name="nationality">
        
        
          <label>رقم الهوية:</label>
          <input type="text" name="id_number">
        
        
          <label>مكان الإصدار:</label>
          <input type="text" name="issue_place">
    
    
          <label>تاريخ الانتهاء:</label>
          <input type="date" name="expiry_date">
        
          <label>العنوان:</label>
          <textarea name="address" rows="2"></textarea>
          <label>رقم الهاتف:</label>
          <input type="text" name="phone">

          <label>البريد الإلكتروني:</label>
          <input type="text" name="email">
        
          <label>اسم المصرف:</label>
          <input type="text" name="bank">
        
          <label>رقم الايبان:</label>
          <input type="text" name="iban">

        <div class="form-buttons">
          <button type="submit">السابق</button>
          <button type="submit">إتمام الطلب</button>
        </div>
        
      </div>
    </form>
  </div>

  <script>
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
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.js"></script>

  <script>
  window.onload = function () {
  const hijriInput = document.querySelector('input[name="hijri_date"]');
  
  const todayHijri = moment().format('iYYYY-iMM-iDD'); // Hijri format
  if (hijriInput) hijriInput.value = todayHijri;
};

  </script>

  <?php include 'footer.php'; ?>

</body>
</html>