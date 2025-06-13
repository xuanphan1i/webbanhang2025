<?php
session_start();

if (isset($_POST['them_gio'])) {
    $sp = [
        'id' => $_POST['id'],
        'ten' => $_POST['ten'],
        'gia' => $_POST['gia'],
        'hinh_anh' => $_POST['hinh_anh'],
        'soluong' => $_POST['soluong']
    ];

    if (!isset($_SESSION['giohang'])) {
        $_SESSION['giohang'] = [];
    }

    $_SESSION['giohang'][] = $sp;

    // Chuyển hướng lại trang giỏ hàng kèm thông báo
    header("Location: giohang.php?success=1");
    exit;
}

// XỬ LÝ THÊM SẢN PHẨM
if (isset($_POST['them_gio'])) {
    $sp = [
        'id' => $_POST['id'],
        'ten' => $_POST['ten'],
        'gia' => $_POST['gia'],
        'hinh' => $_POST['hinh'],
        'soluong' => $_POST['soluong']
    ];

    if (!isset($_SESSION['giohang'])) {
        $_SESSION['giohang'] = [];
    }

    $_SESSION['giohang'][] = $sp;
}

// XỬ LÝ XÓA SẢN PHẨM
if (isset($_GET['xoa'])) {
    $index = $_GET['xoa'];
    unset($_SESSION['giohang'][$index]);
    $_SESSION['giohang'] = array_values($_SESSION['giohang']); // Sắp xếp lại key
}

// XỬ LÝ CẬP NHẬT SỐ LƯỢNG
if (isset($_POST['capnhat'])) {
    foreach ($_POST['soluong'] as $index => $so_luong_moi) {
        $_SESSION['giohang'][$index]['soluong'] = $so_luong_moi;
    }
}

// HIỂN THỊ GIAO DIỆN
?>
<?php require '../includes/T11header.php' ?>
<!-- ✅ Đặt đoạn thông báo tại đây -->
<?php if (isset($_GET['success'])): ?>
  <div style="color: green; font-weight: bold;">✅ Sản phẩm đã được thêm vào giỏ hàng!</div>
<?php endif; ?>
<h2>Giỏ hàng của bạn</h2>
<form method="POST">
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>STT</th>
        <th>Hình ảnh</th>
        <th>Tên</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Tổng</th>
        <th>Xóa</th>
    </tr>

    <?php
    $tong = 0;
    if (!empty($_SESSION['giohang'])) {
        foreach ($_SESSION['giohang'] as $index => $sp) {
            $tien = $sp['gia'] * $sp['soluong'];
            $tong += $tien;
            echo "<tr>
                <td>".($index+1)."</td>
                <td><img src='images/{$sp['hinh']}' width='50'></td>
                <td>{$sp['ten']}</td>
                <td>{$sp['gia']}</td>
                <td>
                    <input type='number' name='soluong[$index]' value='{$sp['soluong']}' min='1'>
                </td>
                <td>$tien</td>
                <td><a href='giohang.php?xoa=$index'>Xóa</a></td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>Giỏ hàng trống</td></tr>";
    }
    ?>
</table>

<br>
<button type="submit" name="capnhat">Cập nhật số lượng</button>
<a href="dathang.php"><button type="button">Đặt hàng</button></a>
</form>
