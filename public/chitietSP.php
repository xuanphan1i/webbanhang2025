<?php
session_start();
require_once '../config/config.php';

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

// LẤY DỮ LIỆU CHI TIẾT SẢN PHẨM
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM san_pham WHERE id = $id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $sp = $result->fetch_assoc();
    } else {
        echo "<p>Không tìm thấy sản phẩm.</p>";
        exit;
    }
} else {
    echo "<p>Không có sản phẩm được chọn.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - <?= htmlspecialchars($sp['ten']) ?></title>
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">
</head>
<body>
<?php require '../includes/T11header.php'; ?>
<div id="khung">
    <div class="h22"><h2>Chi tiết sản phẩm</h2></div>
    <div class="baoa">
        <a href="javascript:history.back()" class="btn-quay-lai">Quay lại</a>
    </div>

    <?php if ($sp): ?>
    <div class="duoi">
        <div class="anh">
            <img src="<?= htmlspecialchars($sp['hinh_anh']) ?>" alt="<?= htmlspecialchars($sp['ten']) ?>" />
        </div>
        <div class="nd">
            <h3><?= htmlspecialchars($sp['ten']) ?></h3>
            <div class="tt"><b>Tình trạng:</b> Còn hàng</div>
            <p class="tt-nutri"><b>Giá trị dinh dưỡng:</b></p>
            <p class="tt-mo-ta"><?= nl2br(htmlspecialchars($sp['mo_ta'])) ?></p>
            <h4 class="dang-cap-nhat">(Thông tin sản phẩm đang được cập nhật)</h4>
            <p class="gia">Giá: $<?= number_format($sp['gia'], 2) ?></p>
            <div class="sl">
                <label for="sl">Số lượng:</label>
                <input type="number" name="soluong" class="input-soluong" value="1" min="1" max="10" />
            </div>

            <form action="" method="POST" class="form-them-gio">
                <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                <input type="hidden" name="ten" value="<?= htmlspecialchars($sp['ten']) ?>">
                <input type="hidden" name="gia" value="<?= $sp['gia'] ?>">
                <input type="hidden" name="hinh_anh" value="<?= htmlspecialchars($sp['hinh_anh']) ?>">
                <input type="hidden" name="soluong" value="1">
                <button type="submit" name="them_gio" class="btn-them">Thêm vào giỏ hàng</button>
            </form>
        </div>
    </div>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm.</p>
    <?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-them-gio').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('them_gio', '1');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Lỗi kết nối hoặc phản hồi máy chủ không hợp lệ');
                return response.json();
            })
            .then(data => {
                if (data.status === 'success' || data.status === 'session') {
                    alert('✅ ' + data.message);
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                alert('⚠️ Không thể kết nối đến máy chủ!');
                console.error('Lỗi:', error);
            });
        });
    });
});
</script>


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
    .duoi {
        display: flex;
        margin: 30px auto;
        max-width: 1000px;
        gap: 70px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 12px;
        background-color: #f8f8f8;
    }

    .duoi .anh img {
        max-width: 400px;
        border-radius: 8px;
        border: 2px solid #91ac41;
    }

.nd h3 {
  font-size: 30px;
  color: rgb(54, 53, 53);
  margin-bottom: 15px;
}

.tt {
  color: #91ac41;
  font-size: 16px;
  margin-bottom: 10px;
}

.tt-nutri {
  color: red;
  font-weight: bold;
  margin-top: 10px;
}

.tt-mo-ta {
  font-style: italic;
  color: coral;
}

.dang-cap-nhat {
  opacity: 0.7;
  margin-top: 10px;
}

.gia {
  color: #91ac41;
  font-size: 20px;
  font-weight: bold;
  margin-top: 10px;
}

.sl {
  margin-top: 15px;
}

.sl input[type="number"] {
  padding: 6px 10px;
  width: 70px;
  font-size: 16px;
  border-radius: 4px;
  border: 1px solid #ccc;
}
.btn-them {
    display: inline-block;
    padding: 12px 28px;
    background-color: #ee4d2d; /* cam đậm kiểu Shopee */
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 10px;
}

.btn-them:hover {
    background-color: #d8431f; /* đậm hơn khi hover */
}

</style>
</html>
