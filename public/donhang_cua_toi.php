
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

    $sql = "SELECT dh.*, nd.ten AS ten_nguoi_nhan
        FROM don_hang dh
        JOIN nguoi_dung nd ON dh.khach_hang_id = nd.id
        WHERE khach_hang_id = ?
        ORDER BY ngay_dat DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $khach_hang_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table class="bang-don">';
        echo '<thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Đơn hàng</th>
                    <th>Thông tin giao hàng</th>
                    <th>Trạng thái</th>
                </tr>
              </thead>';
        echo '<tbody>';

        $trang_thai_mau = [
            'cho_xac_nhan' => ['text' => 'Chờ xác nhận', 'color' => '#fff3cd'],
            'dang_giao'    => ['text' => 'Đang giao',    'color' => '#bee5eb'],
            'da_giao'      => ['text' => 'Đã giao',      'color' => '#c3e6cb'],
            'da_huy'       => ['text' => 'Đã hủy',       'color' => '#f8d7da'],
        ];

        while ($row = $result->fetch_assoc()) {
            $ma_don = $row['id'];
            $ngay_dat = $row['ngay_dat'];
            $trang_thai = $row['trang_thai'];
            $dia_chi = htmlspecialchars($row['dia_chi']);
            $sdt = htmlspecialchars($row['so_dien_thoai']);
            $ghi_chu = !empty($row['ghi_chu']) ? htmlspecialchars($row['ghi_chu']) : '-';

            // Lấy chi tiết đơn hàng
            $sql_ct = "SELECT ctdh.*, sp.ten, sp.hinh_anh 
                       FROM chi_tiet_don_hang ctdh 
                       JOIN san_pham sp ON ctdh.san_pham_id = sp.id 
                       WHERE ctdh.don_hang_id = ?";
            $stmt_ct = $conn->prepare($sql_ct);
            $stmt_ct->bind_param("i", $ma_don);
            $stmt_ct->execute();
            $chi_tiet = $stmt_ct->get_result();

            echo '<tr>';

            // Cột 1: Mã đơn
            echo '<td>#' . $ma_don . '</td>';

            // Cột 2: Danh sách sản phẩm
            echo '<td>';
            echo '<table class="bang-sp">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên sản phẩm</th>
                            <th>Hình ảnh</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>';
            $tong_cong = 0;
            $stt = 1;

            while ($ct = $chi_tiet->fetch_assoc()) {
                $thanh_tien = $ct['gia'] * $ct['so_luong'];
                $tong_cong += $thanh_tien;

                echo '<tr>
                        <td>' . $stt++ . '</td>
                        <td>' . htmlspecialchars($ct['ten']) . '</td>
                        <td><img src="' . $ct['hinh_anh'] . '" width="50"></td>
                        <td>' . number_format($ct['gia'], 0, ',', '.') . ' đ</td>
                        <td>' . $ct['so_luong'] . '</td>
                        <td>' . number_format($thanh_tien, 0, ',', '.') . ' đ</td>
                      </tr>';
            }

            // Tổng cộng (sửa colspan thành 5 vì có 6 cột)
            echo '<tr>
                    <td colspan="5" style="text-align:right;"><strong>Tổng cộng:</strong></td>
                    <td><strong>' . number_format($tong_cong, 0, ',', '.') . ' đ</strong></td>
                  </tr>';

            echo '</tbody></table>';
            echo '</td>';

            // Cột 3: Thông tin giao hàng
            echo '<td class="tt-giao-hang">';
            echo '<div><strong>Người nhận:</strong> ' . htmlspecialchars($row['ten_nguoi_nhan']) . '</div>';
            echo '<div><strong>Địa chỉ:</strong> ' . $dia_chi . '</div>';
            echo '<div><strong>SĐT:</strong> ' . $sdt . '</div>';
            echo '<div><strong>Ghi chú:</strong> ' . $ghi_chu . '</div>';
            echo '<div><strong>Ngày đặt:</strong> ' . $ngay_dat . '</div>';
            echo '</td>';



            // Cột 4: Trạng thái
            $text = isset($trang_thai_mau[$trang_thai]) ? $trang_thai_mau[$trang_thai]['text'] : ucfirst($trang_thai);
            $color = isset($trang_thai_mau[$trang_thai]) ? $trang_thai_mau[$trang_thai]['color'] : '#f0f0f0';
            echo '<td class="trang-thai" style="background-color:' . $color . ';">' . $text . '</td>';

            echo '</tr>';

            $stmt_ct->close();
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
.bang-sp thead {
    background-color: #f0f0f0; /* xám nhạt */
    color: black;
    text-align: center;
}
.tt-giao-hang {
    line-height: 1.6;
    padding: 12px 16px;
    background-color: #fffdfd;
    border: 1px solid #f3c2d8;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    font-size: 14px;
    color: #333;
    width: 220px; /* hoặc max-width nếu cần giới hạn */
}

.tt-giao-hang div {
    margin-bottom: 6px;
}

</style>
</html>