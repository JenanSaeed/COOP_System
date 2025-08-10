<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}

require_once "db_connect.php"; // make sure this file connects to your DB

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contract_type'])) {
    $_SESSION['contract_type'] = trim($_POST['contract_type']);
    header("Location: c-adminForm.php");
    exit();
}

// Retrieve contract types from DB
$contracts = [];
$sql = "SELECT con_type FROM terms ORDER BY con_type ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $contracts[] = $row['con_type'];
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الصفحة الرئيسية - قسم العقود</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>

<main class="home-main">
  <div style="text-align: center; margin-bottom: 20px;">
    <h2>الرجاء اختيار نوع العقد:</h2>
  </div>

  <form action="" method="POST" style="display:inline-block;">
    <div class="form-buttons">
      <?php foreach ($contracts as $type): ?>
        <button type="submit" name="contract_type" value="<?php echo htmlspecialchars($type); ?>" class="home-btn">
          <?php echo htmlspecialchars($type); ?>
        </button>
      <?php endforeach; ?>
    </div>
  </form>
</main>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
