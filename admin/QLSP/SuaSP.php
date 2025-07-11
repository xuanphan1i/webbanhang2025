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
    // 
    // Giữ ảnh cũ nếu không upload ảnh mới
$hinh_anh = $_POST['hinh_anh_cu'];

if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] === 0) {
    $upload_dir = '../../public/assets/img/';
    $file_name = basename($_FILES['hinh_anh']['name']);
    $tmp_path = $_FILES['hinh_anh']['tmp_name'];

    if (move_uploaded_file($tmp_path, $upload_dir . $file_name)) {
        $hinh_anh = '../public/assets/img/' . $file_name; // đường dẫn lưu trong DB
    }
}

    $mo_ta = mysqli_real_escape_string($conn, $_POST['mo_ta']);
    $danhmuc = mysqli_real_escape_string($conn, $_POST['danhmuc']);

    $sqlUpdate = "UPDATE san_pham 
                  SET ten='$ten', gia=$gia, hinh_anh='$hinh_anh', mo_ta='$mo_ta', danhmuc='$danhmuc' 
                  WHERE id = $id";

    if (mysqli_query($conn, $sqlUpdate)) {
        echo "<script>alert('Sửa sản phẩm thành công!'); window.location.href = '../QLSP.php';</script>";
        exit;
    } else {
        echo "Lỗi sửa: " . mysqli_error($conn);
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
        <div class="container" style="background-color:rgba(255, 255, 255, 0.5);">
             <form method="POST" enctype="multipart/form-data">
                <label>Tên sản phẩm:</label><br>
                <input type="text" name="ten" value="<?= htmlspecialchars($sp['ten']) ?>" required><br><br>

                <label>Giá:</label><br>
                <input type="number" name="gia" value="<?= $sp['gia'] ?>" min="1"  required><br><br>

                <label>Hình ảnh mới:</label><br>
                <input type="file" name="hinh_anh" id="hinh_anh_input"><br><br>

                <!-- Ảnh xem trước -->
                <img id="preview_image" src="/webbanhang/public/<?= $sp['hinh_anh'] ?>" width="100">


                <label>Mô tả:</label><br>
                <textarea name="mo_ta" rows="4"><?= htmlspecialchars($sp['mo_ta']) ?></textarea><br><br>

                <label>Danh mục:</label><br>
                <select name="danhmuc" required>
                    <option value="rau" <?= $sp['danhmuc'] == 'rau' ? 'selected' : '' ?>>Rau</option>
                    <option value="sinhto" <?= $sp['danhmuc'] == 'sinhto' ? 'selected' : '' ?>>Sinh Tố</option>
                    <option value="thit" <?= $sp['danhmuc'] == 'thit' ? 'selected' : '' ?>>Thịt</option>
                    <option value="traicay" <?= $sp['danhmuc'] == 'traicay' ? 'selected' : '' ?>>Trái Cây</option>
                </select><br><br>

                <button type="submit" name="capnhat" class="btn-sua2">Sửa</button>
                <a href="../QLSP.php"><button type="button" class="btn-huy2" >Hủy</button></a>
            </form>
        </div>
    <?php else: ?>
        <p>Sản phẩm không tồn tại.</p>
    <?php endif; ?>
    <!-- hiện ảnh ngay sau khi chọn -->
    <script>
document.getElementById('hinh_anh_input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const preview = document.getElementById('preview_image');
        preview.src = URL.createObjectURL(file);
    }
});
</script>

</body>
<style>
    textarea {
  font-family: Arial, sans-serif;
  font-size: 14px;
}
body{
        font-family: Arial, Helvetica, sans-serif;

    }
    .container {
    padding: 40px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
     


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
     background-color: rgba(255, 255, 255, 0.5);
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

form {
   background-color: #fff8e6;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 500px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* đổ bóng nhẹ */
    font-family: 'Segoe UI', sans-serif;
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
</style>
</html>
