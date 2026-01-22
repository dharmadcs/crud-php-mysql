-- Database V2: Modern Multi-Product Management System
-- LAB 5 Quiz Project - Refactored

-- Drop old tables if exist
DROP TABLE IF EXISTS handphones;
DROP TABLE IF EXISTS laptops;

-- Create database
CREATE DATABASE IF NOT EXISTS db_penjualan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_penjualan;

-- Table: categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT '📦',
    color VARCHAR(20) DEFAULT '#667eea',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: products (universal for all product types)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(150) NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    description TEXT,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_brand (brand),
    INDEX idx_price (price),
    INDEX idx_stock (stock)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO categories (name, icon, color) VALUES
('Handphone', '[HP]', '#f093fb'),
('Laptop', '[LT]', '#4facfe'),
('Tablet', '[TB]', '#43e97b'),
('Smart Watch', '[SW]', '#fa709a'),
('Camera', '[CM]', '#667eea'),
('Audio', '[AU]', '#764ba2'),
('Gaming', '[GM]', '#f5576c'),
('Accessories', '[AC]', '#38f9d7');

-- Insert sample data for products (migrated from old tables)
-- Handphones
INSERT INTO products (category_id, brand, model, price, stock, description) VALUES
(1, 'Samsung', 'Galaxy S23 Ultra', 15999000, 15, '6.8" Dynamic AMOLED, Snapdragon 8 Gen 2, 12GB RAM, 256GB Storage, 200MP Camera'),
(1, 'Apple', 'iPhone 15 Pro Max', 21999000, 10, '6.7" Super Retina XDR, A17 Pro, 8GB RAM, 256GB Storage, 48MP Camera'),
(1, 'Xiaomi', 'Redmi Note 13 Pro', 4299000, 25, '6.67" AMOLED, Snapdragon 7s Gen 2, 8GB RAM, 128GB Storage, 200MP Camera'),
(1, 'OPPO', 'Reno 11', 5499000, 20, '6.7" AMOLED, Dimensity 8200, 8GB RAM, 256GB Storage, 50MP Camera'),
(1, 'Vivo', 'V29', 4999000, 18, '6.78" AMOLED, Snapdragon 778G, 12GB RAM, 256GB Storage, 50MP Camera');

-- Laptops
INSERT INTO products (category_id, brand, model, price, stock, description) VALUES
(2, 'ASUS', 'ROG Strix G15', 18999000, 8, 'AMD Ryzen 9 5900HX, 16GB DDR4, 512GB SSD, RTX 3060'),
(2, 'Lenovo', 'ThinkPad X1 Carbon', 24500000, 5, 'Intel Core i7-1260P, 16GB LPDDR5, 1TB SSD'),
(2, 'HP', 'Pavilion Gaming 15', 12999000, 12, 'Intel Core i5-12500H, 8GB DDR4, 512GB SSD, GTX 1650'),
(2, 'Dell', 'XPS 13', 19999000, 7, 'Intel Core i7-1185G7, 16GB LPDDR4x, 512GB SSD'),
(2, 'Acer', 'Aspire 5', 7999000, 15, 'AMD Ryzen 5 5500U, 8GB DDR4, 512GB SSD'),
(2, 'Apple', 'MacBook Air M2', 16999000, 10, 'Apple M2 Chip, 8GB Unified, 256GB SSD');

-- Additional sample products (other categories)
INSERT INTO products (category_id, brand, model, price, stock, description) VALUES
(3, 'Samsung', 'Galaxy Tab S9', 9999000, 12, '11" Dynamic AMOLED, Snapdragon 8 Gen 2, 8GB RAM, 128GB Storage'),
(3, 'Apple', 'iPad Pro 12.9"', 17999000, 8, '12.9" Liquid Retina XDR, M2 Chip, 8GB RAM, 256GB Storage'),
(4, 'Apple', 'Watch Series 9', 6999000, 15, 'Always-On Retina Display, S9 SiP, Health Tracking'),
(4, 'Samsung', 'Galaxy Watch 6', 4999000, 20, 'Super AMOLED, Exynos W930, Health Monitoring'),
(5, 'Sony', 'Alpha A7 IV', 35999000, 5, 'Full-frame 33MP, 4K 60fps, In-body stabilization'),
(5, 'Canon', 'EOS R6 Mark II', 42999000, 4, 'Full-frame 24MP, 4K 60fps, Dual Pixel AF'),
(6, 'Sony', 'WH-1000XM5', 5499000, 25, 'Premium Noise Cancelling, 30hr Battery, LDAC'),
(6, 'AirPods', 'Pro 2nd Gen', 3999000, 30, 'Active Noise Cancellation, Spatial Audio');
