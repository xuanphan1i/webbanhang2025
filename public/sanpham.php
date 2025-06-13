<?php
require_once('../config/config.php');

// Lấy danh mục từ URL nếu có
$danhmuc = isset($_GET['danhmuc']) ? mysqli_real_escape_string($conn, $_GET['danhmuc']) : null;

// Câu truy vấn sản phẩm
if (!empty($danhmuc)) {
    $sql = "SELECT * FROM san_pham WHERE danhmuc = '$danhmuc'";
} else {
    $sql = "SELECT * FROM san_pham"; // khi không có danhmuc -> hiển thị tất cả
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Trang sản phẩm</title>
        <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">

    
</head>
<body>
    <?php require '../includes/T11header.php' ?>
    <!-- DANH MỤC -->
    <div class="san_pham">
        <div class="h22"><h2>Sản phẩm đặc trưng</h2></div>
        <!-- Nút quay lại -->
        <div class="baoa">
        <a href="javascript:history.back()" class="btn-quay-lai">Quay lại</a>
        </div>
         <div class="a-la"><img src="../public/assets/img/main/la2.png" alt=""></div>

        <ul class="ul_sp">
            <li class="li_sp">
                <a href="sanpham.php" class="<?= empty($danhmuc) ? 'active' : '' ?>">TẤT CẢ</a>
            </li>
            <li class="li_sp">
                <a href="sanpham.php?danhmuc=sinhto" class="<?= ($danhmuc == 'sinhto') ? 'active' : '' ?>">SINH TỐ</a>
            </li>
            <li class="li_sp">
                <a href="sanpham.php?danhmuc=rau" class="<?= ($danhmuc == 'rau') ? 'active' : '' ?>">RAU</a>
            </li>
            <li class="li_sp">
                <a href="sanpham.php?danhmuc=thit" class="<?= ($danhmuc == 'thit') ? 'active' : '' ?>">THỊT</a>
            </li>
            <li class="li_sp">
                <a href="sanpham.php?danhmuc=traicay" class="<?= ($danhmuc == 'traicay') ? 'active' : '' ?>">TRÁI CÂY</a>
            </li>
        </ul>


    </div>

    <!-- DANH SÁCH SẢN PHẨM -->
    <div class="sp_dien_hinh">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="sp">
                <a href="chitietSP.php?id=<?= $row['id'] ?>">
                    <img src="<?= $row['hinh_anh'] ?>" alt="<?= $row['ten'] ?>">
                </a>
                <div class="sp_chu">
                    <div class="ten"><?= $row['ten'] ?></div>
                    <div class="gia"><?= number_format($row['gia'], 0, ',', '.') ?> đ</div>
                </div>
            </div>
        <?php } ?>
    </div>

            <?php require '../includes/footer.php'?>
</body>
<style>
    .li_sp a.active {
  background-color: #91ac41;
  color: #fff;
}

        .ul_sp {
  display: flex;
  justify-content: center;
  gap: 20px;
  list-style: none;
  padding: 0;
  margin: 30px 0;
  flex-wrap: wrap;
}

.li_sp a {
  display: inline-block;
  padding: 10px 20px;
  border: 1px solid #91ac41;
  border-radius: 20px;
  color:rgb(2, 2, 2);
  text-decoration: none;
  font-weight: bold;
  transition: all 0.3s ease-in-out;
  text-transform: uppercase;
  background-color: transparent;
}

.li_sp a:hover {
  background-color: #91ac41;
  color: white;
  transform: scale(1.05);
}
.a-la {
  padding-bottom: 20px;
  text-align: center;
}

        .sp_dien_hinh {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
  padding: 20px;
}

.sp_dien_hinh .sp {
  width: 23%;
  margin: 10px 0;
  background-color: rgba(0,0,0,0.1);
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 0 5px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.sp_dien_hinh .sp:hover {
  transform: translateY(-5px);
}

.sp_dien_hinh .sp img {
  width: 100%;
  height: auto;
  display: block;
  transition: 0.3s ease;
}

.sp_dien_hinh .sp a:hover > img {
  opacity: 0.7;
}

.sp_chu {
  background-color: #dcdada;
  padding: 12px;
  text-align: center;
}

.ten {
  font-weight: bold;
  font-size: 18px;
  padding: 10px 0;
  color: #060606;
}

.gia {
  color: #91ac41;
  font-size: 15px;
  font-weight: bold;
}

.sp_chu .ten:hover {
  color: #28a745;
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
        /* menu sản phẩm nhỏ */
        
    </style>
</html>
