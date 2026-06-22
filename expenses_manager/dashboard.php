<?php
session_start();
include 'db.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// جلب ملخص المصاريف للمستخدم الحالي
$user_id = $_SESSION['user_id'];
$total_query = $conn->query("SELECT COUNT(*) AS total_items, SUM(amount) AS total_amount FROM expenses WHERE user_id = $user_id");
$data = $total_query->fetch_assoc();

$total_items = $data['total_items'] ?? 0;
$total_amount = $data['total_amount'] ?? 0;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>لوحة التحكم</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Times New Roman', Times, serif;
    }

    .dashboard-box {
      max-width: 700px;
      margin: 5px auto;
      padding: 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }

    h3 {
      font-weight: bold;
      text-align: center;
      margin-bottom: 25px;
    }

    .stats-box {
      background-color: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      text-align: center;
    }

    .btn-custom {
      min-width: 150px;
      margin: 5px;
    }

    .logout-link {
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="dashboard-box">
  <h3>مرحباً، <?= htmlspecialchars($_SESSION['full_name']) ?>!</h3>

  <!-- الإحصائيات -->
  <div class="row">
    <div class="col-md-6">
      <div class="stats-box">
        <h5>عدد المصاريف</h5>
        <p><?= $total_items ?></p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="stats-box">
        <h5>المجموع الكلي</h5>
        <p><?= number_format($total_amount, 2) ?> د.ع</p>
      </div>
    </div>
  </div>

  <!-- أزرار التنقل -->
  <div class="text-center mt-4">
    <a href="add_expense.php" class="btn btn-dark btn-custom">إضافة مصروف</a>
    <a href="expenses.php" class="btn btn-secondary btn-custom">عرض المصاريف</a>
    <a href="settings.php" class="btn btn-outline-dark btn-custom">الإعدادات</a>
  </div>

  <div class="logout-link">
    <a href="logout.php" class="btn btn-link text-danger">تسجيل الخروج</a>
  </div>
</div>

</body>
</html>