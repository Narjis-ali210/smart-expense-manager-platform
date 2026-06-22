<?php
session_start();
include 'db.php';

$error = '';

// عرض رسالة الخطأ من الجلسة إن وجدت (ثم حذفها)
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

// التحقق من إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استقبال البيانات من الفورم
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // البحث عن المستخدم
    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // تحقق من كلمة المرور
        if (password_verify($password, $user['password'])) {
            // تسجيل الدخول
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: dashboard.php");
            exit;
        } else {
            // كلمة المرور خاطئة
            $_SESSION['login_error'] = "كلمة المرور غير صحيحة.";
            header("Location: login.php");
            exit;
        }
    } else {
        // اسم المستخدم غير موجود
        $_SESSION['login_error'] = "اسم المستخدم غير موجود.";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Times New Roman', Times, serif;
    }

    .login-box {
      max-width: 380px;
      margin: 5px auto;
      padding: 25px;
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }

    .logo-box {
      text-align: center;
      margin-bottom: 15px;
    }

    .logo-box img {
      width: 65px;
    }

    h3 {
      font-weight: bold;
      font-size: 1.4rem;
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      text-align: right;
      display: block;
    }

    .btn-dark {
      background-color: #000;
      border: none;
    }

    .btn-dark:hover {
      background-color: #333;
    }

    .alert {
      text-align: center;
    }
  </style>
</head>
<body>

<div class="login-box">
  <!-- لوجو -->
  <div class="logo-box">
    <img src="logo.png" alt="Logo">
  </div>

  <!-- عنوان -->
  <h3>تسجيل الدخول</h3>

  <!-- رسالة خطأ -->
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <!-- النموذج -->
  <form method="POST">
    <div class="form-group">
      <label>اسم المستخدم</label>
      <input type="text" name="username" class="form-control" placeholder="ادخل اسم المستخدم" required>
    </div>
    <div class="form-group">
      <label>كلمة المرور</label>
      <input type="password" name="password" class="form-control" placeholder="ادخل كلمة المرور" required>
    </div>
    <button type="submit" class="btn btn-dark btn-block">تسجيل دخول</button>
    <div class="text-center mt-3">
      ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a>
    </div>
  </form>
</div>

</body>
</html>