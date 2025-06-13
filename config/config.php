<?php
$host = "localhost";
$user = "root";
$password = ""; // Mặc định của XAMPP
$dbname = "webbanhang_2025_xuan"; // Đổi thành tên thật của bạn

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// else {
//     echo "Kết nối thành công!";
// }
?>
