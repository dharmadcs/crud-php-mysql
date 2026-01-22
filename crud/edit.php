<?php
require_once '../config/database.php';

$error = '';
$product = null;

// Get product ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Get database connection
$database = new Database();
$conn = $database->getConnection();

// Get categories for dropdown
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = intval($_POST['category_id']);
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = trim($_POST['description']);
    
    // Validation
    if ($category_id <= 0 || empty($brand) || empty($model) || $price <= 0 || $stock < 0) {
        $error = 'Semua field yang required harus diisi dengan benar!';
    } else {
        // Get current image
        $current_image_query = $conn->query("SELECT image FROM products WHERE id = $id");
        $current_image_row = $current_image_query->fetch_assoc();
        $image_path = $current_image_row['image'];
        
        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            
            if (!in_array($file_type, $allowed_types)) {
                $error = 'Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.';
            } else if ($file_size > 5 * 1024 * 1024) {
                $error = 'Ukuran file terlalu besar. Maksimal 5MB.';
            } else {
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = uniqid('product_') . '.' . $file_ext;
                $upload_path = '../uploads/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if ($image_path && file_exists('../' . $image_path)) {
                        unlink('../' . $image_path);
                    }
                    $image_path = 'uploads/' . $new_filename;
                } else {
                    $error = 'Gagal mengupload gambar.';
                }
            }
        }
        
        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE products SET category_id=?, brand=?, model=?, price=?, stock=?, description=?, image=? WHERE id=?");
            $stmt->bind_param("issdissi", $category_id, $brand, $model, $price, $stock, $description, $image_path, $id);
            
            if ($stmt->execute()) {
                $stmt->close();
                $database->closeConnection();
                header("Location: index.php?success=updated");
                exit();
            } else {
                $error = 'Gagal mengupdate produk: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Get product data
$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Edit Produk</h1>
            <p>Update informasi produk</p>
        </div>

        <nav class="nav">
            <a href="../index.php">🏠 Dashboard</a>
            <a href="index.php">📦 Semua Produk</a>
            <a href="create.php">➕ Tambah Produk</a>
        </nav>

        <div class="content">
            <div style="max-width: 800px; margin: 0 auto;">
                <h2>Form Edit Produk</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="category_id">Kategori *</label>
                        <select id="category_id" name="category_id" required>
                            <?php 
                            mysqli_data_seek($categories_result, 0);
                            while($cat = $categories_result->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo trim($cat['icon']) . ' ' . htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="brand">Brand / Merek *</label>
                        <input type="text" id="brand" name="brand" required value="<?php echo htmlspecialchars($product['brand']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="model">Model / Tipe *</label>
                        <input type="text" id="model" name="model" required value="<?php echo htmlspecialchars($product['model']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="price">Harga (Rp) *</label>
                        <input type="number" id="price" name="price" required min="0" step="0.01" value="<?php echo $product['price']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="stock">Stok *</label>
                        <input type="number" id="stock" name="stock" required min="0" value="<?php echo $product['stock']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi / Spesifikasi</label>
                        <textarea id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Gambar Saat Ini</label>
                        <?php if ($product['image']): ?>
                            <div style="margin-bottom: 16px;">
                                <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="Current" style="max-width: 200px; border-radius: var(--radius); border: 2px solid var(--border-color);" onclick="openLightbox('../<?php echo htmlspecialchars($product['image']); ?>')">
                            </div>
                        <?php else: ?>
                            <p style="color: var(--text-muted);">Belum ada gambar</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="image">Upload Gambar Baru (Opsional)</label>
                        <div class="file-upload">
                            <label for="image" class="file-upload-label">
                                <div class="upload-icon">📷</div>
                                <div>
                                    <strong>Klik atau drag & drop gambar baru</strong><br>
                                    <span style="color: var(--text-muted); font-size: 0.9em;">JPG, PNG atau WEBP (Maks 5MB)</span>
                                </div>
                            </label>
                            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <div id="imagePreview" class="image-preview" style="display: none;"></div>
                    </div>

                    <div class="form-actions" style="display: flex; gap: 12px; margin-top: 32px;">
                        <button type="submit" class="btn btn-success">💾 Update Produk</button>
                        <a href="index.php" class="btn btn-danger">❌ Batal</a>
                    </div>
                </form>
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
