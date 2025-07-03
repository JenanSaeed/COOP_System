<?php
session_start();
include("db_connection.php");

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Insert new vacation request if form submitted
if (isset($_POST['new_request'])) {
    $stmt = $conn->prepare("INSERT INTO vacations (user_id, date_requested, status) VALUES (?, NOW(), 'معلق')");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
}

// Fetch user-specific vacations
$stmt = $conn->prepare("SELECT * FROM vacations WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$vacations = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الإجازات - موظف</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<!-- Main Vacation Section -->
<main class="vacation-page">
  <div class="vacation-container">

    <div class="vacation-title">
      <form method="POST">
        <button type="submit" name="new_request" class="new-vacation-link">
          <h3>طلب إجازة جديدة +</h3>
        </button>
      </form>
    </div>

    <div class="vacation-left">
      <h3 class="vacation-title">الإجازات السابقة</h3>

      <?php if (empty($vacations)): ?>
        <p>لا توجد إجازات سابقة.</p>
      <?php else: ?>
        <?php foreach ($vacations as $vac): ?>
          <?php
            $status_color = match($vac['status']) {
              'مقبول' => 'green',
              'مرفوض' => 'red',
              'معلق' => 'coral',
              default => 'gray'
            };
          ?>
          <div class="vacation-card">
            <span class="status-dot" style="background-color: <?= $status_color ?>;"></span>
            <span><?= htmlspecialchars($vac['status']) ?></span>
            <span class="vac-date"><?= htmlspecialchars($vac['date_requested']) ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
