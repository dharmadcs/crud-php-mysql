<?php
require_once '../config/database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

// Get filter parameters
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$stock_filter = isset($_GET['stock']) ? $_GET['stock'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'created_at_desc';

// Build query
$query = "SELECT p.*, c.name as category_name, c.icon as category_icon, c.color as category_color 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE 1=1";

if ($category_id > 0) {
    $query .= " AND p.category_id = $category_id";
}

if ($stock_filter == 'low') {
    $query .= " AND p.stock <= 10";
}

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $query .= " AND (p.brand LIKE '%$search_safe%' OR p.model LIKE '%$search_safe%')";
}

switch ($sort_by) {
    case 'brand_asc':
        $query .= " ORDER BY p.brand ASC, p.model ASC";
        break;
    case 'brand_desc':
        $query .= " ORDER BY p.brand DESC, p.model DESC";
        break;
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'stock_asc':
        $query .= " ORDER BY p.stock ASC";
        break;
    case 'stock_desc':
        $query .= " ORDER BY p.stock DESC";
        break;
    case 'created_at_asc':
        $query .= " ORDER BY p.created_at ASC";
        break;
    case 'created_at_desc':
    default:
        $query .= " ORDER BY p.created_at DESC";
        break;
}

$result = $conn->query($query);

// Get all categories for filter
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📦 Daftar Produk</h1>
            <p>Kelola semua produk Anda</p>
        </div>

        <nav class="nav">
            <a href="../index.php">🏠 Dashboard</a>
            <a href="index.php" class="active">📦 Semua Produk</a>
            <a href="create.php">➕ Tambah Produk</a>
        </nav>

        <div class="content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                <h2>Data Produk</h2>
                <a href="create.php" class="btn btn-primary">➕ Tambah Produk</a>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    if ($_GET['success'] == 'created') echo '✅ Produk berhasil ditambahkan!';
                    else if ($_GET['success'] == 'updated') echo '✅ Produk berhasil diupdate!';
                    else if ($_GET['success'] == 'deleted') echo '✅ Produk berhasil dihapus!';
                    ?>
                </div>
            <?php endif; ?>

            <!-- Search and Filter -->
            <div class="search-container">
                <input type="text" id="search" placeholder="🔍 Cari brand/model..." value="<?php echo htmlspecialchars($search); ?>" onkeyup="filterProducts()">

                <select id="categoryFilter" onchange="filterProducts()">
                    <option value="">Semua Kategori</option>
                    <?php
                    mysqli_data_seek($categories_result, 0);
                    while ($cat = $categories_result->fetch_assoc()):
                    ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo trim($cat['icon']) . ' ' . htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select id="sortFilter" onchange="filterProducts()">
                    <option value="created_at_desc" <?php echo ($sort_by == 'created_at_desc') ? 'selected' : ''; ?>>🕒 Terbaru</option>
                    <option value="created_at_asc" <?php echo ($sort_by == 'created_at_asc') ? 'selected' : ''; ?>>🕒 Terlama</option>
                    <option value="brand_asc" <?php echo ($sort_by == 'brand_asc') ? 'selected' : ''; ?>>📝 Brand A-Z</option>
                    <option value="brand_desc" <?php echo ($sort_by == 'brand_desc') ? 'selected' : ''; ?>>📝 Brand Z-A</option>
                    <option value="price_asc" <?php echo ($sort_by == 'price_asc') ? 'selected' : ''; ?>>💰 Harga Rendah</option>
                    <option value="price_desc" <?php echo ($sort_by == 'price_desc') ? 'selected' : ''; ?>>💰 Harga Tinggi</option>
                    <option value="stock_asc" <?php echo ($sort_by == 'stock_asc') ? 'selected' : ''; ?>>📦 Stok Sedikit</option>
                    <option value="stock_desc" <?php echo ($sort_by == 'stock_desc') ? 'selected' : ''; ?>>📦 Stok Banyak</option>
                </select>

                <a href="index.php" class="btn btn-warning btn-small">🔄 Reset Filter</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Kategori</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td>
                                        <?php if ($row['image']): ?>
                                            <img src="../<?php echo htmlspecialchars($row['image']); ?>"
                                                alt="<?php echo htmlspecialchars($row['model']); ?>"
                                                class="product-image"
                                                onclick="openLightbox('../<?php echo htmlspecialchars($row['image']); ?>')">
                                        <?php else: ?>
                                            <div class="no-image">📷</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="category-badge" style="background: <?php echo $row['category_color']; ?>15; color: <?php echo $row['category_color']; ?>; border-color: <?php echo $row['category_color']; ?>;">
                                            <?php echo trim($row['category_icon']) . ' ' . htmlspecialchars($row['category_name']); ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($row['brand']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['model']); ?></td>
                                    <td class="price">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php
                                        $stock = $row['stock'];
                                        $badge_class = 'stock-low';
                                        if ($stock > 15) $badge_class = 'stock-high';
                                        else if ($stock > 5) $badge_class = 'stock-medium';
                                        ?>
                                        <span class="stock-badge <?php echo $badge_class; ?>"><?php echo $stock; ?> unit</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-small">👁️ Detail</a>
                                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-small">✏️ Edit</a>
                                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-small" onclick="event.preventDefault(); confirmDelete('<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>').then(result => { if(result) window.location.href = this.href; });">🗑️ Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 60px;">
                                    <div style="font-size: 3em; margin-bottom: 16px;">📦</div>
                                    <p style="color: var(--text-muted); font-size: 1.2em;">Belum ada produk. <a href="create.php" style="color: var(--primary);">Tambah sekarang</a></p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2026 LAB 5 Quiz Project V2 - Multi-Product Management System</p>
        </div>
    </div>

    <script src="../assets/script.js"></script>
</body>

</html>
<?php
$database->closeConnection();
?>