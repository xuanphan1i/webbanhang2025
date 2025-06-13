<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Trở lại trang login ở public
    header("Location: /webbanhang/public/login.php");
    exit();
}
