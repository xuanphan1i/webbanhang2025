<?php
require_once('../config/config.php'); // điều chỉnh đường dẫn nếu cần
session_start(); // Quan trọng để sử dụng session

// ===== XỬ LÝ THÊM GIỎ HÀNG =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_gio'])) {
    $sp_id = (int)$_POST['id'];
    $so_luong = 1;

    if (isset($_SESSION['user_id'])) {
        // Người dùng đã đăng nhập -> lưu vào CSDL
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
        echo "<script>alert('Đã thêm vào giỏ hàng!'); window.location.href = window.location.href;</script>";
        exit;
    } else {
        // Chưa đăng nhập -> lưu vào session
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

        echo "<script>alert('Đã thêm vào giỏ hàng!'); window.location.href = window.location.href;</script>";
        exit;
    }
}


$sql = "SELECT * FROM san_pham ORDER BY RAND() LIMIT 12";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xuan</title>

    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">
   
    <!-- favicon -->
    <script
    defer
    src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"
    integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc"
    crossorigin="anonymous"
  ></script>
     <!-- favicon end -->
</head>
<body>
    <!-- header -->
    <?php require '../includes/T11header.php' ?>
    <!-- main -->
     <main>
        <!-- slider -->
        <div class="slider">
         <div class="sliders slide1">
            <img src="../public/assets/img/main/slider/oar01.jpg"  class="slide" alt="">
            <img src="../public/assets/img/main/slider/oar02.jpg"class="slide"  alt="">
         </div>
         
            <button class="prev" onclick="prevSlide()">❮</button>
            <button class="next" onclick="nextSlide()">❯</button>
        </div>
        <!-- sản phẩm đặc trưng -->
        <div class="san_pham">
         <div class="chu1">Thức ăn sạch</div>
         <div class="chu2">Sản phẩm đặc trưng</div>
         <div class="a-la"><img src="../public/assets/img/main/la2.png" alt=""></div>
            <ul class="ul_sp">
                  <li style="background-color: #91ac41;" class="li_sp"><a  href="sanpham.php?danhmuc=tấtcả " class="tat_ca" style="color: #fbfbfb;" >Tất cả</a></li>
                  <li class="li_sp"><a href="sanpham.php?danhmuc=sinhto "  class="rau">SINH TỐ</a></li>
                  <li class="li_sp"><a href=" sanpham.php?danhmuc=rau" class="nuoc_ep">RAU</a></li>
                  <li class="li_sp"><a href=" sanpham.php?danhmuc=thit" class="thit_bo">THỊT</a></li>
                  <li class="li_sp"><a href=" sanpham.php?danhmuc=traicay" class="thit">TRÁI CÂY</a></li>
            </ul>
        </div>
        <!-- hiển thị 12 sản phẩm -->
          <div id="khung">
            <div class="than">
              <div id="rau" class="sp_dien_hinh">
                <?php while ($sp = mysqli_fetch_assoc($result)): ?>
                  <div class="sp">
                    <a href="chitietSP.php?id=<?= $sp['id'] ?></a>">
                      <img src="<?= htmlspecialchars($sp['hinh_anh']) ?>" alt="<?= htmlspecialchars($sp['ten']) ?>">
                    </a>
                    <div class="sp_chu">
                      <div class="ten"><?= htmlspecialchars($sp['ten']) ?></div>
                      <div class="gia-them">
                          <div class="gia"><?= number_format($sp['gia'], 0, ',', '.') ?> đ</div>

                      <!-- Form thêm giỏ hàng -->
                      <form method="post" style="display:inline;">
                          <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                          <input type="hidden" name="ten" value="<?= $sp['ten'] ?>">
                          <input type="hidden" name="gia" value="<?= $sp['gia'] ?>">
                          <input type="hidden" name="hinh_anh" value="<?= $sp['hinh_anh'] ?>">
                          <button type="submit" name="them_gio" class="nut-them-gio" title="Thêm vào giỏ hàng">+</button>
                      </form>
                    </div>
                  </div>

                  </div>
                <?php endwhile; ?>
              </div>
            </div>
          </div>

        <!-- ưu đãi giảm giá  -->
        <div class="uu_dai" style="margin-top: 90px;">
            <div class="giam_gia_soc">
                  <div class="giam1">Giảm giá sốc!</div>
                  <div class="giam2">Cửa hàng Oars Ưu đãi giảm giá 50% khi mua tại cửa hàng</div>
                  <img src="../public/assets/img/main/la2.png" alt="">
                  <div class="xem_ngay"><a href=" "
                  class="a_xem"></a></div>
            </div>
            <div class="anh_gg">
                  <img src="../public/assets/img/main/nengiamgia.jpg" alt="">
            </div>
        </div> 
        <!-- giới thiệu -->
<div class="gioi_thieu">
  <div class="chu1">Về chúng tôi</div>
  <div class="chu2">Chúng tôi là ai</div>
  <div class="a-la">
    <img src="../public/assets/img/main/la2.png" alt="">
  </div>
  <div class="chu3">
    Chúng tôi là những người mang đến thực phẩm sạch, an toàn và bền vững từ thiên nhiên.
  </div>
  
  <ul class="ul_gt">
    <li class="li_gt">
      <img src="../public/assets/img/main/tron-1.png" alt="">
      <div class="chudam">Trang trại tự nhiên</div>
      <div class="gach"></div>
      <div class="chu">
        Trang trại, tự nhiên, hữu cơ, bền vững, sạch, an toàn, sinh thái, tuần hoàn, phát triển, xanh.
      </div>
    </li>

    <li class="li_gt">
      <img src="../public/assets/img/main/tron2.png" alt="">
      <div class="chudam">Thực phẩm tốt cho sức khỏe</div>
      <div class="gach"></div>
      <div class="chu">
        Hữu cơ, tươi sạch, dinh dưỡng, an toàn, tự nhiên, ít chế biến, lành mạnh, cân bằng, giàu vitamin, bổ dưỡng.
      </div>
    </li>

    <li class="li_gt">
      <img src="../public/assets/img/main/tron3.png" alt="">
      <div class="chudam">Bảo tồn đa dạng sinh học</div>
      <div class="gach"></div>
      <div class="chu">
        Sinh thái, tự nhiên, bảo vệ, cân bằng, môi trường, hệ sinh thái, bền vững, phát triển, động thực vật, gen quý.
      </div>
    </li>

    <li class="li_gt">
      <img src="../public/assets/img/main/tron4.png" alt="">
      <div class="chudam">Kiểm soát môi trường</div>
      <div class="gach"></div>
      <div class="chu">
        Kiểm soát, phòng ngừa, bảo vệ, vi sinh, môi trường, dịch bệnh, an toàn, sinh thái, sức khỏe, bền vững.
      </div>
    </li>
  </ul>
</div>

        <!-- blog -->
        <div class="gioi_thieu">
            <div class="chu1">Bài viết nổi bật</div>
            <div class="chu2">Cập nhật gần đây</div>
            <div class="a-la"><img src="../public/assets/img/main/la2.png" alt=""></div>
            <div class="chu3">Chúng tôi cung cấp thực phẩm sạch tươi ngon mỗi ngày, mang đến bữa ăn dinh dưỡng và sức khỏe cho gia đình bạn</div>
            <div class="main-blog">
               <div class="blog">
                  <div class="blog-1"><a href="https://www.vinmec.com/vie/bai-viet/rau-la-mau-xanh-tot-cho-nao-bo-vi">
                  <img src="../public/assets/img/main/blog/blog1.jpg" alt="">
                  <div class="ten-blog"><a href="https://www.vinmec.com/vie/bai-viet/rau-la-mau-xanh-tot-cho-nao-bo-vi">Rau ăn lá màu xanh tốt cho não bộ?</a></div>
                     <div class="them"><a href="https://www.vinmec.com/vie/bai-viet/rau-la-mau-xanh-tot-cho-nao-bo-vi">XEM</a></div></a>
                  </div>
                  <div class="blog-1"><a href="https://www.vinmec.com/vie/bai-viet/cach-giu-vitamin-trong-cac-loai-rau-cu-vi">
                     <img src="../public/assets/img/main/blog/blog2.jpg" alt="">
                     <div class="ten-blog"><a href="https://www.vinmec.com/vie/bai-viet/cach-giu-vitamin-trong-cac-loai-rau-cu-vi">Cách giữ vitamin trong các loại rau củ</a> </div>
                     <div class="them"><a href="https://www.vinmec.com/vie/bai-viet/cach-giu-vitamin-trong-cac-loai-rau-cu-vi">XEM</a></div></a>
                  </div>
                  <div class="blog-1"><a href="https://www.vinmec.com/vie/bai-viet/cac-loai-rau-cu-mau-vang-va-loi-ich-cho-suc-khoe-vi">
                  <img src="../public/assets/img/main/blog/blog3.jpg" alt="">
                  <div class="ten-blog"><a href="https://www.vinmec.com/vie/bai-viet/cac-loai-rau-cu-mau-vang-va-loi-ich-cho-suc-khoe-vi">Các loại rau củ màu vàng và lợi ích cho sức khỏe</a></div>
                  <div class="them"><a href="https://www.vinmec.com/vie/bai-viet/cac-loai-rau-cu-mau-vang-va-loi-ich-cho-suc-khoe-vi">XEM</a></div>
                  </a>
                  </div>
               </div>

            </div>
        </div>
     </main>

    <!-- footer -->
    <?php require '../includes/footer.php' ?>
     <script src="../public/banner.js"></script>
    
     <style>
      /* phần giới thiệu  */
      .gia-them form {
    display: inline-block;
}

.gioi_thieu {
  text-align: center ;
}
.gia-them {
    display: flex;
    justify-content: space-between;
    align-items: center;
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

.san_pham{
   text-align: center;
}
.chu1 {
  color: #91ac41  ;
  font-size: 40px ;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
  padding-top: 40px  ;
}
.chu2 {
  font-size: 60px;
  margin-top: 20px;
}
.a-la {
  padding-bottom: 20px;
}
.chu3 {
  opacity: 0.5;
  font-weight: bold;
  padding-bottom: 40px;
}
.ul_gt {
  display: flex;
  justify-content: space-around;
}
.li_gt > img {
  padding-bottom: 25px;
}
.li_gt img {
  transition: all 0.5s ease-in-out;
}
.li_gt:hover img {
  transform: scale(1.1);
}
.chudam {
  font-weight: bold;
  font-size: 18px;
  padding: 10px;
}

.ul_gt .li_gt .gach {
  width: 48px;
  height: 2px;
  margin-left: 130px;
  margin-bottom: 20px;
  background-color: #91ac41;
}
.chu {
  opacity: 0.65;
}
.li_gt .chu {
  width: 80%;
  margin-left: 40px;
  line-height: 1.5;
} 
/* ================================== sp điển hình======================*/
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
  background-color: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 0 5px rgba(0,0,0,0.1);
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

/* giảm giá  */
.uu_dai {
  display: flex;
  background-color: #e5eef0;
}
.uu_dai .giam_gia_soc {
  width: 50%;
  text-align: center;
}
.giam_gia_soc {
  text-align: left;
  padding-left: 60px;
  padding-right: 60px;
  padding-top: 70px;
}
.giam1 {
  color: #91ac41;
  font-size: 30px;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
  padding-top: 40px;
}
.giam2 {
  font-size: 40px;
  margin-top: 10px;
  padding-right: 30px;
  color: #413e3e;
}
.giam_gia_soc > img {
  padding-top: 20px;
  padding-bottom: 40px;
}
.anh_gg {
  margin: 20px;
}

.anh_gg > img {
  border: 3px solid white;
  background-size: cover;
  border-radius: 50px;
}
.a_xem:hover {
  background-color: #d0f562;
  color: crimson;
}
.xem_ngay a {
  font-size: 25px;
  color: aliceblue;
  background-color: #91ac41;
}
/* blog============== */
.gioi_thieu .main-blog {
  background-color: #f7eee2;
}
.blog {
  display: flex;
  flex-wrap: wrap;
  margin: 20px 0px;
  padding: 40px 30px;
  justify-content: space-around;
}
.blog-1 {
  width: 30%;
  margin-bottom: 20px;
  height: 450px;
  background-color: #ffffff;

  margin-bottom: 30px;
}
.blog .blog-1:hover {
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); /* Đổ bóng khi hover */
}
.blog-1 img {
  width: 100%;
  height: 280px;
  /* border: 1px solid #91ac41; */
  background-size: cover;
  margin-bottom: 30px;
}
.blog-1 a .ten-blog {
  font-size: 14px;
  color: #1e1e1e;
  font-weight: 600;
}
.them {
  margin-top: 25px;
}
.them a {
  color: #ffffff;
  background-color:#91ad41;
  padding: 15px 40px;
  border-radius: 25px;

  text-align: center;
  align-items: center;
}
.ten-blog a {
  font-size: 14px;
  color: #1e1e1e;
  font-weight: 600;
  font-style: italic;
}
.ten-blog a:hover {
  color:  #df1e31;
}
.them a:hover {
  background-color: #df1e31;
}
/* main .san_pham */
.ul_sp {
  display: flex;
  text-transform: capitalize;
  margin: 10px;
  margin-left: 330px;
}
.san_pham .ul_sp .li_sp {
  padding: 10px 20px;
  margin: 0px 10px;
  border: 1px solid #91ac41;
  border-radius: 20px;
}

.san_pham .ul_sp .li_sp > a {
  color: rgb(81, 79, 79);

  font-weight: 600;
}
.san_pham .ul_sp .li_sp:hover {
  background-color: #91ac41;
}
.san_pham .ul_sp .li_sp a:hover {
  color: azure;
}
     </style>
</body>
</html>
