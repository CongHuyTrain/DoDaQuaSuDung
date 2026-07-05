
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categories (name, slug) VALUES
('Điện thoại - Máy tính bảng', 'dien-thoai-may-tinh-bang'),
('Laptop - Máy tính', 'laptop-may-tinh'),
('Đồ gia dụng', 'do-gia-dung'),
('Thời trang', 'thoi-trang'),
('Sách - Văn phòng phẩm', 'sach-van-phong-pham'),
('Đồ khác', 'do-khac');

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,                     
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(12,2) NOT NULL,
    condition_status ENUM('Mới', 'Like new', 'Đã qua sử dụng') DEFAULT 'Đã qua sử dụng',
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('Đang bán', 'Đã bán', 'Đã ẩn') DEFAULT 'Đang bán',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_products_user ON products(user_id);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_status ON products(status);
