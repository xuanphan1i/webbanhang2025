<?php
session_start();
require_once '../config/config.php';

// ✅ Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ✅ Kiểm tra có chọn sản phẩm không
if (!isset($_POST['chon_sp']) || count($_POST['chon_sp']) === 0) {
    echo "<script>alert('Bạn chưa chọn sản phẩm nào để đặt hàng!'); window.location.href='giohang.php';</script>";
    exit();
}

$ds_sp_chon = $_POST['chon_sp']; // danh sách id sản phẩm được chọn
$giohang = [];
$user_id = $_SESSION['user_id'];

// ✅ Lấy sản phẩm từ CSDL theo danh sách đã chọn
$ids = implode(',', array_map('intval', $ds_sp_chon)); // đảm bảo an toàn
$sql = "SELECT gh.san_pham_id AS id, gh.so_luong AS soluong, sp.ten, sp.gia, sp.hinh_anh 
        FROM gio_hang gh
        JOIN san_pham sp ON gh.san_pham_id = sp.id
        WHERE gh.user_id = $user_id AND gh.san_pham_id IN ($ids)";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $giohang[] = $row;
}

if (count($giohang) === 0) {
    echo "<script>alert('Không tìm thấy sản phẩm đã chọn!'); window.location.href='giohang.php';</script>";
    exit();
}

// ✅ Lấy dữ liệu từ form
$dia_chi = $_POST['dia_chi'];
$so_dien_thoai = $_POST['so_dien_thoai'];
$ghi_chu = $_POST['ghi_chu'];
$ngay_dat = date('Y-m-d H:i:s');

// ✅ Tính tổng tiền
$tong_tien = 0;
foreach ($giohang as $sp) {
    $tong_tien += $sp['gia'] * $sp['soluong'];
}

// ✅ Lưu đơn hàng
$sql = "INSERT INTO don_hang (khach_hang_id, tong_tien, ngay_dat, trang_thai, dia_chi, so_dien_thoai, ghi_chu)
        VALUES (?, ?, ?, 'cho_xac_nhan', ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("idssss", $user_id, $tong_tien, $ngay_dat, $dia_chi, $so_dien_thoai, $ghi_chu);
$stmt->execute();
$ma_don_hang = $stmt->insert_id;

// ✅ Lưu chi tiết đơn hàng
$sql_ct = "INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, gia) VALUES (?, ?, ?, ?)";
$stmt_ct = $conn->prepare($sql_ct);
foreach ($giohang as $sp) {
    $san_pham_id = (int)$sp['id'];
    $so_luong = (int)$sp['soluong'];
    $gia = (float)$sp['gia'];
    $stmt_ct->bind_param("iiid", $ma_don_hang, $san_pham_id, $so_luong, $gia);
    $stmt_ct->execute();
}

// ✅ Xóa sản phẩm đã đặt khỏi bảng gio_hang
$sql_del = "DELETE FROM gio_hang WHERE user_id = ? AND san_pham_id IN ($ids)";
$stmt_del = $conn->prepare($sql_del);
$stmt_del->bind_param("i", $user_id);
$stmt_del->execute();

// ✅ Thông báo và chuyển hướng
echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'donhang_cua_toi.php';</script>";
exit();
?>
