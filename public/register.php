<!-- code php -->
<?php
session_start();
include '../config/config.php'; // Kết nối database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $ten = trim($_POST['ten']);
    $email = trim($_POST['email']);
    $mat_khau = $_POST['password'];
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    
    // Gán cố định vai trò là 'nguoidung'
    $vai_tro = 'nguoidung';

    // Kiểm tra email đã tồn tại chưa
    $sql_check = "SELECT id FROM nguoi_dung WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('s', $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "<script>alert('Email đã được đăng ký rồi! Vui lòng chọn email khác.');
              window.history.back();
              </script>";
        exit;
    }
    
    // Thêm người dùng mới vào DB (mật khẩu thô)
    $sql_insert = "INSERT INTO nguoi_dung (ten, email, mat_khau, so_dien_thoai, vai_tro, ngay_tao) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param('sssss', $ten, $email, $mat_khau, $so_dien_thoai, $vai_tro);

    if ($stmt_insert->execute()) {
        echo "<script>alert('Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.');
              window.location.href = '../public/login.php';
              </script>";
    } else {
        echo "<script>alert('Đăng ký thất bại, vui lòng thử lại!');
              window.history.back();
              </script>";
    }
}

?>


<!-- giao diện html+css -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng ký</title>
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">

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
      <h2>Đăng Ký</h2>
      <form action="register.php" method="POST">
        <div class="form-group">
          <label for="ten">Tên</label>
          <input type="text" id="ten" name="ten" required class="nhap" />
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required  class="nhap"/>
        </div>

        <div class="form-group">
          <label for="password">Mật khẩu</label>
          <input type="password" id="password" name="password" required class="nhap" />
        </div>

        <div class="form-group">
          <label for="so_dien_thoai">Số điện thoại</label>
          <input type="tel" id="so_dien_thoai" name="so_dien_thoai"  required class="nhap" pattern="[0-9]{10}" maxlength="10" />
        </div>

        <!-- <div class="form-group">
          <label for="vai_tro">Vai trò</label>
          <select id="vai_tro" name="vai_tro" required>
            <option value="nguoidung">nguoidung</option>
            <option value="admin">admin</option>
          </select>
        </div> -->

        <button type="submit">Đăng Ký</button>

        <p class="login-link">
          Bạn đã có tài khoản?
          <a href="../public/login.php">Đăng nhập ngay</a>
        </p>
      </p>
      </form>
    </div>
    </div>
 <?php require '../includes/footer.php' ?>

  </body>
  <style>
.bao{
     background-color: #d4e1b3; 
     display: flex;
     justify-content: center;
     padding: 50px 0;
}
.container {
  width: 350px;
  margin: auto;
  padding: 20px 40px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  font-family: "Arial", sans-serif;
}

h2 {
  text-align: center;
  font-size: 20px;
  text-transform: uppercase;
  font-weight: bold;
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  font-weight: bold;
  font-size: 14px;
  margin-bottom: 5px;
}

.nhap {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 14px;
  color: #333;
}

button {
  width: 100%;
  margin-top: 20px;
  padding: 10px;
  background: #8aa53f; /* Màu xanh lá nhạt */
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  text-transform: uppercase;
}

button:hover {
  background: #7a9438; /* Màu xanh đậm hơn khi hover */
  color: #fff; /* Giữ nguyên màu chữ */
  border-color: #8bb563;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Hiệu ứng nổi */
}

.login-link {
  text-align: center;
  margin-top: 10px;
  font-size: 14px;
}

.login-link a {
  color: #6b8e23; /* Xanh nhẹ */
  text-decoration: none;
  font-weight: bold;
}

.login-link a:hover {
  text-decoration: underline;
}
.pt {
  font-size: 18px; /* Tăng kích thước chữ */
  font-weight: bold; /* In đậm */

  text-transform: uppercase; /* Chuyển thành chữ in hoa */
  text-align: center; /* Căn giữa */
  margin-top: 10px; /* Tạo khoảng cách với phần trên */
  letter-spacing: 1px; /* Tăng khoảng cách giữa các chữ */
}
.pt a {
  text-decoration: none;
  color: #4a752c; /* Màu xanh lá đậm để hài hòa với nút Đăng ký */
}

  </style>
</html>
