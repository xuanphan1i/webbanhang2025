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
    sp.ten AS ten_san_pham,
    sp.id AS san_pham_id,
    sp.gia AS gia_goc,
    sp.hinh_anh, -- ✅ thêm dòng này
    sp.mo_ta,
    ct.so_luong,
    ct.gia AS gia_mua
FROM don_hang dh
JOIN chi_tiet_don_hang ct ON dh.id = ct.don_hang_id
JOIN san_pham sp ON ct.san_pham_id = sp.id
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

<table>
    <thead>
    <tr style="color: #fff;">
        <th>ID đơn</th>
        <th>Tên khách hàng</th>
        <th>ID khách hàng</th>
        <th>Thông tin đơn</th> <!-- 👈 đã gộp -->
        <th>Trạng thái</th>
        <th>Chi tiết đơn</th>
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
                'ten_khach_hang' => $row['ten_khach_hang'],
                'khach_hang_id' => $row['khach_hang_id'],
                'ngay_dat' => $row['ngay_dat'],
                'trang_thai' => $row['trang_thai'],
                'dia_chi' => $row['dia_chi'],
                'so_dien_thoai' => $row['so_dien_thoai'],
                'ghi_chu' => $row['ghi_chu'],
                'tong_tien' => 0,
                'san_phams' => []
            ];
        }

        $thanh_tien = $row['gia_mua'] * $row['so_luong'];
        $don_hangs[$id]['tong_tien'] += $thanh_tien;

        $don_hangs[$id]['san_phams'][] = [
            'ten' => $row['ten_san_pham'],
            'hinh_anh' => $row['hinh_anh'],
            'gia' => $row['gia_mua'],
            'so_luong' => $row['so_luong'],
            'thanh_tien' => $thanh_tien
        ];
    }

    foreach ($don_hangs as $don) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($don['id']) . "</td>";
        echo "<td>" . htmlspecialchars($don['ten_khach_hang']) . "</td>";
        echo "<td>" . htmlspecialchars($don['khach_hang_id']) . "</td>";
        echo "<td>";
        echo "<strong>Ngày đặt:</strong> " . htmlspecialchars($don['ngay_dat']) . "<br>";
        echo "<strong>Địa chỉ:</strong> " . htmlspecialchars($don['dia_chi']) . "<br>";
        echo "<strong>SĐT:</strong> " . htmlspecialchars($don['so_dien_thoai']) . "<br>";
        echo "<strong>Ghi chú:</strong> " . htmlspecialchars($don['ghi_chu']);
        echo "</td>";

        $trang_thai_mapping = [
            'cho_xac_nhan' => ['text' => 'Chờ xác nhận', 'color' => '#fff3cd'],
            'dang_giao'    => ['text' => 'Đang giao',    'color' => '#bee5eb'],
            'da_giao'      => ['text' => 'Đã giao',      'color' => '#c3e6cb'],
        ];
        $tt = $don['trang_thai'];
        $text = $trang_thai_mapping[$tt]['text'] ?? 'Không rõ';
        $color = $trang_thai_mapping[$tt]['color'] ?? '#f8d7da';
        echo "<td style='background-color: $color; font-weight: bold; border-radius: 5px;'>$text</td>";

        // Chi tiết đơn
        echo "<td>";
        echo "<table style='border-collapse: collapse; width: 100%; font-size: 13px;'>";
        echo "<thead><tr>
                <th>STT</th>
                <th>Tên SP</th>
                <th>Hình</th>
                <th>Giá</th>
                <th>SL</th>
                <th>Thành tiền</th>
              </tr></thead><tbody>";

        $stt = 1;
        foreach ($don['san_phams'] as $sp) {
            echo "<tr>";
            echo "<td>$stt</td>";
            echo "<td>" . htmlspecialchars($sp['ten']) . "</td>";
            echo "<td><img src='" . htmlspecialchars($sp['hinh_anh']) . "' width='40'></td>";
            echo "<td>" . number_format($sp['gia'], 0, ',', '.') . " đ</td>";
            echo "<td>" . $sp['so_luong'] . "</td>";
            echo "<td>" . number_format($sp['thanh_tien'], 0, ',', '.') . " đ</td>";
            echo "</tr>";
            $stt++;
        }

        echo "<tr>
                <td colspan='5' style='text-align:right; font-weight:bold;'>Tổng cộng:</td>
                <td><strong>" . number_format($don['tong_tien'], 0, ',', '.') . " đ</strong></td>
              </tr>";

        echo "</tbody></table>";
        echo "</td>";

        echo "<td>
            <div style='display: inline-flex; gap: 5px;'>
                <a href='QLDon.php?action=sua&id=" . $don['id'] . "' class='btn-sua'>Sửa</a>
                <a href='QLDon.php?action=xoa&id=" . $don['id'] . "' class='btn-xoa' onclick=\"return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');\">Xóa</a>
            </div>
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
        <h3 style="color: #ccc;
            color: #222;  font-family: 'Segoe UI', 'Roboto', sans-serif; text-align: center;">Sửa đơn hàng ID: <?= htmlspecialchars($don_hang_sua['id']) ?></h3>

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
            <button type="submit" name="sua_don_hang" class="btn-sua2">Sửa</button>

            <!-- Đây là nút QUAY LẠI -->
            <a href="QLDon.php" style="margin-left: 10px; color: white; background-color: red; padding: 5px 10px; text-decoration: none;" class="btn-huy2">Hủy</a>
        </form>
    </div>
</div>
<?php endif; ?>
 


<div style="height: 500px;" class="cuoi"></div>
</body>

<style>
    textarea {
  font-family: Arial, sans-serif;
  font-size: 14px;
}
body{
        font-family: Arial, Helvetica, sans-serif;

    }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
    th { background: #2e7d32; }
    .form-sua {
        margin-top: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            background:rgb(255, 255, 255);
            width: 400px;
            padding: 20px;
            border: 2px solid khaki;           /* Viền xám nhạt */
            border-radius: 10px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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

    label{
            color: #ccc;
            color: #222;              /* Đen nhẹ */
            font-weight: 600;
            font-size: 17px;          /* To rõ */
            display: block;
            margin-bottom: 8px;
            font-family: 'Segoe UI', 'Roboto', sans-serif;
        }
        input{
            width: 100%;
            padding: 12px 16px;              /* to hơn, rộng rãi */
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #fff;
            color: #333;
            font-family: 'Segoe UI', 'Roboto', sans-serif;
        }
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
    background-color: white;           /* nền trắng */
    color: #ff9800;                    /* chữ cam */
    border: 2px solid #ff9800;         /* viền cam */
    border-radius: 6px;
    font-weight: bold;
    text-transform: uppercase;
    text-decoration: none;
    transition: all 0.3s ease;

    margin-left: 10px;
    margin-top: 20px;

    /* ✅ Không bị ảnh hưởng bởi float bên cạnh */
    clear: both;
}

.btn-quay-lai:hover {
    background-color: #ff9800;         /* hover nền cam */
    color: white;                      /* chữ trắng */
}
.baoa {
    margin-top: 20px; /* hoặc 30-40 nếu bạn muốn khoảng cách lớn hơn */
}

    .overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
    }
    /* Nút Sửa: nền trắng, viền đỏ, chữ đỏ */
.btn-sua2 {
    padding: 10px 20px;
    background-color: white;
    color: #d32f2f;
    border: 2px solid #d32f2f;
    border-radius: 6px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-sua2:hover {
    background-color: #ffecec;
    color: #b71c1c;
}

/* Nút Hủy: nền đỏ, chữ trắng, không viền */
.btn-huy2 {
    padding: 10px 20px;
    background-color: #d32f2f;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-huy2:hover {
    background-color: #b71c1c;
}

.btn-sua {
    background-color: #4CAF50; /* xanh lá */
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
}

.btn-xoa {
    background-color: #f44336; /* đỏ */
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
}

.btn-sua:hover {
    background-color: #45a049;
}

.btn-xoa:hover {
    background-color: #d32f2f;
}
</style>
</html>
