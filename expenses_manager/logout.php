<?php
session_start();

// حذف كل متغيرات الجلسة
$_SESSION = [];

// حذف الكوكي الخاصة بالجلسة (اختياري لكن مهم)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// تدمير الجلسة
session_destroy();

// تحويل إلى login
header("Location: dashboard.php");
exit;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الخروج</title>
  <meta http-equiv="refresh" content="3;url=login.php"> <!-- إعادة توجيه تلقائية بعد 3 ثواني -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Times New Roman', Times, serif;
      text-align: center;
      padding-top: 100px;
    }

    .logout-box {
      max-width: 500px;
      margin: auto;
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .alert {
      font-size: 1.2rem;
    }

    .small-note {
      font-size: 0.9rem;
      margin-top: 15px;
      color: #555;
    }
  </style>
</head>
<body>

<div class="logout-box">
  <div class="alert alert-info">
    تم تسجيل الخروج بنجاح. سيتم تحويلك إلى صفحة تسجيل الدخول...
  </div>
  <div class="small-note">
    إذا لم يتم التحويل تلقائيًا، <a href="login.php">اضغط هنا</a>.
  </div>
</div>

</body>
</html>