
<?php



include 'db_connect.php';

function decrypt($data, $key) {
  
    $data = base64_decode($data);
    if ($data === false || strlen($data) <= 16) return false;

    $iv = substr($data, 0, 16);
    $encryptedData = substr($data, 16);
    return openssl_decrypt($encryptedData, 'AES-256-CBC', $key, 0, $iv);
}


$secretKey = 'f7d9a2d91e47fcb2e3c98602c858c901'; // Must match the one used for encryption
$encryptedId = $_GET['id'] ?? null;

$contractId = $encryptedId ? decrypt($encryptedId, $secretKey) : null;

if (!$contractId) {
    die("رمز العقد غير صالح أو مفقود.");
}

// --- Fetch contract ---
$stmt = $conn->prepare("SELECT * FROM contract WHERE con_id = ?");
$stmt->bind_param("s", $contractId); // Use "s" not "i" if con_id is a string
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

function encrypt($data, $key) {
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return urlencode(base64_encode($iv . $encrypted));
}

$encryptedForNextPage = encrypt($contract['con_id'], $secretKey);

?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>عرض العقد</title>
  <link rel="stylesheet" href="style.css" />
</head>
<?php include 'header.php' ?>
<body class="bg-light">
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
  <a href="c-guestSign.php?id=<?= $encryptedForNextPage ?>" class="nextCD">التالي</a>
</div>  
</br>

</body>
<?php include 'footer.php' ?> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>
