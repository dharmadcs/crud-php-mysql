# 🏪 Sistem Manajemen Produk Multi-Kategori

Aplikasi web PHP modern untuk mengelola inventory produk multi-kategori dengan fitur CRUD lengkap, dashboard analitik, dan interface yang user-friendly.

## 🎯 Fitur Utama

### ✅ Core Features
- **Dashboard Analytics**: Statistik penjualan, total produk, stok, dan nilai inventory
- **Multi-Product Management**: Mendukung berbagai kategori produk (Handphone, Laptop, Tablet, dll.)
- **Advanced CRUD Operations**: Create, Read, Update, Delete untuk semua produk
- **Smart Search & Filter**: Pencarian real-time dan filter berdasarkan kategori
- **Flexible Sorting**: Urutkan produk berdasarkan brand, harga, stok, atau tanggal
- **Image Upload**: Upload dan preview gambar produk dengan drag & drop
- **Stock Management**: Color-coded stock indicators dengan alert stok rendah

### 🎨 User Interface
- **Modern Design**: Gradient backgrounds dengan animasi smooth
- **Responsive Layout**: Optimal di desktop, tablet, dan mobile
- **Interactive Elements**: Hover effects, modal confirmations, lightbox images
- **Color-coded Status**: Visual indicators untuk stok dan status produk

### 🔒 Security & Performance
- **Prepared Statements**: Proteksi SQL injection
- **Input Validation**: Validasi form client-side dan server-side
- **UTF-8 Support**: Mendukung karakter unicode dan emoji
- **Optimized Queries**: Database queries yang efisien dengan indexing

## 📋 System Requirements

- **PHP**: 8.0 atau lebih tinggi
- **Database**: MySQL 8.0 / MariaDB 10.5 atau lebih tinggi
- **Web Server**: Apache/Nginx atau PHP Built-in Server
- **Browser**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Docker**: (Opsional) untuk containerized deployment

## 🚀 Quick Start

#### Database Setup

# Menggunakan terminal MySQL langsung
```
mysql -u root -p db_penjualan < database.sql
```

**Apache/Nginx** (Production):
- Copy project ke document root web server
- Pastikan mod_rewrite aktif (untuk Apache)
- Configure virtual host jika diperlukan


## 📁 Project Structure

```
crud-php-mysql/
├── 📁 assets/
│   ├── style.css           # Modern CSS dengan gradient & animations
│   └── script.js           # Interactive JavaScript features
├── 📁 config/
│   └── database.php        # Database connection & configuration
├── 📁 crud/
│   ├── index.php           # Product listing dengan search/sort/filter
│   ├── create.php          # Add new product form
│   ├── edit.php            # Edit existing product
│   ├── view.php            # Product detail view
│   └── delete.php          # Delete product with confirmation
├── 📁 uploads/             # Product image storage
├── 📁 database.sql      # Database schema & sample data
├── 📁 index.php           # Dashboard dengan analytics
└── 📁 README.md           # This documentation
```

## 💾 Database Schema

### Tabel: `categories`
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK, AUTO_INCREMENT) | ID unik kategori |
| name | VARCHAR(100) | Nama kategori (Handphone, Laptop, dll.) |
| icon | VARCHAR(50) | Icon text (contoh: [HP], [LT]) |
| color | VARCHAR(20) | Hex color code untuk UI |
| created_at | TIMESTAMP | Waktu dibuat |

### Tabel: `products`
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK, AUTO_INCREMENT) | ID unik produk |
| category_id | INT (FK) | Reference ke tabel categories |
| brand | VARCHAR(100) | Merek produk |
| model | VARCHAR(150) | Model/tipe produk |
| price | DECIMAL(15,2) | Harga dalam Rupiah |
| stock | INT | Jumlah stok tersedia |
| description | TEXT | Deskripsi spesifikasi produk |
| image | VARCHAR(255) | Path file gambar produk |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu terakhir update |

## 🎨 User Experience Features

### Dashboard Analytics
- **Total Products**: Jumlah semua produk
- **Total Stock**: Jumlah unit stok keseluruhan
- **Inventory Value**: Nilai total inventory dalam Rupiah
- **Category Breakdown**: Produk per kategori dengan nilai inventory

### Advanced Product Management
- **Real-time Search**: Cari berdasarkan brand atau model
- **Category Filtering**: Filter produk berdasarkan kategori
- **Multiple Sorting Options**:
  - 🕒 Terbaru/Terlima (berdasarkan tanggal)
  - 📝 Brand A-Z / Z-A
  - 💰 Harga Rendah/Tinggi
  - 📦 Stok Sedikit/Banyak

### Visual Indicators
- **Stock Status Colors**:
  - 🟢 Hijau: Stok tinggi (>15 unit)
  - 🟡 Kuning: Stok sedang (6-15 unit)
  - 🔴 Merah: Stok rendah (≤5 unit)
- **Category Badges**: Setiap produk ditandai dengan kategori dan warna

## 🔧 API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/` | GET | Dashboard utama dengan statistik |
| `/crud/index.php` | GET | List semua produk dengan filter/sort |
| `/crud/create.php` | GET/POST | Form tambah produk baru |
| `/crud/edit.php?id=X` | GET/POST | Form edit produk |
| `/crud/view.php?id=X` | GET | Detail produk |
| `/crud/delete.php?id=X` | POST | Hapus produk |

### Query Parameters (GET)
- `search`: Pencarian berdasarkan brand/model
- `category_id`: Filter berdasarkan kategori
- `sort`: Sorting (brand_asc, price_desc, dll.)
- `stock`: Filter stok (low untuk ≤10)

## 🐳 Docker Configuration

### Services
- **mysql**: MySQL 8.0 database dengan persistent volume
- **php-app**: PHP 8.0 dengan Apache, auto-reload untuk development

### Networks
- **lab5_network**: Isolated network untuk komunikasi antar container

### Volumes
- **mysql_data**: Persistent storage untuk database
- **./:/var/www/html**: Source code mounting
- **./uploads:/var/www/html/uploads**: File upload storage

## 🧪 Testing Guide

### Basic CRUD Testing
1. **Create**: Tambah produk baru dengan gambar
2. **Read**: Lihat daftar produk dan detail
3. **Update**: Edit informasi produk existing
4. **Delete**: Hapus produk dengan konfirmasi

### Advanced Features Testing
1. **Search**: Cari produk berdasarkan brand
2. **Filter**: Filter berdasarkan kategori
3. **Sort**: Test semua opsi sorting
4. **Upload**: Test upload gambar dengan drag & drop
5. **Responsive**: Test di berbagai ukuran layar

### Performance Testing
- Load testing dengan 100+ produk
- Search performance dengan large dataset
- Image upload dengan berbagai format/size

### File Permission Issues
```bash
# Fix upload directory permissions
chmod 755 uploads/
chmod 644 uploads/*
```

### Common Errors
- **"Access denied for user"**: Check database credentials
- **"Table doesn't exist"**: Re-import `database.sql`
- **"File upload failed"**: Check upload directory permissions
- **"Emoji characters"**: Database menggunakan UTF-8, pastikan browser support

## 🔄 Migration Guide

### From V1 to V2
1. Backup data existing jika ada
2. Stop aplikasi lama
3. Import `database.sql` untuk schema baru
4. Migrate data dari tabel lama ke struktur baru
5. Update semua file path references
6. Test semua functionality

## 📈 Performance Optimization

### Database Optimization
- **Indexes**: Primary keys, foreign keys, dan frequently queried columns
- **Query Optimization**: Efficient JOINs dan WHERE clauses
- **Connection Pooling**: Persistent connections

### Frontend Optimization
- **Lazy Loading**: Images dimuat saat diperlukan
- **Minification**: CSS/JS compression
- **Caching**: Browser caching untuk static assets

### Server Optimization
- **OPcache**: PHP opcode caching
- **Compression**: Gzip compression untuk responses
- **CDN**: Static assets delivery

### Code Standards
- **PHP**: PSR-12 coding standards
- **JavaScript**: ESLint dengan Airbnb config
- **CSS**: BEM methodology
- **Git**: Conventional commits

## 📝 Academic Notes

**Course**: LAB 5 - Web Programming
**Objective**: Implementasi CRUD dengan PHP & MySQL

### Learning Outcomes
1. **Database Design**: Relational database dengan foreign keys
2. **PHP Backend**: Server-side programming dengan security best practices
3. **Frontend Integration**: HTML/CSS/JS untuk interactive web applications
4. **Docker Containerization**: Modern deployment practices
5. **User Experience**: Responsive design dan intuitive interfaces

### Technical Stack
- **Backend**: PHP 8.0, MySQL 8.0
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Tools**: Docker, Composer, Git
- **Architecture**: MVC-inspired structure

---

**LAB 5 Quiz Project V2** - Modern Multi-Product Management System
© 2026 - Built with ❤️ using PHP & MySQL
