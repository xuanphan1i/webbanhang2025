<?php
require_once '../config/config.php'; // hoặc đường dẫn tới file kết nối CSDL

if (isset($_GET['tukhoa'])) {
    $tukhoa = trim($_GET['tukhoa']);
    $sql = "SELECT * FROM san_pham WHERE ten LIKE ? LIMIT 5";
    $stmt = $conn->prepare($sql);
    $tk = "%" . $tukhoa . "%";
    $stmt->bind_param("s", $tk);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo '<div class="item-timkiem">';
        echo '<a href="../public/chitietSP.php?id=' . $row['id'] . '">' . htmlspecialchars($row['ten']) . '</a>';
        echo '</div>';
    }

    if ($result->num_rows === 0) {
        echo '<div class="item-timkiem">Không tìm thấy sản phẩm</div>';
    }
}
?>
