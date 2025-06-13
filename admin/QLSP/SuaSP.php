<?php
session_start();
require_once('../../config/config.php');


// Lấy ID sản phẩm từ URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM san_pham WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $sp = mysqli_fetch_assoc($result);
}
// Lấy dữ liệu sản phẩm từ DB
$sql = "SELECT * FROM san_pham WHERE id = $id";
$result = mysqli_query($conn, $sql);
$sp = mysqli_fetch_assoc($result);

// Xử lý khi người dùng submit form sửa
if (isset($_POST['capnhat'])) {
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $gia = intval($_POST['gia']);
    $hinh_anh = mysqli_real_escape_string($conn, $_POST['hinh_anh']);
    $mo_ta = mysqli_real_escape_string($conn, $_POST['mo_ta']);
    $danhmuc = mysqli_real_escape_string($conn, $_POST['danhmuc']);

    $sqlUpdate = "UPDATE san_pham 
                  SET ten='$ten', gia=$gia, hinh_anh='$hinh_anh', mo_ta='$mo_ta', danhmuc='$danhmuc' 
                  WHERE id = $id";

    if (mysqli_query($conn, $sqlUpdate)) {
        echo "<script>alert('Cập nhật sản phẩm thành công!'); window.location.href = '../QLSP.php';</script>";
        exit;
    } else {
        echo "Lỗi cập nhật: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <link rel="icon" type="image/png" href="../../public/assets/img/favicon/android-chrome-512x512.png">

</head>
<body>
    <!-- Giao diện form sửa -->
    <div class="baoh2"><h2>Sửa Sản Phẩm</h2></div>
     <!-- Nút quay lại -->
    <div class="baoa"><a href="../../admin/QLSP.php" class="btn-quay-lai">Quay lại</a></div>

    <?php if ($sp): ?>
        <div class="container">
             <form method="POST">
                <label>Tên sản phẩm:</label><br>
                <input type="text" name="ten" value="<?= htmlspecialchars($sp['ten']) ?>" required><br><br>

                <label>Giá:</label><br>
                <input type="number" name="gia" value="<?= $sp['gia'] ?>" required><br><br>

                <label>Hình ảnh (đường dẫn):</label><br>
                <input type="text" name="hinh_anh" value="<?= htmlspecialchars($sp['hinh_anh']) ?>" required><br><br>

                <label>Mô tả:</label><br>
                <textarea name="mo_ta" rows="4"><?= htmlspecialchars($sp['mo_ta']) ?></textarea><br><br>

                <label>Danh mục:</label><br>
                <select name="danhmuc" required>
                    <option value="rau" <?= $sp['danhmuc'] == 'rau' ? 'selected' : '' ?>>Rau</option>
                    <option value="sinhto" <?= $sp['danhmuc'] == 'sinhto' ? 'selected' : '' ?>>Sinh Tố</option>
                    <option value="thit" <?= $sp['danhmuc'] == 'thit' ? 'selected' : '' ?>>Thịt</option>
                    <option value="traicay" <?= $sp['danhmuc'] == 'traicay' ? 'selected' : '' ?>>Trái Cây</option>
                </select><br><br>

                <button type="submit" name="capnhat">Cập nhật</button>
                <a href="../QLSP.php"><button type="button">Hủy</button></a>
            </form>
        </div>
    <?php else: ?>
        <p>Sản phẩm không tồn tại.</p>
    <?php endif; ?>
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
