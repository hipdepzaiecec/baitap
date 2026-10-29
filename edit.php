<?php
require 'db.php';
$error = '';

// Lấy thông tin sản phẩm cũ để hiển thị ra form
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM SanPham WHERE MaSP = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        die("Không tìm thấy sản phẩm!");
    }
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $MaSP = $_POST['MaSP'];
    $TenSP = trim($_POST['TenSP']);
    $MaLoai = trim($_POST['MaLoai']);
    $DonGia = $_POST['DonGia'];
    $SoLuongTon = $_POST['SoLuongTon'];

    if (empty($TenSP) || empty($MaLoai) || $DonGia <= 0 || $SoLuongTon < 0) {
        $error = "Dữ liệu không hợp lệ!";
    } else {
        try {
            // Không cho sửa Mã SP (Khóa chính)
            $stmt = $conn->prepare("UPDATE SanPham SET TenSP=?, MaLoai=?, DonGia=?, SoLuongTon=? WHERE MaSP=?");
            $stmt->execute([$TenSP, $MaLoai, $DonGia, $SoLuongTon, $MaSP]);
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Lỗi cập nhật: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>Sửa Sản Phẩm: <?= $product['MaSP'] ?></h2>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <form action="" method="POST" style="max-width: 500px;">
        <input type="hidden" name="MaSP" value="<?= $product['MaSP'] ?>">
        
        <div class="mb-3">
            <label class="form-label">Tên SP:</label>
            <input type="text" name="TenSP" class="form-control" value="<?= htmlspecialchars($product['TenSP']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mã Loại:</label>
            <input type="text" name="MaLoai" class="form-control" value="<?= htmlspecialchars($product['MaLoai']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Đơn Giá:</label>
            <input type="number" name="DonGia" class="form-control" value="<?= $product['DonGia'] ?>" required min="1">
        </div>
        <div class="mb-3">
            <label class="form-label">Số Lượng Tồn:</label>
            <input type="number" name="SoLuongTon" class="form-control" value="<?= $product['SoLuongTon'] ?>" required min="0">
        </div>
        <button type="submit" class="btn btn-primary">Cập Nhật</button>
        <a href="index.php" class="btn btn-secondary">Quay Lại</a>
    </form>
</body>
</html>