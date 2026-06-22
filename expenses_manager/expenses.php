<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;

// جلب جميع الفئات للقائمة المنسدلة
$category_list = $conn->query("SELECT * FROM categories ORDER BY name");

// جلب المصاريف مع JOIN حسب الفئة المحددة
$where = "expenses.user_id = $user_id";
if ($category_filter > 0) {
    $where .= " AND expenses.category_id = $category_filter";
}

$query = $conn->query("
    SELECT expenses.*, categories.name AS category_name
    FROM expenses
    LEFT JOIN categories ON expenses.category_id = categories.id
    WHERE $where
    ORDER BY expense_date DESC
");

// تلخيص المصاريف حسب الفئة المختارة
$summary_query = $conn->query("
    SELECT categories.name AS category_name, SUM(expenses.amount) AS total
    FROM expenses
    JOIN categories ON expenses.category_id = categories.id
    WHERE $where
    GROUP BY expenses.category_id
    ORDER BY total DESC
");

$total_expenses = $conn->query("SELECT SUM(amount) AS total FROM expenses WHERE $where")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>قائمة المصاريف</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Times New Roman', Times, serif;
    }

    .expenses-box {
      max-width: 950px;
      margin: 5px auto;
      padding: 25px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      text-align: right;
    }

    h3 {
      text-align: center;
      font-weight: bold;
      margin-bottom: 20px;
    }

    table th, table td {
      text-align: center;
      vertical-align: middle;
    }

    .btn-custom {
      min-width: 140px;
      margin: 5px;
    }

    .filter-form {
      max-width: 400px;
      margin: 20px auto 30px;
    }
  </style>
</head>
<body>

<div class="expenses-box">
  <h3>قائمة المصاريف</h3>

  <!-- فلترة حسب الفئة -->
  <form method="GET" class="filter-form">
    <div class="form-group">
      <label>اختر فئة لعرض المصاريف:</label>
      <select name="category" class="form-control" onchange="this.form.submit()">
        <option value="0">عرض الكل</option>
        <?php while ($cat = $category_list->fetch_assoc()): ?>
          <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
  </form>

  <!-- جدول المصاريف -->
  <?php if ($query->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr>
            <th>العنوان</th>
            <th>المبلغ</th>
            <th>الفئة</th>
            <th>التاريخ</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $query->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= number_format($row['amount'], 2) ?> د.ع</td>
              <td><?= htmlspecialchars($row['category_name']) ?></td>
              <td><?= $row['expense_date'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center">لا توجد مصاريف مسجلة لهذه الفئة.</div>
  <?php endif; ?>

  <!-- تحليل حسب الفئة -->
  <?php if ($summary_query->num_rows > 0): ?>
    <hr>
    <h4 class="text-center mb-3">تحليل حسب الفئة</h4>
    <div class="table-responsive">
      <table class="table table-sm table-bordered">
        <thead class="thead-light">
           <tr>
            <th>الفئة</th>
            <th>الإجمالي (د.ع)</th>
            <th>النسبة من الكل</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $summary_query->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['category_name']) ?></td>
              <td><?= number_format($row['total'], 2) ?></td>
              <td><?= $total_expenses > 0 ? round(($row['total'] / $total_expenses) * 100, 2) : 0 ?>%</td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-dark btn-custom">لوحة التحكم</a>
    <a href="add_expense.php" class="btn btn-secondary btn-custom">إضافة مصروف</a>
  </div>
</div>

</body>
</html>