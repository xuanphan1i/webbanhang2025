<?php
session_start();
require_once '../config/config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../public/login.php");
    exit;
}


// Xử lý xóa
if (isset($_GET['action']) && $_GET['action'] === 'xoa' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM don_hang WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('Đã xóa đơn hàng!'); window.location.href='QLD.php';</script>";
    exit;
}

$don_hang_sua = null;


// Xử lý sửa
if (isset($_GET['action']) && $_GET['action'] === 'sua' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM don_hang WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $don_hang_sua = $stmt->get_result()->fetch_assoc();
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sua_don_hang'])) {
    $id = intval($_POST['id']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $dia_chi = trim($_POST['dia_chi']);
    $trang_thai = trim($_POST['trang_thai']);
    $ghi_chu = trim($_POST['ghi_chu']);

    $stmt = $conn->prepare("UPDATE don_hang SET so_dien_thoai = ?, dia_chi = ?, trang_thai = ?, ghi_chu = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $so_dien_thoai, $dia_chi, $trang_thai, $ghi_chu, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Sửa đơn hàng thành công!'); window.location.href='QLDon.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi sửa.'); window.history.back();</script>";
    }
    exit;
}


$sql = "
    SELECT 
        dh.id AS don_hang_id,
        nd.ten AS ten_khach_hang,
        dh.khach_hang_id,
        dh.tong_tien,
        dh.ngay_dat,
        dh.trang_thai,
        dh.dia_chi,
        dh.so_dien_thoai,
        dh.ghi_chu,
        ct.san_pham_id,
        ct.so_luong,
        ct.gia
    FROM don_hang dh
    JOIN chi_tiet_don_hang ct ON dh.id = ct.don_hang_id
    JOIN nguoi_dung nd ON dh.khach_hang_id = nd.id
    ORDER BY dh.id ASC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png" />
</head>
<body>
<div class="h22"><h2>Danh sách đơn hàng</h2></div>
<div class="baoa"><a href="index.php" class="btn-quay-lai">Quay lại</a></div>

<table style="background-color: #91ad41; color: #ffff;">
   <thead>
    <tr>
        <th>ID</th>
         <th>Tên khách hàng</th> <!-- 👈 thêm dòng này -->
        <th>Mã khách hàng</th>
        <th>Tổng tiền</th>
        <th>Ngày đặt</th>
        <th>Trạng thái</th>
        <th>Địa chỉ</th>
        <th>Số điện thoại</th>
        <th>Ghi chú</th>
        <th>Sản phẩm</th> <!-- THÊM DÒNG NÀY -->
        <th>Thao tác</th>
    </tr>
</thead>


    <tbody>
<?php
if ($result->num_rows > 0) {
    $don_hangs = [];

    while ($row = $result->fetch_assoc()) {
        $id = $row['don_hang_id'];

        if (!isset($don_hangs[$id])) {
            $don_hangs[$id] = [
                'id' => $id,
                'ten_khach_hang' => $row['ten_khach_hang'], // 👈 thêm dòng này
                'khach_hang_id' => $row['khach_hang_id'],
                'tong_tien' => $row['tong_tien'],
                'ngay_dat' => $row['ngay_dat'],
                'trang_thai' => $row['trang_thai'],
                'dia_chi' => $row['dia_chi'],
                'so_dien_thoai' => $row['so_dien_thoai'],
                'ghi_chu' => $row['ghi_chu'],
                'san_phams' => []
            ];
        }

        $don_hangs[$id]['san_phams'][] = "SP ID: " . $row['san_pham_id'] . " (sl:" . $row['so_luong'] . ")";

    }

    foreach ($don_hangs as $don) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($don['id']) . "</td>";
        echo "<td>" . htmlspecialchars($don['ten_khach_hang']) . "</td>"; // 👈 sau dòng in ID

        echo "<td>" . htmlspecialchars($don['khach_hang_id']) . "</td>";
        echo "<td>" . htmlspecialchars($don['tong_tien']) . "</td>";
        echo "<td>" . htmlspecialchars($don['ngay_dat']) . "</td>";
        echo "<td>" . htmlspecialchars($don['trang_thai']) . "</td>";
        echo "<td>" . htmlspecialchars($don['dia_chi']) . "</td>";
        echo "<td>" . htmlspecialchars($don['so_dien_thoai']) . "</td>";
        echo "<td>" . htmlspecialchars($don['ghi_chu']) . "</td>";
        echo "<td>" . implode(", ", $don['san_phams']) . "</td>"; // Sản phẩm gộp
        echo "<td>
                <a href='QLDon.php?action=sua&id=" . $don['id'] . "'>Sửa</a> |
                <a href='QLDon.php?action=xoa&id=" . $don['id'] . "' onclick=\"return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');\">Xóa</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>Chưa có đơn hàng nào.</td></tr>";
}
?>
</tbody>

</table>

<?php if ($don_hang_sua): ?>
<div class="overlay">
    <div class="form-sua">
        <h3 style="color: #ffff;">Sửa đơn hàng ID: <?= htmlspecialchars($don_hang_sua['id']) ?></h3>

        <form method="POST" action="QLDon.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($don_hang_sua['id']) ?>">

            <label>Số điện thoại:</label><br>
            <input type="text" name="so_dien_thoai" pattern="[0-9]{10}" maxlength="10"
                   value="<?= htmlspecialchars($don_hang_sua['so_dien_thoai']) ?>" required><br><br>

            <label>Địa chỉ:</label><br>
            <input type="text" name="dia_chi" value="<?= htmlspecialchars($don_hang_sua['dia_chi']) ?>" required><br><br>

            <label>Trạng thái đơn hàng:</label><br>
            <select name="trang_thai" required>
                <option value="cho_xac_nhan" <?= $don_hang_sua['trang_thai'] == 'cho_xac_nhan' ? 'selected' : '' ?>>Chờ xác nhận</option>
                <option value="dang_giao" <?= $don_hang_sua['trang_thai'] == 'dang_giao' ? 'selected' : '' ?>>Đang giao</option>
                <option value="da_giao" <?= $don_hang_sua['trang_thai'] == 'da_giao' ? 'selected' : '' ?>>Đã giao</option>
            </select><br><br>

            <label>Ghi chú:</label><br>
            <textarea name="ghi_chu" rows="3" style="width: 100%;"><?= htmlspecialchars($don_hang_sua['ghi_chu']) ?></textarea><br><br>

            <!-- Đây là nút GỬI FORM -->
            <button type="submit" name="sua_don_hang">Sửa</button>

            <!-- Đây là nút QUAY LẠI -->
            <a href="QLDon.php" style="margin-left: 10px; color: white; background-color: red; padding: 5px 10px; text-decoration: none;">Hủy</a>
        </form>
    </div>
</div>
<?php endif; ?>



<div style="height: 500px;" class="cuoi"></div>
</body>

<style>
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
    th { background: #2e7d32; }
    .form-sua {
        margin-top: 20px;
        padding: 15px;
        border: 1px solid #ccc;
        background: #59cca6;
        width: 400px;
    }
    .h22 {
        padding: 40px 0;
        background-image: url(../public/assets/img/main/gioiThieu/a1.png);
        background-size: cover;
        background-position: center;
        display: flex;
        justify-content: center;
        align-items: center;
        text-transform: uppercase;
        z-index: 999;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    h2 {
        width: 500px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #ffff;
        border: 2px solid #ef7f94;
        border-radius: 20px;
        margin: 0;
        padding: 20px;
        color: #ef7f94;
    }
    label { color: #ffff; }
    button {
        padding: 10px 20px;
        background-color: #e3b375;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        text-transform: uppercase;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-right: 10px;
    }
    button:hover { background-color: #ef7f94; }
    .btn-quay-lai {
        display: inline-block;
        padding: 10px 20px;
        background-color: #e3b375;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        text-transform: uppercase;
        transition: background-color 0.3s ease;
        margin-left: 10px;
        margin-top: 70px;
    }
    .btn-quay-lai:hover { background-color: #ef7f94; }
    .baoa { margin-top: -50px; }
    .overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
    }
</style>
</html>
