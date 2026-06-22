<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// جلب الفئات من جدول categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$success = '';
$error = '';

if (isset($_SESSION['add_success'])) {
    $success = $_SESSION['add_success'];
    unset($_SESSION['add_success']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $amount = floatval($_POST['amount']);
    $category_id = intval($_POST['category_id']);
    $date = $_POST['expense_date'];
    $user_id = $_SESSION['user_id'];

    if ($amount <= 0) {
        $error = "يجب أن يكون المبلغ أكبر من صفر.";
    } else {
        $insert = $conn->query("INSERT INTO expenses (user_id, title, amount, category_id, expense_date)
                                VALUES ('$user_id', '$title', '$amount', '$category_id', '$date')");
        if ($insert) {
            $_SESSION['add_success'] = "تمت إضافة المصروف بنجاح.";
            header("Location: add_expense.php");
            exit;
        } else {
            $error = "حدث خطأ أثناء إضافة المصروف.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إضافة مصروف</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Times New Roman', Times, serif;
    }

    .expense-box {
      max-width: 450px;
      margin: 5px auto;
      padding: 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }

    h3 {
      font-weight: bold;
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      display: block;
      text-align: right;
    }

    .btn-dark {
      background-color: #000;
      border: none;
    }

    .btn-dark:hover {
      background-color: #333;
    }
  </style>
</head>
<body>

<div class="expense-box">
  <h3>إضافة مصروف جديد</h3>

  <?php if ($success): ?>
    <div class="alert alert-success text-center"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>عنوان المصروف</label>
      <input type="text" name="title" class="form-control" placeholder="مثال: بنزين، طعام..." required>
    </div>
    <div class="form-group">
      <label>المبلغ (د.ع)</label>
      <input type="number" step="0.01" name="amount" class="form-control" placeholder="ادخل المبلغ" required>
    </div>
    <div class="form-group">
      <label>الفئة</label>
      <select name="category_id" class="form-control" required>
        <option value="" disabled selected>اختر الفئة</option>
        <?php while ($cat = $categories->fetch_assoc()): ?>
          <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label>تاريخ المصروف</label>
      <input type="date" name="expense_date" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-dark btn-block">إضافة</button>
    <div class="text-center mt-3">
      <a href="dashboard.php">العودة للوحة التحكم</a>
    </div>
  </form>
</div>

</body>
</html>