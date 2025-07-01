<?php
session_start();

include '../config/config.php';
if (!isset($_SESSION['user_id'])) {
    // ❌ Chưa đăng nhập → chuyển về trang đăng nhập (hoặc hiện thông báo)
    echo "<script>alert('⚠️ Vui lòng đăng nhập để xem giỏ hàng'); window.location.href = 'login.php';</script>";
    exit;
}
$ds_gio_hang = [];
// hiển thị sp từ bảng giỏ hàng ra giỏ hàng
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT gh.san_pham_id, gh.so_luong, sp.ten, sp.gia, sp.hinh_anh 
            FROM gio_hang gh
            JOIN san_pham sp ON gh.san_pham_id = sp.id
            WHERE gh.user_id = $user_id";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $ds_gio_hang[] = $row;
    }
} else {
    // Nếu chưa đăng nhập, lấy từ session
    $ds_gio_hang = $_SESSION['giohang'] ?? [];
}





// XÓA SẢN PHẨM
if (isset($_GET['xoa'])) {
    $san_pham_id = $_GET['xoa'];

    if (isset($_SESSION['user_id'])) {
        // Người dùng đã đăng nhập → xóa trong DB
        $stmt = $conn->prepare("DELETE FROM gio_hang WHERE user_id = ? AND san_pham_id = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $san_pham_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Nếu chưa đăng nhập → xóa trong session
        unset($_SESSION['giohang'][$san_pham_id]);
        $_SESSION['giohang'] = array_values($_SESSION['giohang']);
    }

    header("Location: giohang.php");
    exit;
}


// CẬP NHẬT SỐ LƯỢNG
if (isset($_GET['capnhat'])) {
    $san_pham_id = $_GET['capnhat'];
    $so_luong_moi = $_GET['soluong'] ?? null;

    if (is_numeric($san_pham_id) && is_numeric($so_luong_moi) && $so_luong_moi > 0) {
        if (isset($_SESSION['user_id'])) {
            // Người dùng đã đăng nhập → cập nhật trong DB
            $stmt = $conn->prepare("UPDATE gio_hang SET so_luong = ? WHERE user_id = ? AND san_pham_id = ?");
            $stmt->bind_param("iii", $so_luong_moi, $_SESSION['user_id'], $san_pham_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Nếu chưa đăng nhập → cập nhật trong session
            $_SESSION['giohang'][$san_pham_id]['soluong'] = $so_luong_moi;
        }
    }

    header("Location: giohang.php");
    exit;
}


// ĐẶT HÀNG
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dathang'])) {
    // 1. Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Bạn cần đăng nhập để đặt hàng');</script>";
        exit;
    }

   
    // 2. Kiểm tra giỏ hàng (tùy theo đã đăng nhập hay chưa)
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT COUNT(*) AS tong FROM gio_hang WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['tong'] == 0) {
        echo "<script>alert('Giỏ hàng của bạn đang trống!');</script>";
        exit;
    }
} else {
    // Nếu chưa đăng nhập thì kiểm tra trong session
    if (!isset($_SESSION['giohang']) || count($_SESSION['giohang']) === 0) {
        echo "<script>alert('Giỏ hàng của bạn đang trống!');</script>";
        exit;
    }
}


    // 3. Kiểm tra có chọn sản phẩm không
    if (!isset($_POST['chon_sp']) || count($_POST['chon_sp']) === 0) {
        echo "<script>alert('Vui lòng chọn ít nhất một sản phẩm để đặt hàng');</script>";
        exit;
    }

    // Gửi sang trang xử lý đặt hàng
    header('Location: dathang.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">
</head>
<body>
<?php require '../includes/T11header.php'; ?>

<?php if (isset($_GET['success'])): ?>
    <div style="color: green; font-weight: bold;">✅ Sản phẩm đã được thêm vào giỏ hàng!</div>
<?php endif; ?>

<div class="h22"><h2>Giỏ hàng của bạn</h2></div>

<div class="baoa">
    <a href="javascript:history.back()" class="btn-quay-lai">Quay lại</a>
</div>

<div class="baoform">
    <form method="POST" id="formCheckbox">
        <table border="1" cellpadding="8" cellspacing="0" style="width: 100%;">
            <tr>
                <th><input type="checkbox" id="chon_tatca" onclick="toggleCheckboxes(this)"></th>
                <th width="5%">STT</th>
                <th width="30%">Hình ảnh</th>
                <th width="15%">Tên</th>
                <th width="15%">Giá</th>
                <th width="5%">Số lượng</th>
                <th width="20%">Tổng</th>
                <th width="15%">Thao tác</th>
            </tr>

            <?php
            $tong = 0;
            if (!empty($ds_gio_hang)) {
                foreach ($ds_gio_hang as $index => $sp) {
                    $tien = $sp['gia'] * $sp['so_luong']; // dùng đúng tên từ DB
                    $tong += $tien;

                    echo '<tr>
                        <td><input type="checkbox" name="chon_sp[]" value="' . htmlspecialchars($sp['san_pham_id']) . '"></td>
                        <td>' . ($index + 1) . '</td>
                        <td><img src="' . htmlspecialchars($sp['hinh_anh']) . '" width="50"></td>
                        <td>' . htmlspecialchars($sp['ten']) . '</td>
                        <td>' . number_format($sp['gia']) . '</td>
                        <td><input type="number" id="soluong_' . $sp['san_pham_id'] . '" value="' . $sp['so_luong'] . '" min="1"></td>
                        <td>' . number_format($tien) . '</td>
                        <td>
                            <a href="giohang.php?xoa=' . $sp['san_pham_id'] . '" 
                            class="btn-xoa" 
                            onclick="return confirm(\'Bạn có chắc chắn muốn xóa sản phẩm này không?\')">
                            Xóa
                            </a><br>
                            <a href="#" 
                            class="btn-capnhat" 
                            onclick="capNhatSoLuong(' . $sp['san_pham_id'] . ')">
                            Cập nhật
                            </a>
                        </td>
                    </tr>';


                }
            } else {
                echo '<tr><td colspan="8">Giỏ hàng trống</td></tr>';
            }
            ?>
        </table>
    </form>
</div>

<!-- ✅ Thanh cố định cuối màn hình -->
<div class="thanh-cuoi-man-hinh">
    <form action="dathang.php" method="POST" id="formDatHang" onsubmit="return copyCheckboxes()">
        <button type="submit" name="dathang"
            id="nutDatHang"
            <?php if (!isset($_SESSION['user_id'])) echo 'disabled'; ?>>
            Đặt hàng
        </button>
    </form>
</div>

<!-- ✅ JavaScript -->
<script>
// Chọn tất cả
function toggleCheckboxes(master) {
    const checkboxes = document.querySelectorAll("input[name='chon_sp[]']");
    checkboxes.forEach(cb => cb.checked = master.checked);
}

// Cập nhật số lượng
function capNhatSoLuong(index) {
    const input = document.getElementById('soluong_' + index);
    const soLuongMoi = input.value;
    if (soLuongMoi > 0) {
        window.location.href = 'giohang.php?capnhat=' + index + '&soluong=' + soLuongMoi;
    } else {
        alert('Số lượng phải lớn hơn 0');
    }
}

// Kiểm tra checkbox bật/tắt nút đặt hàng
<?php if (isset($_SESSION['user_id'])): ?>
    const nutDatHang = document.getElementById('nutDatHang');
    const checkboxes = document.querySelectorAll('input[name="chon_sp[]"]');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const checkedCount = document.querySelectorAll('input[name="chon_sp[]"]:checked').length;
            nutDatHang.disabled = (checkedCount === 0);
        });
    });
<?php endif; ?>

// Sao chép checkbox đã chọn từ form 1 sang form 2
function copyCheckboxes() {
    const form1 = document.getElementById('formCheckbox');
    const form2 = document.getElementById('formDatHang');

    // Xóa checkbox cũ trong form2
    form2.querySelectorAll('input[name="chon_sp[]"]').forEach(el => el.remove());

    const checked = form1.querySelectorAll('input[name="chon_sp[]"]:checked');

    if (checked.length === 0) {
        alert("Vui lòng chọn ít nhất một sản phẩm để đặt hàng.");
        return false;
    }

    // Tạo input hidden tương ứng trong form2
    checked.forEach(cb => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'chon_sp[]';
        hiddenInput.value = cb.value;
        form2.appendChild(hiddenInput);
    });

    return true;
}
</script>
</body>
</html>


<style>
    .baoform{
        /* display:flex;
        justify-content: center; */
        margin-bottom: 300px;
    }
    .baoform table th,
.baoform table td {
    text-align: center;
    padding: 10px;
}

.baoform img {
    max-width: 100px;
    height: auto;
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
#nutDatHang {
    display: inline-block;
    padding: 12px 28px;
    background-color: #ee4d2d;      /* màu cam đỏ đặc trưng Shopee */
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
    transition: background-color 0.3s ease;
    cursor: pointer;
}

/* Hover */
#nutDatHang:hover {
    background-color: #d8431f;      /* cam đậm hơn */
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

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
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
.thanh-cuoi-man-hinh {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #fff;
    border-top: 1px solid #ccc;
    padding: 10px 20px;
    text-align: right;
    box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
    z-index: 999;
    margin-bottom: 20px;
    
}

.thanh-cuoi-man-hinh form {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-right: 40px;
}



.thanh-cuoi-man-hinh button:disabled {
    background-color: #aaa;
    cursor: not-allowed;
}
.thanh-cuoi-man-hinh {
    display: flex;
    justify-content: center; /* căn giữa theo chiều ngang */
    align-items: center;
    margin-top: 20px;
}

.btn-capnhat,
.btn-xoa {
    display: inline-block;
    width: 90px;              /* ✅ Cố định chiều rộng */
    text-align: center;
    padding: 6px 10px;        /* ✅ Cùng padding */
    font-size: 14px;          /* ✅ Cùng cỡ chữ */
    border-radius: 4px;
    color: white;
    text-decoration: none;
    margin-bottom: 4px;
}

.btn-capnhat {
    background-color: #4CAF50;  /* Xanh */
}

.btn-xoa {
    background-color: #f44336;  /* Đỏ */
             /* Khoảng cách giữa 2 nút */
}

</style>
