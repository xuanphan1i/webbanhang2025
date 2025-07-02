<?php
session_start();
require_once '../config/config.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../public/login.php");
    exit;
}

// Xử lý xóa
// Xử lý xóa
if (isset($_GET['action']) && $_GET['action'] === 'xoa' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Kiểm tra vai trò người dùng trước khi xóa
    $stmt = $conn->prepare("SELECT vai_tro FROM nguoi_dung WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Người dùng không tồn tại!'); window.location.href='QLnguoidung.php';</script>";
        exit;
    }

    $user = $result->fetch_assoc();

    if ($user['vai_tro'] === 'admin') {
        echo "<script>alert('Không thể xóa tài khoản có vai trò là admin!'); window.location.href='QLnguoidung.php';</script>";
        exit;
    }

    // Nếu không phải admin thì xóa
    $stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<script>alert('Đã xóa người dùng!'); window.location.href='QLnguoidung.php';</script>";
    exit;
}

// Khởi tạo biến tránh lỗi undefined
$nguoi_dung_sua = null;

// Xử lý sửa nếu có action=sua và id
if (isset($_GET['action']) && $_GET['action'] === 'sua' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $nguoi_dung_sua = $stmt->get_result()->fetch_assoc();
}

// Xử lý cập nhật sau khi sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sua_nguoi_dung'])) {
    $id = intval($_POST['id']);
    $ten = trim($_POST['ten']);
    $email = trim($_POST['email']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);

    $stmt = $conn->prepare("UPDATE nguoi_dung SET ten = ?, email = ?, so_dien_thoai = ? WHERE id = ?");
    $stmt->bind_param("sssi", $ten, $email, $so_dien_thoai, $id);
    if ($stmt->execute()) {
        echo "<script>alert('Sửa thành công!'); window.location.href='QLnguoidung.php';</script>";
    } else {
        echo "<script>alert('Sửa không thành công, vui lòng thử lại.'); window.history.back();</script>";
    }
    exit;
}
// Truy vấn danh sách người dùng
$sql = "SELECT id, ten, email, so_dien_thoai, vai_tro, ngay_tao FROM nguoi_dung ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý người dùng</title>
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png" />
   
    
</head>
<body >
    <div class="h22"><h2>Danh sách người dùng</h2></div>
        <!-- Nút quay lại -->
    <div class="baoa"><a href="index.php" class="btn-quay-lai">Quay lại</a></div>

    <table >
        <thead>
            <tr style="color: #ffff;">
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Vai trò</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ten']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['so_dien_thoai']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['vai_tro']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ngay_tao']) . "</td>";
                    echo "<td>
                            <a href='QLnguoidung.php?action=sua&id=" . $row['id'] . "' class='btn-sua' >Sửa</a> |
                            <a href='QLnguoidung.php?action=xoa&id=" . $row['id'] . "'class='btn-xoa' onclick=\"return confirm('Bạn có chắc chắn muốn xóa không?');\">Xóa</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Chưa có người dùng nào.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Form sửa người dùng -->
    <?php if ($nguoi_dung_sua): ?>
    <div class="overlay">
        <div class="form-sua">
        <h3 style="font-size: 20px; color:#aaa ; font-weight: 600; text-align: center;" >Sửa người dùng: <?= htmlspecialchars($nguoi_dung_sua['ten']) ?></h3>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $nguoi_dung_sua['id'] ?>">
            <label>Tên:</label><br>
            <input type="text" name="ten" value="<?= htmlspecialchars($nguoi_dung_sua['ten']) ?>" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" value="<?= htmlspecialchars($nguoi_dung_sua['email']) ?>" required><br><br>

            <label>Số điện thoại:</label><br>
            <input type="text" name="so_dien_thoai" pattern="[0-9]{10}" maxlength="10" value="<?= htmlspecialchars($nguoi_dung_sua['so_dien_thoai']) ?>" required><br><br>

            <button type="submit" name="sua_nguoi_dung" class="btn-sua2">Sửa</button>
            <a href="QLNguoiDung.php" style="margin-left: 10px;">
                <button type="button" class="btn-huy2">Hủy</button>
            </a>

        </form>
    </div>
    </div>
    <?php endif; ?>
    <div style="height: 500px;" class="cuoi"></div>
</body>
 <style>
    textarea {
  font-family: Arial, sans-serif;
  font-size: 14px;
}
body{
        font-family: Arial, Helvetica, sans-serif;

    }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #2e7d32;
        }
        .form-sua {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            background:rgb(255, 255, 255);
            width: 400px;
            padding: 20px;
            border: 2px solid khaki;           /* Viền xám nhạt */
            border-radius: 10px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);  /* Đổ bóng nhẹ */
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
        h3,
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
        .baoa{
           margin-top: 20px;         
            
           
        }
        .overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
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

.btn-sua {
    background-color: #4CAF50; /* xanh lá */
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
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
