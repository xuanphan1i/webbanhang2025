<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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
                    
                    <div class="icon"><a href=""><i class="fas fa-cart-plus"></i></a></div>
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