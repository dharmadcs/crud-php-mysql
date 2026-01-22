<?php
require_once '../config/database.php';

// Get product ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Get database connection
$database = new Database();
$conn = $database->getConnection();

// Get product data
$stmt = $conn->prepare("SELECT p.*, c.name as category_name, c.icon as category_icon, c.color as category_color FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt->close();
    $database->closeConnection();
    header("Location: index.php");
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// Determine stock badge
$stock = $product['stock'];
$stock_badge_class = 'stock-low';
$stock_status = 'Stok Rendah';
if ($stock > 15) {
    $stock_badge_class = 'stock-high';
    $stock_status = 'Stok Baik';
} else if ($stock > 5) {
    $stock_badge_class = 'stock-medium';
    $stock_status = 'Stok Sedang';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - <?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👁️ Detail Produk</h1>
            <p>Informasi lengkap produk</p>
        </div>

        <nav class="nav">
            <a href="../index.php">🏠 Dashboard</a>
            <a href="index.php">📦 Semua Produk</a>
            <a href="create.php">➕ Tambah Produk</a>
        </nav>

        <div class="content">
            <div style="max-width: 900px; margin: 0 auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
                    <h2>Informasi Produk</h2>
                    <div class="action-buttons">
                        <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">✏️ Edit</a>
                        <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="event.preventDefault(); confirmDelete('<?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>').then(result => { if(result) window.location.href = this.href; });">🗑️ Hapus</a>
                        <a href="index.php" class="btn btn-primary">⬅️ Kembali</a>
                    </div>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow);">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
                        <!-- Image -->
                        <div>
                            <?php if ($product['image']): ?>
                                <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['model']); ?>" 
                                     style="width: 100%; border-radius: var(--radius); border: 2px solid var(--border-color); cursor: pointer;"
                                     onclick="openLightbox('../<?php echo htmlspecialchars($product['image']); ?>')">
                            <?php else: ?>
                                <div style="width: 100%; height: 400px; background: var(--bg-tertiary); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; font-size: 4em; border: 2px solid var(--border-color);">
                                    📷
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Info -->
                        <div>
                            <div style="margin-bottom: 24px;">
                                <span class="category-badge" style="background: <?php echo $product['category_color']; ?>15; color: <?php echo $product['category_color']; ?>; border-color: <?php echo $product['category_color']; ?>;">
                                    <?php echo trim($product['category_icon']) . ' ' . htmlspecialchars($product['category_name']); ?>
                                </span>
                            </div>

                            <h1 style="font-size: 2.5em; margin-bottom: 8px; line-height: 1.2;">
                                <?php echo htmlspecialchars($product['brand']); ?>
                            </h1>
                            <h2 style="font-size: 1.8em; color: var(--text-secondary); margin-bottom: 24px; font-weight: 400;">
                                <?php echo htmlspecialchars($product['model']); ?>
                            </h2>

                            <div style="margin-bottom: 24px;">
                                <div style="font-size: 0.9em; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">Harga</div>
                                <div class="price" style="font-size: 2.5em; font-weight: 800;">
                                    Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                </div>
                            </div>

                            <div style="margin-bottom: 24px;">
                                <div style="font-size: 0.9em; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">Stok</div>
                                <div>
                                    <span class="stock-badge <?php echo $stock_badge_class; ?>" style="font-size: 1.2em; padding: 10px 20px;">
                                        <?php echo $stock; ?> Unit - <?php echo $stock_status; ?>
                                    </span>
                                </div>
                            </div>

                            <div style="margin-bottom: 24px;">
                                <div style="font-size: 0.9em; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">Total Nilai Stok</div>
                                <div style="font-size: 1.5em; font-weight: 700; color: var(--primary);">
                                    Rp <?php echo number_format($product['price'] * $product['stock'], 0, ',', '.'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($product['description'])): ?>
                        <div style="border-top: 1px solid var(--border-color); padding-top: 24px;">
                            <h3 style="font-size: 1.3em; margin-bottom: 16px;">📝 Deskripsi & Spesifikasi</h3>
                            <p style="color: var(--text-secondary); line-height: 1.8; white-space: pre-wrap;">
                                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 24px; margin-top: 24px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 0.9em; color: var(--text-muted);">
                            <div>
                                <strong>ID Produk:</strong> #<?php echo $product['id']; ?>
                            </div>
                            <div>
                                <strong>Ditambahkan:</strong> <?php echo date('d M Y, H:i', strtotime($product['created_at'])); ?>
                            </div>
                            <div>
                                <strong>Diupdate:</strong> <?php echo date('d M Y, H:i', strtotime($product['updated_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
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
