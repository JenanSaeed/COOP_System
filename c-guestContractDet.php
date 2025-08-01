<?php
include 'db_connect.php';

// Get contract ID from URL
$contractId = $_GET['id'] ?? null;

if (!$contractId) {
    die("رمز العقد مفقود.");
}

// --- Fetch contract ---
$stmt = $conn->prepare("SELECT * FROM contract WHERE con_id = ?");
$stmt->bind_param("i", $contractId);
$stmt->execute();
$result = $stmt->get_result();
$contract = $result->fetch_assoc();

if (!$contract) {
    die("لم يتم العثور على العقد.");
}

// --- Fetch contract terms ---
$stmt2 = $conn->prepare("SELECT con_terms, extra_terms FROM terms WHERE con_type = ?");
$stmt2->bind_param("s", $contract['con_type']);
$stmt2->execute();
$result2 = $stmt2->get_result();
$terms = $result2->fetch_assoc();

// --- Fetch 1st party details ---
$stmt3 = $conn->prepare("SELECT * FROM employee WHERE name = ?");
$stmt3->bind_param("s", $contract['1st_party']);
$stmt3->execute();
$result3 = $stmt3->get_result();
$firstParty = $result3->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>عرض العقد</title>
  <link rel="stylesheet" href="style.css" />
</head>
<?php include 'header.php' ?>
<body>
<section id="contractFullView">
    <div class="form-box">
        <h2 class="form-title">مراجعة بيانات العقد</h2>
 
        <p>رمز العقد: <?= htmlspecialchars($contract['con_id']) ?></p>
        <p>تاريخ التنفيذ: <?= htmlspecialchars($contract['con_date']) ?></p>
        <p>الطرف الأول: <?= htmlspecialchars($contract['1st_party']) ?></p>
        <p>اسم البرنامج: <?= htmlspecialchars($contract['program_name']) ?></p>
        <p>رمز البرنامج: <?= htmlspecialchars($contract['program_id']) ?></p>
        <p>إجمالي العقد: <?= htmlspecialchars($contract['total']) ?></p>
        <p>مدة العقد: <?= htmlspecialchars($contract['con_duration']) ?></p>

        <h3>بيانات الطرف الأول</h3>
        <p>الاسم: <?= htmlspecialchars($firstParty['name']) ?></p>
        <p>الصفة: <?= htmlspecialchars($firstParty['role']) ?></p>
        <p>العنوان: <?= htmlspecialchars($firstParty['address']) ?></p>
        <p>رقم الهاتف: <?= htmlspecialchars($firstParty['phone']) ?></p>
        <p>البريد الإلكتروني: <?= htmlspecialchars($firstParty['email']) ?></p>
 <!-- الشروط -->
    <hr>
    <h3>بنود العقد</h3>
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
</section>  
<div class="button-container">
  <a href="c-guestSign.php?id=<?= urlencode($contract['con_id']) ?>" class="nextCD">التالي</a>
</div>  
</br>

</body>
<?php include 'footer.php' ?> 
</html>
