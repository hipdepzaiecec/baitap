<?php
require 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $MaSP = trim($_POST['MaSP']);
    $TenSP = trim($_POST['TenSP']);
    $MaLoai = trim($_POST['MaLoai']);
    $DonGia = $_POST['DonGia'];
    $SoLuongTon = $_POST['SoLuongTon'];

    // Validate cơ bản
    if (empty($MaSP) || empty($TenSP) || empty($MaLoai) || $DonGia <= 0 || $SoLuongTon < 0) {
        $error = "Vui lòng nhập đầy đủ thông tin hợp lệ (Đơn giá > 0, Số lượng >= 0).";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO SanPham (MaSP, TenSP, MaLoai, DonGia, SoLuongTon) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$MaSP, $TenSP, $MaLoai, $DonGia, $SoLuongTon]);
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Lỗi: Mã sản phẩm có thể đã tồn tại hoặc Mã loại không hợp lệ.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>Thêm Sản Phẩm Mới</h2>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <form action="" method="POST" style="max-width: 500px;">
        <div class="mb-3">
            <label class="form-label">Mã SP:</label>
            <input type="text" name="MaSP" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tên SP:</label>
            <input type="text" name="TenSP" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mã Loại (VD: L01, L02...):</label>
            <input type="text" name="MaLoai" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Đơn Giá:</label>
            <input type="number" name="DonGia" class="form-control" required min="1">
        </div>
        <div class="mb-3">
            <label class="form-label">Số Lượng Tồn:</label>
            <input type="number" name="SoLuongTon" class="form-control" required min="0">
        </div>
        <button type="submit" class="btn btn-success">Lưu Sản Phẩm</button>
        <a href="index.php" class="btn btn-secondary">Quay Lại</a>
    </form>
</body>
</html>