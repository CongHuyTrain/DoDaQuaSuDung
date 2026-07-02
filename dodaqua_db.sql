-- ============================================================
--  dodaqua_db.sql
--  Dùng cho: MySQL Workbench 8.0 CE + XAMPP (MySQL 8.0)
--  Cách chạy: Mở file này trong Workbench → Ctrl+Shift+Enter
-- ============================================================

-- Đảm bảo không bị lỗi khi chạy lại
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tạo & chọn database
CREATE DATABASE IF NOT EXISTS `dodaqua_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `dodaqua_db`;

-- ============================================================
-- 2. BẢNG: users
-- ============================================================
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `full_name`  VARCHAR(100)     NOT NULL,
  `email`      VARCHAR(150)     NOT NULL,
  `password`   VARCHAR(255)     NOT NULL COMMENT 'Lưu hash bằng password_hash()',
  `phone`      VARCHAR(20)      DEFAULT NULL,
  `role`       ENUM('user','admin') NOT NULL DEFAULT 'user',
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. BẢNG: categories
-- ============================================================
CREATE TABLE `categories` (
  `id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cat_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. BẢNG: products
-- ============================================================
CREATE TABLE `products` (
  `id`             INT UNSIGNED      NOT NULL AUTO_INCREMENT,
  `user_id`        INT UNSIGNED      NOT NULL,
  `category_id`    INT UNSIGNED      NOT NULL,
  `title`          VARCHAR(200)      NOT NULL,
  `description`    TEXT              DEFAULT NULL,
  `price`          DECIMAL(15,0)     NOT NULL,
  `image`          VARCHAR(500)      DEFAULT NULL,
  `condition_item` ENUM('new','like_new','good','fair') NOT NULL DEFAULT 'good',
  `location`       VARCHAR(150)      DEFAULT NULL,
  `status`         ENUM('active','sold','hidden')       NOT NULL DEFAULT 'active',
  `created_at`     DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category`  (`category_id`),
  KEY `idx_user`      (`user_id`),
  KEY `idx_status`    (`status`),
  KEY `idx_created`   (`created_at`),
  CONSTRAINT `fk_product_user`
    FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`)      ON DELETE CASCADE,
  CONSTRAINT `fk_product_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. BẢNG: orders
-- ============================================================
CREATE TABLE `orders` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `buyer_id`   INT UNSIGNED NOT NULL,
  `status`     ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `note`       TEXT         DEFAULT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_product` (`product_id`),
  KEY `idx_order_buyer`   (`buyer_id`),
  CONSTRAINT `fk_order_product`
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_buyer`
    FOREIGN KEY (`buyer_id`)   REFERENCES `users`(`id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 6. DỮ LIỆU MẪU: users
--    Mật khẩu gốc tất cả tài khoản: Test@1234
-- ============================================================
INSERT INTO `users` (`full_name`, `email`, `password`, `phone`, `role`) VALUES
('Nguyễn Văn An',  'an@example.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0901234561', 'admin'),
('Trần Thị Bình',  'binh@example.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0912345672', 'user'),
('Lê Văn Cường',   'cuong@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0923456783', 'user'),
('Phạm Thị Dung',  'dung@example.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0934567894', 'user'),
('Hoàng Văn Em',   'em@example.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0945678905', 'user');

-- ============================================================
-- 7. DỮ LIỆU MẪU: categories
-- ============================================================
INSERT INTO `categories` (`name`) VALUES
('Điện thoại & Máy tính bảng'),
('Laptop & Máy tính'),
('Âm thanh & Tai nghe'),
('Máy ảnh & Quay phim'),
('Đồ gia dụng'),
('Thời trang & Phụ kiện'),
('Đồ chơi & Trẻ em'),
('Sách & Văn phòng phẩm'),
('Xe cộ & Phụ tùng'),
('Thể thao & Dã ngoại');

-- ============================================================
-- 8. DỮ LIỆU MẪU: products
-- ============================================================
INSERT INTO `products` (`user_id`,`category_id`,`title`,`description`,`price`,`image`,`condition_item`,`location`,`status`) VALUES
(2,1,'iPhone 13 Pro Max 256GB Xanh Sierra','Máy mua 12/2021, sử dụng kỹ, không trầy xước, pin còn 91%. Đầy đủ hộp, sạc zin.',18500000,NULL,'like_new','TP. Hồ Chí Minh','active'),
(3,1,'Samsung Galaxy S22 Ultra 256GB Đen','Dùng từ tháng 3/2022, còn rất mới. Không va đập, pin 89%. Kèm ốp lưng chính hãng.',16800000,NULL,'like_new','Hà Nội','active'),
(4,1,'Xiaomi Redmi Note 11 Pro 128GB','Cấu hình mạnh, dùng 6 tháng, còn bảo hành hãng. Màn hình 120Hz, sạc 67W.',4200000,NULL,'good','Đà Nẵng','active'),
(5,1,'OPPO Reno 8 5G 128GB Xanh','Mua tháng 9/2022, tặng kèm ốp lưng và kính cường lực. Máy chạy mượt.',6500000,NULL,'like_new','Cần Thơ','active'),
(2,2,'MacBook Air M1 2020 8GB/256GB','Máy nguyên bản, pin còn 87 chu kỳ. Chạy macOS Ventura mượt mà. Tặng túi chống sốc.',19500000,NULL,'like_new','TP. Hồ Chí Minh','active'),
(3,2,'Dell XPS 15 9510 Core i7 / 16GB / 512GB SSD','Laptop cao cấp màn 4K OLED. Không lỗi, bàn phím còn tốt. Giá thương lượng.',22000000,NULL,'good','Hà Nội','active'),
(4,2,'Lenovo ThinkPad E14 Gen 2 i5 / 8GB / 256GB','Laptop văn phòng bền bỉ. Mua 2021, dùng nhẹ, không trầy.',8900000,NULL,'good','Đà Nẵng','active'),
(5,2,'ASUS VivoBook 15 Core i3 / 8GB / 512GB','Máy học sinh sinh viên. Dùng 8 tháng, không va đập. Chạy Win 11 bản quyền.',6200000,NULL,'good','Bình Dương','active'),
(2,3,'Sony WH-1000XM5 Chống ồn cao cấp','Mua 5/2022, dùng ít. Chống ồn hoàn hảo, pin 30h. Đầy đủ hộp và phụ kiện.',7200000,NULL,'like_new','TP. Hồ Chí Minh','active'),
(3,3,'AirPods Pro 2 (2022) MagSafe','Mới dùng 3 tháng. ANC hoạt động tốt, hộp sạc không trầy.',5800000,NULL,'like_new','Hà Nội','active'),
(4,3,'JBL Flip 6 Loa Bluetooth chống nước','Loa dùng ngoài trời, pin 12h, âm bass mạnh. Chỉ thiếu hộp.',1850000,NULL,'good','Đà Nẵng','active'),
(5,4,'Sony Alpha A7 III Body + Lens 28-70mm','Máy ảnh full-frame. Shutter count ~15.000. Không mốc lens, không bụi sensor.',32000000,NULL,'good','TP. Hồ Chí Minh','active'),
(2,4,'Canon EOS 90D + 18-135mm IS USM','Bộ máy bán professional. Shutter count 8.500. Kèm túi máy, thẻ nhớ 64GB.',24500000,NULL,'good','Hà Nội','active'),
(3,5,'Máy lọc không khí Xiaomi Air Purifier 4 Pro','Dùng 1 năm, bộ lọc thay mới tháng trước. Phù hợp phòng 40–60m².',2800000,NULL,'good','TP. Hồ Chí Minh','active'),
(4,5,'Nồi cơm điện Cuckoo IH 1.08L','Hàng nội địa Hàn Quốc, nấu ngon. Dùng 2 năm, còn hoạt động tốt.',1600000,NULL,'good','Cần Thơ','active'),
(5,6,'Nike Air Force 1 Low White Size 42','Giày mới 100%, mua nhưng không vừa. Còn nguyên hộp tag.',1850000,NULL,'new','TP. Hồ Chí Minh','active'),
(2,6,'Túi tote canvas Anello Navy','Túi đi học tiện lợi, dùng vài lần, không bẩn rách.',320000,NULL,'like_new','Hà Nội','active'),
(3,7,'LEGO Technic 42083 Bugatti Chiron','Set Lego hiếm, đã lắp 1 lần rồi tháo ra, còn đủ mảnh.',3200000,NULL,'like_new','TP. Hồ Chí Minh','active'),
(4,8,'Bộ sách Atomic Habits + Deep Work + Show Your Work','Ba cuốn best-seller về năng suất. Đọc 1 lần, còn mới. Giá cả 3 cuốn.',190000,NULL,'good','Đà Nẵng','active'),
(5,9,'Xe đạp thể thao Giant ATX 810 size M','Xe đạp địa hình 27 tốc độ. Dùng 1.5 năm, đã thay bánh mới.',5500000,NULL,'good','TP. Hồ Chí Minh','active'),
(2,10,'Vợt cầu lông Yonex Astrox 99 Pro','Vợt chính hãng. Dùng 6 tháng thi đấu, chưa đứt dây.',2400000,NULL,'good','Hà Nội','active');

-- ============================================================
-- 9. DỮ LIỆU MẪU: orders
-- ============================================================
INSERT INTO `orders` (`product_id`,`buyer_id`,`status`,`note`) VALUES
(1,  3, 'completed', 'Giao hàng tận nhà, đã nhận.'),
(5,  4, 'confirmed', 'Hẹn gặp trao đổi thứ 7 tuần này.'),
(9,  2, 'pending',   'Còn thương lượng giá.'),
(15, 5, 'cancelled', 'Người mua huỷ vì đổi ý.');

-- ============================================================
-- KIỂM TRA KẾT QUẢ
-- ============================================================
SELECT 'users'      AS `Bảng`, COUNT(*) AS `Số dòng` FROM users
UNION ALL
SELECT 'categories', COUNT(*) FROM categories
UNION ALL
SELECT 'products',   COUNT(*) FROM products
UNION ALL
SELECT 'orders',     COUNT(*) FROM orders;

-- ============================================================
-- 10. BẢNG: product_images (nhiều ảnh cho 1 sản phẩm)
-- ============================================================
DROP TABLE IF EXISTS `product_images`;

CREATE TABLE `product_images` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED  NOT NULL,
  `image_url`  VARCHAR(500)  NOT NULL,
  `sort_order` TINYINT       NOT NULL DEFAULT 0 COMMENT '0 = ảnh đại diện',
  PRIMARY KEY (`id`),
  KEY `idx_pi_product` (`product_id`),
  CONSTRAINT `fk_pi_product`
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. ẢNH MINH HỌA MẪU (dùng ảnh Unsplash – miễn phí)
--     Mỗi sản phẩm có 2–3 ảnh; sort_order=0 là ảnh chính
-- ============================================================

-- Sản phẩm 1: iPhone 13 Pro Max
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(1, 'https://images.unsplash.com/photo-1632661674596-df8be070a5c5?w=800&q=80', 0),
(1, 'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=800&q=80', 1),
(1, 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=800&q=80', 2);

-- Sản phẩm 2: Samsung Galaxy S22 Ultra
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(2, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&q=80', 0),
(2, 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=800&q=80', 1);

-- Sản phẩm 3: Xiaomi Redmi Note
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(3, 'https://images.unsplash.com/photo-1585060544812-6b45742d762f?w=800&q=80', 0),
(3, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&q=80', 1);

-- Sản phẩm 4: OPPO Reno 8
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(4, 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=800&q=80', 0),
(4, 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&q=80', 1);

-- Sản phẩm 5: MacBook Air M1
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(5, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&q=80', 0),
(5, 'https://images.unsplash.com/photo-1611186871525-37d95cc8ed34?w=800&q=80', 1),
(5, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=80', 2);

-- Sản phẩm 6: Dell XPS 15
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(6, 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=800&q=80', 0),
(6, 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=800&q=80', 1);

-- Sản phẩm 7: Lenovo ThinkPad
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(7, 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=800&q=80', 0),
(7, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&q=80', 1);

-- Sản phẩm 8: ASUS VivoBook
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(8, 'https://images.unsplash.com/photo-1484788984921-03950022c9ef?w=800&q=80', 0),
(8, 'https://images.unsplash.com/photo-1537498425277-c283d32ef9db?w=800&q=80', 1);

-- Sản phẩm 9: Sony WH-1000XM5
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(9,  'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&q=80', 0),
(9,  'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=800&q=80', 1),
(9,  'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&q=80', 2);

-- Sản phẩm 10: AirPods Pro 2
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(10, 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=800&q=80', 0),
(10, 'https://images.unsplash.com/photo-1589492477829-5e65395b66cc?w=800&q=80', 1);

-- Sản phẩm 11: JBL Flip 6
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(11, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&q=80', 0),
(11, 'https://images.unsplash.com/photo-1589003077984-894e133dabab?w=800&q=80', 1);

-- Sản phẩm 12: Sony Alpha A7 III
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(12, 'https://images.unsplash.com/photo-1510127034890-ba27508e9f1c?w=800&q=80', 0),
(12, 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80', 1),
(12, 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=800&q=80', 2);

-- Sản phẩm 13: Canon EOS 90D
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(13, 'https://images.unsplash.com/photo-1581591524425-c7e0978865fc?w=800&q=80', 0),
(13, 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=800&q=80', 1);

-- Sản phẩm 14: Máy lọc không khí Xiaomi
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(14, 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=800&q=80', 0);

-- Sản phẩm 15: Nồi cơm điện Cuckoo
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(15, 'https://i0.wp.com/locknlockonline.com/wp-content/uploads/2025/10/tai-xuong-2025-09-04t081430-719.png?fit=800%2C800&ssl=1', 0);

-- Sản phẩm 16: Nike Air Force 1
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(16, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80', 0),
(16, 'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=800&q=80', 1),
(16, 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=800&q=80', 2);

-- Sản phẩm 17: Túi tote Anello
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(17, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=80', 0),
(17, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&q=80', 1);

-- Sản phẩm 18: LEGO Technic
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(18, 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=800&q=80', 0),
(18, 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80', 1);

-- Sản phẩm 19: Sách
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(19, 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&q=80', 0),
(19, 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=800&q=80', 1);

-- Sản phẩm 20: Xe đạp Giant
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(20, 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?w=800&q=80', 0),
(20, 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=800&q=80', 1);

-- Sản phẩm 21: Vợt cầu lông Yonex
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(21, 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800&q=80', 0),
(21, 'https://images.unsplash.com/photo-1617083934551-ac1f4b5b72a3?w=800&q=80', 1);

-- Đồng bộ cột image chính trong products từ ảnh đầu tiên
UPDATE products p
JOIN product_images pi ON pi.product_id = p.id AND pi.sort_order = 0
SET p.image = pi.image_url;

-- Kiểm tra cuối
SELECT 'product_images', COUNT(*) FROM product_images;
