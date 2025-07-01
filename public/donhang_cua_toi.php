
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi</title>
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">

</head>
<body>
    <?php require '../includes/T11header.php'; ?>
    <div class="h22"><h2>Đơn hàng của tôi</h2></div>

    <div class="baoa">
        <a href="javascript:history.back()" class="btn-quay-lai">Quay lại</a>
    </div>
    <?php
require_once '../config/config.php';

if (isset($_SESSION['user_id'])) {
    $khach_hang_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM don_hang WHERE khach_hang_id = ? ORDER BY ngay_dat DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $khach_hang_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      echo '<table class="bang-don">';
echo '<thead>
        <tr>
            <th>Mã đơn</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <th>Ghi chú</th>
        </tr>
      </thead>';
echo '<tbody>';
$trang_thai_mau = [
    'cho_xac_nhan' => ['text' => 'Chờ xác nhận', 'color' => '#fff3cd'], // vàng nhạt
    'dang_giao'    => ['text' => 'Đang giao',    'color' => '#bee5eb'], // xanh dương nhạt
    'da_giao'      => ['text' => 'Đã giao',      'color' => '#c3e6cb'], // xanh lá nhạt
    'da_huy'       => ['text' => 'Đã hủy',       'color' => '#f8d7da'], // đỏ nhạt (thêm nếu có)
];

while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>#' . $row['id'] . '</td>';
    echo '<td>' . $row['ngay_dat'] . '</td>';
    echo '<td>' . number_format($row['tong_tien'], 0, ',', '.') . ' đ</td>';
    $trang_thai = $row['trang_thai'];
$text = isset($trang_thai_mau[$trang_thai]) ? $trang_thai_mau[$trang_thai]['text'] : ucfirst($trang_thai);
$color = isset($trang_thai_mau[$trang_thai]) ? $trang_thai_mau[$trang_thai]['color'] : '#f0f0f0';

echo '<td style="background-color: ' . $color . '; font-weight: bold;">' . htmlspecialchars($text) . '</td>';

    echo '<td>' . htmlspecialchars($row['dia_chi']) . '</td>';
    echo '<td>' . htmlspecialchars($row['so_dien_thoai']) . '</td>';
    echo '<td>' . (!empty($row['ghi_chu']) ? htmlspecialchars($row['ghi_chu']) : '-') . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

    } else {
        echo '<p style="margin-left: 10px;">Bạn chưa có đơn hàng nào.</p>';
    }

    $stmt->close();
} else {
    echo '<p style="margin-left: 10px;">Vui lòng đăng nhập để xem đơn hàng của bạn.</p>';
}
?>


    <?php require '../includes/footer.php' ?>
</body>
<style>
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

    .h22 h2{
            width: 500px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffff;
            border: 2px solid #ef7f94;
            border-radius: 20px;
            margin: 0;
            padding: 20px;
            color: #ef7f94 ;

    }
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
    margin-top: 0px;
}

.btn-quay-lai:hover {
    background-color: #ff9800;         /* khi hover: nền cam */
    color: white;                      /* chữ trắng */
}
        .danh-sach-don {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.don-container {
    border: 2px solid #ef7f94;
    border-radius: 12px;
    padding: 20px;
    background-color: #fff5f7;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.don-container h3 {
    margin-top: 0;
    color: #ef7f94;
}
.bang-don {
    width: 95%;
    border-collapse: collapse;
    margin: 20px auto;
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
}

.bang-don th, .bang-don td {
    padding: 12px 16px;
    border: 1px solid #ccc; /* xám nhạt để phân biệt */
    
    text-align: left;
}


.bang-don thead {
    background-color: #ef7f94;
    color: white;
}

.bang-don tbody tr:nth-child(even) {
    background-color: #fff5f7;
}

</style>
</html>