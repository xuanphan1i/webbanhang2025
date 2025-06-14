<!-- code php -->
<?php
session_start(); // ✅ Bắt buộc để sử dụng $_SESSION

include '../config/config.php';  // 🟡 Đảm bảo đường dẫn đúng với vị trí file này

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mat_khau = $_POST['password'];
    $so_dien_thoai = $_POST['so_dien_thoai']; // ✅ Thêm dòng này để nhận số điện thoại
    $vai_tro = $_POST['vai_tro']; // giá trị vai trò người dùng nhập

    // 🔴 Đảm bảo form HTML của bạn có input name="email" và name="mat_khau"

    $sql = "SELECT * FROM nguoi_dung WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email); // ✅ Gán email vào câu lệnh

    $stmt->execute();
    $result = $stmt->get_result(); // ✅ Lấy kết quả sau khi thực hiện truy vấn

    //kiểm tra email , sdt, mk, vai trò có tồn tại ko, , 
    if ($result->num_rows == 1) {// if này kiểm tra email có tồn tại ko, kiểm tra xem trong cơ sở dữ liệu có đúng 1 bản ghi với email đã nhập hay không 
    $user = $result->fetch_assoc();

    // So sánh số điện thoại
    if ($so_dien_thoai != $user['so_dien_thoai']) {
        echo "<script> alert('Vui lòng nhập lại! (Số điện thoại không đúng!)')</script> " ;//alert('Đăng nhập thất bại, vui lòng thử lại!');
    }
    // So sánh mật khẩu
    else if ($user['vai_tro'] != $vai_tro) {
        echo " <script> alert ('Vui lòng nhập lại! (Bạn không có quyền truy cập trang quản trị!)')</script> ";
    }
    // So sánh vai trò
    else if ($mat_khau != $user['mat_khau']) {
        echo " <script>alert('Vui lòng nhập lại! (Mật khẩu không đúng!)')</script> ";
    }
    // Nếu tất cả đều đúng
    else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['ten'];
        $_SESSION['user_role'] = $user['vai_tro'];
    // ✅ Thêm đoạn này để giữ giỏ hàng nếu trước đó đã thêm
        if (!isset($_SESSION['giohang'])) {
            $_SESSION['giohang'] = [];
        }

        echo "<script>alert('Đăng nhập thành công!');";
            if ($user['vai_tro'] == 'admin') {
                echo "window.location.href = '../admin/index.php';";
            } else if ($user['vai_tro'] == 'nguoidung') {
                echo "window.location.href = '../public/index.php';";
            } 

            echo "</script>";

        // header('Location: ../admin/index.php');
    }
} else {
    echo " <script>alert('Vui lòng nhập lại! (Email không tồn tại!)')</script> ";
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

                <div class="form-group">
                  <label for="vai_tro">Vai trò</label>
                  <select id="vai_tro" name="vai_tro" required>
                    <option value="nguoidung">nguoidung</option>
                    <option value="admin">admin</option>
                  </select>
                </div>

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
