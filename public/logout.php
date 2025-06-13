<?php
session_start(); 
// Bắt đầu session hoặc tiếp tục session hiện tại để có thể thao tác

session_destroy(); 
// Xóa tất cả dữ liệu session trên server

if (ini_get("session.use_cookies")) { 
    // Nếu session được lưu trong cookie
    $params = session_get_cookie_params(); 
    // Lấy thông tin cookie hiện tại
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    ); 
    // Xóa cookie session trên trình duyệt (bằng cách set thời gian hết hạn trong quá khứ)
}

echo "<script>
    alert('Đăng xuất thành công!');
    window.location.href = 'login.php';
</script>";
exit;

?>
