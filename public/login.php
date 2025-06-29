<!-- code php -->
<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mat_khau = $_POST['password'];
    $so_dien_thoai = $_POST['so_dien_thoai'];

    $sql = "SELECT * FROM nguoi_dung WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($so_dien_thoai != $user['so_dien_thoai']) {
            echo "<script>alert('Vui lòng nhập lại! (Số điện thoại không đúng!)')</script>";
        } elseif ($mat_khau != $user['mat_khau']) {
            echo "<script>alert('Vui lòng nhập lại! (Mật khẩu không đúng!)')</script>";
        } else {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['ten'];
            $_SESSION['user_role'] = $user['vai_tro'];

            // Nếu có giỏ hàng trong session thì lưu lại vào DB
            if (isset($_SESSION['giohang']) && !empty($_SESSION['giohang'])) {
                foreach ($_SESSION['giohang'] as $id_sp => $sp) {
                    $so_luong = isset($sp['so_luong']) ? (int)$sp['so_luong'] : 1;

                    $stmt_check_sp = $conn->prepare("SELECT id FROM san_pham WHERE id = ?");
                    $stmt_check_sp->bind_param("i", $id_sp);
                    $stmt_check_sp->execute();
                    $result_check_sp = $stmt_check_sp->get_result();

                    if ($result_check_sp->num_rows > 0) {
                        $stmt = $conn->prepare("SELECT id FROM gio_hang WHERE user_id = ? AND san_pham_id = ?");
                        $stmt->bind_param("ii", $_SESSION['user_id'], $id_sp);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $stmt_update = $conn->prepare("UPDATE gio_hang SET so_luong = so_luong + ? WHERE user_id = ? AND san_pham_id = ?");
                            $stmt_update->bind_param("iii", $so_luong, $_SESSION['user_id'], $id_sp);
                            $stmt_update->execute();
                            $stmt_update->close();
                        } else {
                            $stmt_insert = $conn->prepare("INSERT INTO gio_hang (user_id, san_pham_id, so_luong) VALUES (?, ?, ?)");
                            $stmt_insert->bind_param("iii", $_SESSION['user_id'], $id_sp, $so_luong);
                            $stmt_insert->execute();
                            $stmt_insert->close();
                        }

                        $stmt->close();
                    }

                    $stmt_check_sp->close();
                }

                unset($_SESSION['giohang']); // Xoá session giỏ sau khi lưu
            }

            // Chuyển trang sau khi đăng nhập
            echo "<script>alert('Đăng nhập thành công!');";
            if ($user['vai_tro'] == 'admin') {
                echo "window.location.href = '../admin/index.php';";
            } else {
                echo "window.location.href = '../public/index.php';";
            }
            echo "</script>";
        }
    } else {
        echo "<script>alert('Vui lòng nhập lại! (Email không tồn tại!)')</script>";
    }
}
?>


<!-- giao diện html+ css -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập</title>
    <link
      rel="icon"
      type="image/png"
      href="../public/assets/img/favicon/android-chrome-512x512.png"
    />
    <link
      rel="icon"
      type="image/png"
      href="../public/assets/img/favicon/android-chrome-512x512.png"
    />

  </head>
  <body>
    <?php require '../includes/T11header.php' ?>
    <div class="bao">
        <div class="container">
            <h2>Đăng Nhập</h2>
            <form action="login.php" method="post" id="form_dang_nhap">
                <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required />
                </div>
                <div class="form-group">
                <label for="so_dien_thoai">Số điện thoại</label>
                <input type="tel" id="so_dien_thoai" name="so_dien_thoai"  required pattern="[0-9]{10}" maxlength="10"/>
                </div>

                <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required />
                </div>

                <!-- <div class="form-group">
                  <label for="vai_tro">Vai trò</label>
                  <select id="vai_tro" name="vai_tro" required>
                    <option value="nguoidung">nguoidung</option>
                    <option value="admin">admin</option>
                  </select>
                </div> -->

                <button type="submit">Đăng Nhập</button>

                <div class="register-link">
                <p>
                    Chưa có tài khoản?
                    <a href="../public/register.php">Đăng ký ngay</a>
                </p>
                
                </div>
            </form>
        </div>
    </div>

    <!-- js -->
   <?php require '../includes/footer.php' ?>
  </body>
  <style>
    /* Định dạng nền */
.bao{
     background-color: #d4e1b3; 
     display: flex;
     justify-content: center;
     padding: 50px 0;
}
/* Container chính */
.container {
  background: white;
  padding: 20px 30px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  width: 350px;
  text-align: center;
 
}

/* Tiêu đề */
h2 {
  color: #333;
}

/* Form nhóm */
.form-group {
  text-align: left;
  margin-bottom: 15px;
}

.form-group label {
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

/* Nút đăng nhập */
button {
  width: 100%;
  padding: 10px;
  margin-top: 20px;
  background-color: #8aa53f; /* Màu xanh */
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  background-color: #7a9438;
  color: #fff; /* Giữ nguyên màu chữ */
  border-color: #8bb563;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Hiệu ứng nổi */
}

/* Đăng ký */
.register-link {
  margin-top: 15px;
}

.register-link a {
  color: #6b8e23;
  text-decoration: none;
  font-weight: bold;
}

.register-link a:hover {
  text-decoration: underline;
}
.pt a {
  text-decoration: none;
  color: #4a752c; /* Màu xanh lá đậm để hài hòa với nút Đăng ký */
}

  </style>
</html>
