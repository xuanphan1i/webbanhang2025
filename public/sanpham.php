<?php
session_start();
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

// XỬ LÝ THÊM GIỎ HÀNG NGAY TRONG FILE NÀY
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_gio'])) {
    $sp_id = (int)$_POST['id'];
    $so_luong = (int)$_POST['soluong'];

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT * FROM gio_hang WHERE user_id = ? AND san_pham_id = ?");
        $stmt->bind_param("ii", $user_id, $sp_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE gio_hang SET so_luong = so_luong + ? WHERE user_id = ? AND san_pham_id = ?");
            $stmt->bind_param("iii", $so_luong, $user_id, $sp_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO gio_hang (user_id, san_pham_id, so_luong) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $sp_id, $so_luong);
        }

        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng (CSDL)']);
        exit;
    } else {
        $sp = [
            'san_pham_id' => $sp_id,
            'ten' => $_POST['ten'],
            'gia' => $_POST['gia'],
            'hinh_anh' => $_POST['hinh_anh'],
            'so_luong' => $so_luong
        ];

        if (!isset($_SESSION['giohang'])) $_SESSION['giohang'] = [];
        if (isset($_SESSION['giohang'][$sp_id])) {
            $_SESSION['giohang'][$sp_id]['so_luong'] += $so_luong;
        } else {
            $_SESSION['giohang'][$sp_id] = $sp;
        }

        echo json_encode(['status' => 'session', 'message' => 'Đã thêm vào giỏ hàng (chưa đăng nhập)']);
        exit;
    }
}
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
                    <div class="gia-them">
    <span class="gia"><?= number_format($row['gia'], 0, ',', '.') ?> đ</span>

    <!-- Form thêm vào giỏ hàng -->
  <form method="post" action="" class="form-gio">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <input type="hidden" name="ten" value="<?= $row['ten'] ?>">
    <input type="hidden" name="gia" value="<?= $row['gia'] ?>">
    <input type="hidden" name="hinh_anh" value="<?= $row['hinh_anh'] ?>">
    <input type="hidden" name="soluong" value="1">
    <button type="submit" name="them_gio" class="nut-them-gio" title="Thêm giỏ hàng">+</button>
</form>

</div>

                </div>
            </div>
        <?php } ?>
    </div>

            <?php require '../includes/footer.php'?>
    <!-- js -->
    <script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-gio').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('them_gio', '1');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' || data.status === 'session') {
                    alert('✅ ' + data.message);
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                alert('⚠️ Không thể kết nối đến máy chủ!');
                console.error(err);
            });
        });
    });
});
</script>

</body>
<style>
    .li_sp a.active {
  background-color: #91ac41;
  color: #fff;
}
.gia-them {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.gia {
    font-weight: bold;
    color: #ef7f94;
}

.form-gio {
    margin-left: 10px;
}

.nut-them-gio {
    background-color: #ef7f94;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.2s ease;
}

.nut-them-gio:hover {
    background-color: #d95e7d;
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
        /* menu sản phẩm nhỏ */
        
    </style>
</html>
