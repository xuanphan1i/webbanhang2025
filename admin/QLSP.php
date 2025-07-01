<?php
session_start();
require_once '../config/config.php';
function hienThiDanhMucTiengViet($danhmuc) {
    $mapping = [
        'sinhto' => 'Sinh tố',
        'rau' => 'Rau',
        'thit' => 'Thịt',
        'traicay' => 'Trái cây'
    ];

    return $mapping[$danhmuc] ?? ucfirst($danhmuc);
}

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../public/login.php");
    exit;
}

//tìm kiếm sp
$tu_khoa = isset($_GET['tu_khoa']) ? trim($_GET['tu_khoa']) : '';

// Nếu có từ khóa tìm kiếm
if ($tu_khoa !== '') {
    $stmt = $conn->prepare("SELECT * FROM san_pham WHERE ten LIKE ?");
    $like = "%" . $tu_khoa . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Không có từ khóa thì hiện toàn bộ sản phẩm
    $result = $conn->query("SELECT * FROM san_pham");
}


// Lấy danh sách sản phẩm từ CSDL
$sql = "SELECT * FROM san_pham ORDER BY id ASC";
$result = $conn->query($sql);

if (isset($_GET['action']) && $_GET['action'] === 'xoa' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM san_pham WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('Đã xóa sản phẩm!'); window.location.href='qlsp.php';</script>";
    exit; 
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">

    
</head>
<body >

      <div class="h22"><h2>DANH SÁCH SẢN PHẨM</h2></div>
        <!-- Nút quay lại -->
    <div class="baoa"><a href="index.php" class="btn-quay-lai">Quay lại</a></div>

    <a class="btn-them" href="../admin/QLSP/ThemSP.php">+ Thêm sản phẩm mới</a>

    <table >
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Giá (VNĐ)</th>
            <th>Hình ảnh</th>
            <th>Mô tả</th>
            <th>Danh mục</th>
            <th>Thao tác</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['ten']) ?></td>
            <td><?= number_format($row['gia'], 0, ',', '.') ?> đ</td>
            <td><img src="<?= htmlspecialchars($row['hinh_anh']) ?>" alt=""></td>
           <td >
                <?= nl2br(htmlspecialchars($row['mo_ta'])) ?>
           </td>


            <td><?= hienThiDanhMucTiengViet($row['danhmuc']) ?></td>

            <td>
                <a class="btn btn-sua" href="/webbanhang/admin/QLSP/SuaSP.php?id=<?=$row['id']?>" class='btn-sua'>Sửa</a>

                <a class="btn btn-xoa" href="QLSP.php?action=xoa&id=<?= $row['id'] ?>" class='btn-xoa' onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">Xóa</a>


            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</body>
<style>
        body {
            font-family: Arial, sans-serif;
            background:rgb(255, 255, 255);
            padding: 20px;
        }
        textarea {
  font-family: Arial, sans-serif;
  font-size: 14px;
}
body{
        font-family: Arial, Helvetica, sans-serif;

    }
/* Chỉ áp dụng cho ô chứa mô tả nếu có thể xác định */
td:nth-child(5) {
  text-align: left;
  
}
          .h22 {
            /* position: fixed; */
            top: 10px;
            left: 0;
            width: 100%;
            padding: 30px 0;
            background-image: url(../public/assets/img/main/gioiThieu/a1.png);
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            text-transform: uppercase;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 0;

           
            
            }
        h2{
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
        .btn-them {
    display: inline-block;
    padding: 10px 20px;
    background-color: #ee4d2d;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    text-transform: uppercase;
    text-decoration: none;
    transition: background-color 0.3s ease;
    
    float: right;       /* căn phải */
    margin-top: -38px;  /* đẩy lên ngang với nút "quay lại" nếu cần */
    margin-right: 10px; /* cách lề phải */
}

.btn-them:hover {
    background-color: #d8431f;
}


        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-top: 30px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #2e7d32;
            color: #fff;
        }

        img {
            width: 100px;
            height: auto;
        }

        .btn-sua {
           
        }

        .btn-xoa {
            
        }

        .btn-sua:hover {
            background-color: #e0a800;
        }

        .btn-xoa:hover {
            background-color: #c82333;
        }
        .btn-sua {
    background-color: #4CAF50; /* xanh lá */
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    margin-bottom: 4px;
   
}
td a.btn-sua,
td a.btn-xoa {
    display: inline-block !important;
    margin-top: 0 !important;
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
