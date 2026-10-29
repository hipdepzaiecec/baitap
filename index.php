<?php
require 'db.php';

// 1. Xử lý XÓA (Bắt buộc dùng POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_delete'])) {
    $maSP = $_POST['MaSP'];
    try {
        $stmt = $conn->prepare("DELETE FROM SanPham WHERE MaSP = ?");
        $stmt->execute([$maSP]);
        $success_msg = "Xóa sản phẩm $maSP thành công!";
    } catch (PDOException $e) {
        $error_msg = "Không thể xóa sản phẩm này vì đang có trong đơn hàng!";
    }
}

// 2. Xử lý TÌM KIẾM & PHÂN TRANG
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // Số sản phẩm mỗi trang
$offset = ($page - 1) * $limit;

// Xây dựng câu query động
$sql_where = "";
$params = [];
if ($keyword != '') {
    $sql_where = " WHERE TenSP LIKE :keyword OR MaSP LIKE :keyword";
    $params[':keyword'] = "%$keyword%";
}

// Đếm tổng số bản ghi để tính số trang
$count_stmt = $conn->prepare("SELECT COUNT(*) FROM SanPham" . $sql_where);
$count_stmt->execute($params);
$total_rows = $count_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Lấy dữ liệu sản phẩm
$sql = "SELECT * FROM SanPham" . $sql_where . " ORDER BY MaSP ASC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản Phẩm - MiniShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>Danh sách Sản Phẩm</h2>
    
    <?php if(isset($success_msg)) echo "<div class='alert alert-success'>$success_msg</div>"; ?>
    <?php if(isset($error_msg)) echo "<div class='alert alert-danger'>$error_msg</div>"; ?>

    <div class="d-flex justify-content-between mb-3">
        <a href="create.php" class="btn btn-primary">Thêm Sản Phẩm Mới</a>
        
        <!-- Form Tìm Kiếm -->
        <form action="" method="GET" class="d-flex" style="width: 300px;">
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm mã hoặc tên SP..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" class="btn btn-outline-success">Tìm</button>
        </form>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Mã SP</th>
                <th>Tên SP</th>
                <th>Mã Loại</th>
                <th>Đơn Giá</th>
                <th>Số Lượng Tồn</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['MaSP']) ?></td>
                    <td><?= htmlspecialchars($row['TenSP']) ?></td>
                    <td><?= htmlspecialchars($row['MaLoai']) ?></td>
                    <td><?= number_format($row['DonGia'], 0, ',', '.') ?> VNĐ</td>
                    <td><?= htmlspecialchars($row['SoLuongTon']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['MaSP'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        
                        <!-- Xóa bằng POST (Ẩn dữ liệu) -->
                        <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                            <input type="hidden" name="MaSP" value="<?= $row['MaSP'] ?>">
                            <button type="submit" name="btn_delete" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Không tìm thấy sản phẩm nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Phân Trang -->
    <nav>
        <ul class="pagination">
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</body>
</html>