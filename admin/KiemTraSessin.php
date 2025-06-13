<?php
session_start();
// Kiểm tra nếu chưa đăng nhập admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Chuyển hướng về trang đăng nhập
    header(' ../public/login.php'); //đã sửa ở đây:  ../public/login.php ban đầu là  header('Location: login.php');
    exit();
}
?>
 