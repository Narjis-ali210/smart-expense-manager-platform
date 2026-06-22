<?php
session_start(); // بدء الجلسة لعرض الرسائل بعد إعادة التوجيه

include 'db.php';

// عرض رسالة النجاح من جلسة مؤقتة إذا كانت موجودة
$success = '';
$error = '';
if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

// عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // استقبال وتنظيف البيانات
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // تحقق من تطابق كلمة المرور
    if ($password !== $confirm_password) {
        $error = "كلمة المرور وتأكيدها غير متطابقين.";
    } else {
        // تحقق من عدم تكرار الاسم أو البريد
        $check = $conn->query("SELECT id FROM users WHERE username = '$username' OR email = '$email'");
        if ($check->num_rows > 0) {
            $error = "اسم المستخدم أو البريد الإلكتروني مستخدم مسبقاً.";
        } else {
            // تشفير كلمة المرور
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // تنفيذ الإدخال
            $insert = $conn->query("INSERT INTO users (full_name, username, email, password) 
                                    VALUES ('$full_name', '$username', '$email', '$hashed')");

            // إعادة التوجيه عند النجاح
            if ($insert) {
                $_SESSION['register_success'] = "تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.";
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "حدث خطأ أثناء إنشاء الحساب.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إنشاء حساب</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Times New Roman', Times, serif;
    }

    .register-box {
      max-width: 370px;
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
    .alert{
        text-align: center;
    }
  </style>
</head>
<body>

<div class="register-box">
  <!-- لوجو -->
  <div class="logo-box">
    <img src="logo.png" alt="Logo">
  </div>

  <!-- عنوان -->
  <h3>إنشاء حساب</h3>

  <!-- رسائل -->
  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <!-- النموذج -->
  <form method="POST">
    <div class="form-group">
      <label>الاسم الكامل</label>
      <input type="text" name="full_name" class="form-control" placeholder="ادخل اسمك الكامل" required>
    </div>
    <div class="form-group">
      <label>اسم المستخدم</label>
      <input type="text" name="username" class="form-control" placeholder="اختر اسم مستخدم" required>
    </div>
    <div class="form-group">
      <label>البريد الإلكتروني</label>
      <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
    </div>
    <div class="form-group">
      <label>كلمة المرور</label>
      <input type="password" name="password" class="form-control" placeholder="ادخل كلمة المرور" required>
    </div>
    <div class="form-group">
      <label>تأكيد كلمة المرور</label>
      <input type="password" name="confirm_password" class="form-control" placeholder="أعد كتابة كلمة المرور" required>
    </div>
    <button type="submit" class="btn btn-dark btn-block">تسجيل</button>
    <div class="text-center mt-3">
      لديك حساب؟ <a href="login.php">تسجيل الدخول</a>
    </div>
  </form>
</div>

</body>
</html>