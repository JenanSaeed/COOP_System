<?php
include 'header.php';
include 'db_connect.php';
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

    .right-section, .left-section {
      flex: 1;
      min-width: 300px;
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
<body>

  <div class="container">
    <!-- Right Section: Signature and Dates -->
    <div class="right-section">
      <form method="POST" action="">
          <label>الرجاء إرفاق توقيع الطرف الثاني:</label>
          <label class="signature-upload" for="signature_input">📎 إرفاق التوقيع</label>
          <input type="file" name="signature" id="signature_input" accept="image/*" style="display: none;">
          <img id="signature_preview" style="max-width: 100%; margin-top: 10px; display: none;" />


          <label>التاريخ الهجري:</label>
          <input type="text" name="hijri_date">
        
          <label>التاريخ الميلادي:</label>
          <input type="date" name="birth_date">
      </form>
    </div>

    <!-- Left Section: Other Info -->
    <div class="left-section">
      <form method="POST" action="">
       
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
          <input type="text" name="expiry_date">
        
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
      </form>
    </div>
  </div>
  <?php include 'footer.php'; ?>

</body>
</html>
