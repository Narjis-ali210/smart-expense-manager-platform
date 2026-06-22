<?php
session_start();
include 'db.php';

// تحقق من الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// رسائل من الجلسة
if (isset($_SESSION['settings_success'])) {
    $success = $_SESSION['settings_success'];
    unset($_SESSION['settings_success']);
}
if (isset($_SESSION['settings_error'])) {
    $error = $_SESSION['settings_error'];
    unset($_SESSION['settings_error']);
}

// جلب بيانات المستخدم الحالية
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $changes = [];

    // تغيير الاسم إذا اختلف
    if ($full_name !== $user['full_name']) {
        $conn->query("UPDATE users SET full_name = '$full_name' WHERE id = $user_id");
        $_SESSION['full_name'] = $full_name;
        $changes[] = "الاسم";
    }

    // تغيير اسم المستخدم مع التحقق من التكرار
    if ($username !== $user['username']) {
        $check_user = $conn->query("SELECT id FROM users WHERE username = '$username' AND id != $user_id");
        if ($check_user->num_rows > 0) {
            $_SESSION['settings_error'] = "اسم المستخدم مستخدم مسبقاً.";
            header("Location: settings.php");
            exit;
        }
        $conn->query("UPDATE users SET username = '$username' WHERE id = $user_id");
        $changes[] = "اسم المستخدم";
    }

    // تغيير البريد الإلكتروني مع التحقق من التكرار
    if ($email !== $user['email']) {
        $check_email = $conn->query("SELECT id FROM users WHERE email = '$email' AND id != $user_id");
        if ($check_email->num_rows > 0) {
            $_SESSION['settings_error'] = "البريد الإلكتروني مستخدم مسبقاً.";
            header("Location: settings.php");
            exit;
        }
        $conn->query("UPDATE users SET email = '$email' WHERE id = $user_id");
        $changes[] = "البريد الإلكتروني";
    }

    // تغيير كلمة المرور إذا تم إدخالها وتأكيدها
    if (!empty($password)) {
        if ($password === $confirm_password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password = '$hashed' WHERE id = $user_id");
            $changes[] = "كلمة المرور";
        } else {
            $_SESSION['settings_error'] = "كلمة المرور وتأكيدها غير متطابقين.";
            header("Location: settings.php");
            exit;
        }
    }

    // إشعار نهائي حسب التغييرات
    if (!empty($changes)) {
        $_SESSION['settings_success'] = "تم تحديث " . implode(" و", $changes) . " بنجاح.";
    } else {
        $_SESSION['settings_success'] = "لم يتم تعديل أي شيء.";
    }

    header("Location: settings.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الإعدادات</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Times New Roman', Times, serif;
    }

    .settings-box {
      max-width: 500px;
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

<div class="settings-box">
  <h3>الإعدادات</h3>
<?php if ($success): ?>
    <div class="alert alert-success text-center"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>الاسم الكامل</label>
      <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" class="form-control" required>
    </div>
    <div class="form-group">
      <label>اسم المستخدم</label>
      <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" required>
    </div>
    <div class="form-group">
      <label>البريد الإلكتروني</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
    </div>
    <div class="form-group">
      <label>كلمة المرور الجديدة (اختياري)</label>
      <input type="password" name="password" class="form-control" placeholder="اتركها فارغة إذا لا تريد التغيير">
    </div>
    <div class="form-group">
      <label>تأكيد كلمة المرور</label>
      <input type="password" name="confirm_password" class="form-control" placeholder="كرر كلمة المرور الجديدة">
    </div>
    <button type="submit" class="btn btn-dark btn-block">حفظ التغييرات</button>
    <div class="text-center mt-3">
      <a href="dashboard.php">العودة للوحة التحكم</a>
    </div>
  </form>
</div>

</body>
</html>