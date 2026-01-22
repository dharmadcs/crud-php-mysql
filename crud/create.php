<?php
require_once '../config/database.php';

$error = '';
$success = '';

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
        // Handle image upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            
            if (!in_array($file_type, $allowed_types)) {
                $error = 'Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.';
            } else if ($file_size > 5 * 1024 * 1024) { // 5MB max
                $error = 'Ukuran file terlalu besar. Maksimal 5MB.';
            } else {
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = uniqid('product_') . '.' . $file_ext;
                $upload_path = '../uploads/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'uploads/' . $new_filename;
                } else {
                    $error = 'Gagal mengupload gambar.';
                }
            }
        }
        
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO products (category_id, brand, model, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdi ss", $category_id, $brand, $model, $price, $stock, $description, $image_path);
            
            if ($stmt->execute()) {
                $stmt->close();
                $database->closeConnection();
                header("Location: index.php?success=created");
                exit();
            } else {
                $error = 'Gagal menambahkan produk: ' . $conn->error;
                // Delete uploaded image if database insert fails
                if ($image_path && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>➕ Tambah Produk Baru</h1>
            <p>Isi form di bawah untuk menambahkan produk</p>
        </div>

        <nav class="nav">
            <a href="../index.php">🏠 Dashboard</a>
            <a href="index.php">📦 Semua Produk</a>
            <a href="create.php" class="active">➕ Tambah Produk</a>
        </nav>

        <div class="content">
            <div style="max-width: 800px; margin: 0 auto;">
                <h2>Form Tambah Produk</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="category_id">Kategori *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php 
                            mysqli_data_seek($categories_result, 0);
                            while($cat = $categories_result->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo trim($cat['icon']) . ' ' . htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="brand">Brand / Merek *</label>
                        <input type="text" id="brand" name="brand" required placeholder="Contoh: Samsung, Apple, ASUS" value="<?php echo isset($_POST['brand']) ? htmlspecialchars($_POST['brand']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="model">Model / Tipe *</label>
                        <input type="text" id="model" name="model" required placeholder="Contoh: Galaxy S23 Ultra, iPhone 15 Pro" value="<?php echo isset($_POST['model']) ? htmlspecialchars($_POST['model']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="price">Harga (Rp) *</label>
                        <input type="number" id="price" name="price" required min="0" step="0.01" placeholder="Contoh: 15999000" value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="stock">Stok *</label>
                        <input type="number" id="stock" name="stock" required min="0" placeholder="Contoh: 10" value="<?php echo isset($_POST['stock']) ? $_POST['stock'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi / Spesifikasi</label>
                        <textarea id="description" name="description" placeholder="Masukkan deskripsi atau spesifikasi produk..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Upload Gambar Produk</label>
                        <div class="file-upload">
                            <label for="image" class="file-upload-label">
                                <div class="upload-icon">📷</div>
                                <div>
                                    <strong>Klik atau drag & drop gambar</strong><br>
                                    <span style="color: var(--text-muted); font-size: 0.9em;">JPG, PNG atau WEBP (Maks 5MB)</span>
                                </div>
                            </label>
                            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <div id="imagePreview" class="image-preview" style="display: none;"></div>
                    </div>

                    <div class="form-actions" style="display: flex; gap: 12px; margin-top: 32px;">
                        <button type="submit" class="btn btn-success">💾 Simpan Produk</button>
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
