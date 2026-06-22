<?php
$host = 'localhost';
$dbname = 'expenses_manager';
$username = 'root';
$password = ''; // إذا عندك كلمة مرور غيرها هنا

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error);
}

$conn->set_charset("utf8");
?>