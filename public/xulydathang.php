<?php
session_start();
require_once '../config/config.php';

// ✅ Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ✅ Kiểm tra giỏ hàng
if (!isset($_SESSION['giohang']) || empty($_SESSION['giohang'])) {
    echo "Giỏ hàng trống!";
    exit();
}

// ✅ Lấy dữ liệu từ form
$dia_chi = $_POST['dia_chi'];
$so_dien_thoai = $_POST['so_dien_thoai'];
$ghi_chu = $_POST['ghi_chu'];
$ma_nguoi_dung = $_SESSION['user_id'];
$ngay_dat = date('Y-m-d H:i:s');

// ✅ Tính tổng tiền
$tong_tien = 0;
foreach ($_SESSION['giohang'] as $sp) {
    $tong_tien += $sp['gia'] * $sp['soluong'];
}

// ✅ Lưu đơn hàng vào bảng don_hang
$sql = "INSERT INTO don_hang (khach_hang_id, tong_tien, ngay_dat, trang_thai, dia_chi, so_dien_thoai, ghi_chu)
        VALUES (?, ?, ?, 'cho_xac_nhan', ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("idssss", $ma_nguoi_dung, $tong_tien, $ngay_dat, $dia_chi, $so_dien_thoai, $ghi_chu);
$stmt->execute();
$ma_don_hang = $stmt->insert_id;

// ✅ Lưu từng sản phẩm vào bảng chi_tiet_don_hang
$sql_ct = "INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, gia) VALUES (?, ?, ?, ?)";
$stmt_ct = $conn->prepare($sql_ct);

foreach ($_SESSION['giohang'] as $sp) {
    $san_pham_id = (int)$sp['id'];
    $so_luong = (int)$sp['soluong'];
    $gia = (float)$sp['gia'];
    
    $stmt_ct->bind_param("iiid", $ma_don_hang, $san_pham_id, $so_luong, $gia);
    $stmt_ct->execute();
}

// ✅ Xóa giỏ hàng
unset($_SESSION['giohang']);

// ✅ Thông báo và chuyển hướng
echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'donhang_cua_toi.php';</script>";
exit();
?>
