
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
        echo '<div class="danh-sach-don">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="don-container">';
            echo '<h3>Đơn hàng #' . $row['id'] . '</h3>';
            echo '<p><strong>Ngày đặt:</strong> ' . $row['ngay_dat'] . '</p>';
            echo '<p><strong>Tổng tiền:</strong> ' . number_format($row['tong_tien'], 0, ',', '.') . ' đ</p>';
            echo '<p><strong>Trạng thái:</strong> ' . htmlspecialchars($row['trang_thai']) . '</p>';
            echo '<p><strong>Địa chỉ:</strong> ' . htmlspecialchars($row['dia_chi']) . '</p>';
            echo '<p><strong>Số điện thoại:</strong> ' . htmlspecialchars($row['so_dien_thoai']) . '</p>';
            if (!empty($row['ghi_chu'])) {
                echo '<p><strong>Ghi chú:</strong> ' . htmlspecialchars($row['ghi_chu']) . '</p>';
            }
            echo '</div>';
        }
        echo '</div>';
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
            background-color: #e3b375;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            text-transform: uppercase;
            transition: background-color 0.3s ease;
            margin-left: 10px;
            margin-top: 0px;
    }
        
    .btn-quay-lai:hover {
            background-color: #ef7f94; /* đậm hơn khi hover */
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

</style>
</html>