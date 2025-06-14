<?php
session_start();

// XỬ LÝ THÊM SẢN PHẨM
if (isset($_POST['them_gio'])) {
    $sp = [
        'id' => $_POST['id'],
        'ten' => $_POST['ten'],
        'gia' => $_POST['gia'],
        'hinh_anh' => $_POST['hinh_anh'],
        'soluong' => $_POST['soluong']
    ];

    if (!isset($_SESSION['giohang'])) {
        $_SESSION['giohang'] = [];
    }

    $_SESSION['giohang'][] = $sp;

    header("Location: giohang.php?success=1");
    exit;
}

// XỬ LÝ XÓA SẢN PHẨM
if (isset($_GET['xoa'])) {
    $index = $_GET['xoa'];
    unset($_SESSION['giohang'][$index]);
    $_SESSION['giohang'] = array_values($_SESSION['giohang']); // Reset key
    header("Location: giohang.php");
    exit;
}

// CẬP NHẬT SỐ LƯỢNG QUA GET
if (isset($_GET['capnhat'])) {
    $index = $_GET['capnhat'];
    $so_luong_moi = $_GET['soluong'] ?? null;
    if (is_numeric($index) && is_numeric($so_luong_moi) && $so_luong_moi > 0) {
        $_SESSION['giohang'][$index]['soluong'] = $so_luong_moi;
    }
    header("Location: giohang.php");
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
    <form method="POST">
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
            if (!empty($_SESSION['giohang'])) {
                foreach ($_SESSION['giohang'] as $index => $sp) {
                    $tien = $sp['gia'] * $sp['soluong'];
                    $tong += $tien;

                    echo '<tr>
                        <td><input type="checkbox" name="chon_sp[]" value="' . $index . '"></td>
                        <td>' . ($index + 1) . '</td>
                        <td><img src="' . $sp['hinh_anh'] . '" width="50"></td>
                        <td>' . $sp['ten'] . '</td>
                        <td>' . $sp['gia'] . '</td>
                        <td><input type="number" id="soluong_' . $index . '" value="' . $sp['soluong'] . '" min="1"></td>
                        <td>' . $tien . '</td>
                        <td>
                            <a href="giohang.php?xoa=' . $index . '">Xóa</a><br>
                            <a href="#" onclick="capNhatSoLuong(' . $index . ')">Cập nhật</a>
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
        <form method="POST">
            <!-- <button type="submit" name="capnhat">Cập nhật số lượng</button> -->

            <button type="submit" name="dathang"
                id="nutDatHang"
                <?php if (!isset($_SESSION['user'])) echo 'disabled'; ?>>
                Đặt hàng
            </button>
        </form>
    </div>
<!-- ✅ JavaScript -->
<script>
function toggleCheckboxes(master) {
    const checkboxes = document.querySelectorAll("input[name='chon_sp[]']");
    checkboxes.forEach(cb => cb.checked = master.checked);
}

function capNhatSoLuong(index) {
    const input = document.getElementById('soluong_' + index);
    const soLuongMoi = input.value;
    if (soLuongMoi > 0) {
        window.location.href = 'giohang.php?capnhat=' + index + '&soluong=' + soLuongMoi;
    } else {
        alert('Số lượng phải lớn hơn 0');
    }
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
   button[type="submit"],
button[type="button"] {
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
    border: none;
    cursor: pointer;
}

/* Hover tách riêng ra */
button[type="submit"]:hover,
button[type="button"]:hover {
    background-color: #ef7f94; /* đậm hơn khi hover */
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


</style>
