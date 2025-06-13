<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Liên hệ</title>
    <link rel="icon" type="image/png" href="../public/assets/img/favicon/android-chrome-512x512.png">
  </head>
  <body>
    <?php require '../includes/T11header.php' ?>
    <div class="tieu_de">
      <h1 class="gth1">LIÊN HỆ VỚI CHÚNG TÔI</h1>
    </div>
    >

    <!-- Biểu mẫu liên hệ -->
    <section class="lien_he">
        <div class="anhlh"><img src="../public/assets/img/main/lienHe/lh.jpg" alt=""></div>
      <div class="container">
        <h2>Biểu mẫu liên hệ</h2>
        <p>Vui lòng điền thông tin để chúng tôi hỗ trợ bạn.</p>
        <form class="form_lien_he">
          <input type="text" placeholder="Tên của bạn" required />
          <input type="email" placeholder="Thư điện tử" required />
          <input type="tel" placeholder="Số điện thoại" required />
          <textarea placeholder="Nội dung tin nhắn" required></textarea>
          <button type="submit">Gửi</button>
        </form>
      </div>
    </section>

    <!-- Thông tin liên hệ -->
    <section class="thong_tin">
      <div class="container">
        <div class="dia_chi">
          <p>📍 Trường Cao đẳng Công nghệ Bách khoa Hà Nội - Cơ sở Thanh Trì</p>
        </div>
        <div class="dien_thoai">
          <p>📞 01122000</p>
        </div>
        <div class="email">
          <p>✉ phanthixuan168@gmail.com</p>
        </div>
      </div>
    </section>

    <!-- Bản đồ -->
    <section class="ban_do">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.799830960821!2d105.87066357512776!3d21.04148768751157!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135abc67a8fbe9d%3A0xf0195d646144bb92!2zVHLGsOG7nW5nIENhbyDEkOG6s25nIENvbmcgTmdo4buHIEJhY2ggaG9hIEjDoCBOb8OgSSAtIEPGoSBz4buRYyBUaGFuaCBUcsOs!5e0!3m2!1svi!2s!4v1710748801234"
        width="100%"
        height="800"
      ></iframe>
    </section>
    <?php require '../includes/footer.php' ?>
  </body>
  <style>
    /* Cài đặt chung */
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

/* Header */
.tieu_de {
  text-align: center; /* Căn giữa nội dung theo chiều ngang */
  display: flex;
  flex-direction: column;
  align-items: center; /* Căn giữa theo trục ngang */
  justify-content: center; /* Căn giữa theo trục dọc */
  height: 300px;
  background-image: url(../public/assets/img/main/lienHe/banner.png);
}

.gth1 {
  font-size: 24px;
  font-weight: bold;
  margin: 0;
  color: #2c3e50; /* Màu chữ (có thể chỉnh sửa) */
}

.gtp {
  margin-top: 5px;
}

.gtp a {
  text-decoration: none;
  color: #91ac41; /* Màu chữ liên kết */
  font-weight: bold;
}

/* Biểu mẫu liên hệ */
.lien_he {
  text-align: center;
  padding: 40px;
}

.form_lien_he {
  display: flex;
  flex-direction: column;
  max-width: 400px;
  margin: auto;
}

.form_lien_he input,
.form_lien_he textarea {
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
}

.form_lien_he button {
  background: #91ac41;
  color: white;
  padding: 10px;
  border: none;
  cursor: pointer;
}

/* Thông tin liên hệ */
.thong_tin {
  margin-top: 50px;
  display: flex;
  justify-content: space-around;
  padding: 20px;
  background: #f7eee2;
}
.lien_he {
  display: flex;
}
.container {
  margin-left: 150px;
  margin-top: 100px;
}
/* Bản đồ */
.ban_do {
  padding: 20px;
}

  </style>
</html>
