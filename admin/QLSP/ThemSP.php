<?php
session_start();
require_once('../../config/config.php');
 // Kết nối cơ sở dữ liệu ($conn)

// Xử lý khi submit form
if (isset($_POST['them'])) {
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $gia = intval($_POST['gia']);
    $hinh_anh = mysqli_real_escape_string($conn, $_POST['hinh_anh']);
    $mo_ta = mysqli_real_escape_string($conn, $_POST['mo_ta']);
    $danhmuc = mysqli_real_escape_string($conn, $_POST['danhmuc']);

    $sql = "INSERT INTO san_pham (ten, gia, hinh_anh, mo_ta, danhmuc)
            VALUES ('$ten', $gia, '$hinh_anh', '$mo_ta', '$danhmuc')";

   if (mysqli_query($conn, $sql)) {
    echo "<script>
        alert('Thêm sản phẩm thành công!');
        window.location.href = '../QLSP.php';
    </script>";
    exit;
} else {
        echo "Lỗi thêm sản phẩm: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link rel="icon" type="image/png" href="../../public/assets/img/favicon/android-chrome-512x512.png">

</head>
<body>
    <!-- Giao diện thêm sản phẩm -->
        <div class="baoh2"><h2>Thêm Sản Phẩm</h2></div>
        <!-- Nút quay lại -->
    <div class="baoa"><a href="../../admin/QLSP.php" class="btn-quay-lai">Quay lại</a></div>
        <div class="container">
            <form method="POST">
            <label>Tên sản phẩm:</label><br>
            <input type="text" name="ten" required><br><br>

            <label>Giá:</label><br>
            <input type="number" name="gia" required><br><br>

            <label>Hình ảnh (đường dẫn):</label><br>
            <input type="text" name="hinh_anh" value="../../public/assets/img/main/sanpham/" required><br><br>

            <label>Mô tả:</label><br>
            <textarea name="mo_ta" rows="4"></textarea><br><br>

            <label>Danh mục:</label><br>
            <select name="danhmuc" required>
                <option value="">--Chọn danh mục--</option>
                <option value="rau">Rau</option>
                <option value="sinhto">Sinh Tố</option>
                <option value="thit">Thịt</option>
                <option value="traicay">Trái Cây</option>
            </select><br><br>

            <button type="submit" name="them">Thêm sản phẩm</button>
            <a href="../QLSP.php"><button type="button">Hủy</button></a>

        </form>
        </div>
</body>
<style>
    .container {
    padding: 40px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
     background-color: #d4e1b3; 


}
.baoh2{
    top: 10px;
            left: 0;
            width: 100%;
            padding: 30px 0;
            background-image: url(../../public/assets/img/main/gioiThieu/a1.png);
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            text-transform: uppercase;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.baoa{
     background-color: #d4e1b3; 

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
            margin-top: 20px;
        }
        
        .btn-quay-lai:hover {
            background-color: #ef7f94; /* đậm hơn khi hover */
        }

form {
    background-color: #ffffff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 500px;
}

button{
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
        button:hover{
            background-color: #ef7f94; /* đậm hơn khi hover */

        }

input[type="text"],
input[type="number"],
textarea,
select {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 16px;
}

</style>
</html>

