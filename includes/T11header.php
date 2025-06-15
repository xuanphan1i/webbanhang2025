<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// số lương koaij mặt hàng có trong giỏ
$so_loai_sp = isset($_SESSION['giohang']) ? count($_SESSION['giohang']) : 0;
// $so_don = 0;
// if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'nguoidung' && isset($_SESSION['user_id'])) {
//     require_once '../config/config.php'; // đảm bảo đã kết nối CSDL

//     $user_id = $_SESSION['user_id'];
//     $sql = "SELECT COUNT(*) AS tong FROM don_hang WHERE khach_hang_id = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $user_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $row = $result->fetch_assoc();
//     $so_don = $row['tong'];
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="../includes/layout.css ?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">
   
    <!-- js ảnh banner begin -->
    <script
    defer
    src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"
    integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc"
    crossorigin="anonymous"
  ></script>
     <!-- js ảnh banner end -->
</head>
<body class="h1" >
    <header >
        <div class="header-container">
            <!-- logo -->
            <div class="logo">
                <a href=""><img class="img" src="../public/assets/img/header/logo2.jpg" alt=""></a>
            </div>
            <!-- menu -->
            <div class="menu-main"> 
                <div class="menu">
                    <ul class="ul1">
                        <!--hiện thêm li admin khi admin đăng nhập  -->
                         <?php 
                            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') 
                        {
                                echo '<li class="li1"><a href="../admin/index.php">ADMIN</a></li>';
                            }
                            ?>
                        


                        <li class="li1"><a href="../public/index.php">TRANG CHỦ</a></li>
                        <li class="li1"><a href="../public/gioithieu.php">GIỚI THIỆU</a></li>
                        <li class="li1 limn"><a href="../public/sanpham.php">SẢN PHẨM</a>
                        <!-- menu cấp 2 -->
                         <ul class="ul2">
                            <li class="li2"><a href="sanpham.php?danhmuc=sinhto">SINH TỐ</a></li>
                            <li class="li2"><a href="sanpham.php?danhmuc=rau">RAU</a></li>
                            <li class="li2"><a href="sanpham.php?danhmuc=thit">THỊT</a></li>
                            <li class="li2 limn2"><a href="sanpham.php?danhmuc=traicay">TRÁI CÂY</a>
                                <!-- menu cấp 3 -->
                                 <!-- <ul class="ul3">
                                    <li class="li3"><a href="">LỰU</a></li>
                                    <li class="li3"><a href="">ĐÀO</a></li>
                                    <li class="li3 limn3"><a href="">CÀ CHUA</a>
                                       có thể làm thêm menu cấp 4,5...
                                        
                                    </li>
                                 </ul> -->
                            </li>
                         </ul>
                        </li>
                        <li class="li1"><a href="../public/lienhe.php">LIÊN HỆ</a></li>
                    </ul>
                </div>
            </div>
            <!-- right -->
            <div class="right-main">
                <div class="right">
                    <div class="baotk icon">
                        <div class="timkiem1">
                            <input type="text" placeholder="Tìm kiếm..." class="nuttk">                
                            
                            <div class="nut1"> <a href="" ><i class="fas fa-search "></i></a></div>
                        </div>
                    </div>
                    
                    <div class="icon icon-cart"><a href="../public/giohang.php"><i class="fas fa-cart-plus"></i>
                    
                        <?php if ($so_loai_sp > 0): ?>
                        <span class="cart-count"><?= $so_loai_sp ?></span>
                        <?php endif; ?>
                    </a></div>
                    <!-- Icon đơn hàng của tôi (chỉ hiện khi đã đăng nhập với vai trò người dùng) -->
                    <?php 
                        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'nguoidung') {
                            
                            echo '
                            <div class="icon icon-orders">
                                <a href="../public/donhang_cua_toi.php" title="Đơn hàng của tôi">
                                    <i class=" icon fas fa-box"></i>
                                </a>
                            </div>';
                        }
                    ?>
                    <!-- <div class="icon"><a href=""><i class="fas fa-heart"></i></a></div> -->
                    <!-- đăng nhập đăng xuất  -->
                    <div class="icon"> 
                        <?php
                            if (session_status() === PHP_SESSION_NONE) {
                                session_start();
                            }

                            if (isset($_SESSION['user_id'])) {
                                // Đã đăng nhập, hiện icon đăng xuất
                                echo '<a href="../public/logout.php" title="Đăng xuất"><i class="fas fa-sign-out-alt"></i></a>';
                            } else {
                                // Chưa đăng nhập, hiện icon người dùng
                                echo '<a href="../public/login.php" title="Đăng nhập"><i class="fas fa-user"></i></a>';
                            }
                            ?>  
                    </div>
                    
                </div>
            </div>
        </div>
    </header>
   



</body>
</html>