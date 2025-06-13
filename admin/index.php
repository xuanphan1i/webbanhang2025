
<!-- bỏ phần phải đăng nhập đúng admin mới vào được chứ ko thể vào bằng đường link http://localhost:8080/webbanhang/admin/index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../admin/css/styleAdmin.css ?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">

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
                        <li class="li1"><a href="../public/index.php">VỀ TRANG KHÁCH</a></li>
                        <li class="li1"><a href="../admin/QLNguoiDung.php">QUẢN LÝ NGƯỜI DÙNG</a></li>
                        <li class="li1"><a href="../admin/QLSP.php">QUẢN LÝ SẢN PHẨM</a></li>
                        <li class="li1 limn"><a href="../admin/QLDon.php">QUẢN LÝ ĐƠN</a>                      
                        </li>
                    </ul>
                </div>
            </div>
            <!-- right -->
            <div class="right-main">
                <div class="right">                   
                   
                    <div class="icon"> <a href="../public/logout.php" title="Đăng xuất"><i class="fas fa-sign-out-alt"></i></a>
                    <!-- Icon đăng xuất -->
                        <!-- <a href="#" id="icon-dangxuat" style="display: none;" onclick="confirmLogout(event)">
                            <i class="fas fa-sign-out-alt"></i>
                        </a> -->
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </header>
    <div class="thua" ></div>
    <!-- phần js cho phần đăng xuất -->
   
    <!-- slider -->
            <div class="slider">
            <div class="sliders slide1">
                <img src="../public/assets/img/main/slider/oar02.jpg"class="slide"  alt="">
            </div>
            </div>

<?php require '../includes/footer.php'?>
</body>
</html>