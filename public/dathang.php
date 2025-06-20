<?php
session_start();

// Kiểm tra giỏ hàng
if (!isset($_SESSION['giohang']) || count($_SESSION['giohang']) === 0) {
    echo "<script>alert('Giỏ hàng trống! Vui lòng chọn sản phẩm.'); window.location.href='trangchu.php';</script>";
    exit();
}

$giohang = $_SESSION['giohang'];

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng</title>
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">
    <!-- favicon -->
    <script
    defer
    src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"
    integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc"
    crossorigin="anonymous"
  ></script>
     <!-- favicon end -->
    <style>
        body {
            font-family: Arial, sans-serif;
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
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 50px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        form {
            width: 60%;
            margin: auto;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        .dat-hang-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>


    <div class="h22"><h2>Xác nhận đặt hàng </h2></div>

<div class="baoa">
    <a href="javascript:history.back()" class="btn-quay-lai">Quay lại</a>
   
</div>

    <table>
        <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $tong_tien = 0;
            foreach ($giohang as $sp) {
                $thanh_tien = $sp['gia'] * $sp['soluong'];
                $tong_tien += $thanh_tien;
                echo "<tr>
                    <td><img src='{$sp['hinh_anh']}' width='60'></td>
                    <td>{$sp['ten']}</td>
                    <td>" . number_format($sp['gia']) . " đ</td>
                    <td>{$sp['soluong']}</td>
                    <td>" . number_format($thanh_tien) . " đ</td>
                </tr>";
            }
            ?>
            <tr>
                <td colspan="4"><strong>Tổng cộng</strong></td>
                <td><strong><?= number_format($tong_tien) ?> đ</strong></td>
            </tr>
        </tbody>
    </table>

    <form action="xulydathang.php" method="post">
        <h2>Thông tin giao hàng</h2>

        <label for="dia_chi">Địa chỉ giao hàng <span style="color:red">*</span></label>
        <input type="text" name="dia_chi" id="dia_chi" required>

        <label for="so_dien_thoai">Số điện thoại <span style="color:red">*</span></label>
        <input type="text" name="so_dien_thoai" id="so_dien_thoai" required>

        <label for="ghi_chu">Ghi chú đơn hàng</label>
        <textarea name="ghi_chu" id="ghi_chu" rows="4" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến..."></textarea>

        <button type="submit" class="dat-hang-btn btn-quay-lai">Xác nhận đặt hàng</button>
    </form>
</body>
</html>
