-- ============================================================
--  update_descriptions.sql
--  Cập nhật mô tả chi tiết cho 21 sản phẩm
--  Chạy trong phpMyAdmin: chọn database dodaqua_db → Import file này
-- ============================================================

USE `dodaqua_db`;

-- Sản phẩm 1: iPhone 13 Pro Max
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY
- Mua mới tháng 12/2021, sử dụng được khoảng 2 năm
- Máy không trầy xước, không va đập, không bị bẻ cong
- Màn hình zin, không bóng ma, không điểm chết
- Pin còn 91% (kiểm tra qua cài đặt > pin)
- Loa, mic, camera, Face ID hoạt động hoàn hảo

📦 TRONG HỘP BAO GỒM
- iPhone 13 Pro Max 256GB màu Xanh Sierra
- Hộp zin chính hãng Apple
- Cáp Lightning to USB-C zin
- Sạc củ 20W (mua kèm)
- Không có tai nghe (chưa mở)

⚙️ THÔNG SỐ KỸ THUẬT
- Màn hình: 6.7 inch Super Retina XDR ProMotion 120Hz
- Chip: Apple A15 Bionic
- Camera: 12MP x3 (chính, góc rộng, tele 3x)
- Pin: 4352 mAh
- Bộ nhớ: 256GB (không mở rộng được)
- Kết nối: 5G, WiFi 6, Bluetooth 5.0

💬 LƯU Ý KHI MUA
- Giá đã fix, không thương lượng thêm
- Hỗ trợ kiểm tra máy trực tiếp tại TP.HCM
- Có thể ship COD toàn quốc (người mua chịu phí ship)' WHERE `id` = 1;

-- Sản phẩm 2: Samsung Galaxy S22 Ultra
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY
- Mua mới tháng 3/2022, dùng được khoảng 1.5 năm
- Máy còn rất mới, không va đập, không trầy xước đáng kể
- Màn hình Dynamic AMOLED 2X sắc nét, không lỗi điểm ảnh
- Pin còn 89%, sạc nhanh 45W vẫn hoạt động tốt
- S Pen đi kèm còn nguyên, không mất nét

📦 TRONG HỘP BAO GỒM
- Samsung Galaxy S22 Ultra 256GB màu Đen Phantom
- Hộp zin Samsung
- Cáp USB-C zin
- S Pen zin trong máy
- Ốp lưng chính hãng Samsung (dùng 1 tuần)

⚙️ THÔNG SỐ KỸ THUẬT
- Màn hình: 6.8 inch Dynamic AMOLED 2X, 120Hz
- Chip: Snapdragon 8 Gen 1
- Camera: 108MP + 10MP (tele 3x) + 10MP (tele 10x) + 12MP góc rộng
- Pin: 5000 mAh, sạc nhanh 45W
- Bộ nhớ: 256GB + 12GB RAM
- Tích hợp S Pen, kết nối 5G

💬 LƯU Ý KHI MUA
- Hỗ trợ gặp mặt kiểm tra tại Hà Nội (quận Đống Đa)
- Ship toàn quốc qua GHTK hoặc GHN' WHERE `id` = 2;

-- Sản phẩm 3: Xiaomi Redmi Note 11 Pro
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY
- Mua mới tháng 6/2022, dùng 6 tháng rồi thay máy mới
- Máy còn tốt, có vài vết xước nhỏ ở viền (không đáng kể)
- Màn hình AMOLED không bóng ma, cảm ứng mượt
- Pin còn 85%, sạc siêu nhanh 67W vẫn hoạt động
- Còn bảo hành hãng đến tháng 6/2024

📦 TRONG HỘP BAO GỒM
- Xiaomi Redmi Note 11 Pro 128GB
- Cáp USB-C
- Củ sạc 67W zin
- Hộp zin (có trầy nhẹ)

⚙️ THÔNG SỐ KỸ THUẬT
- Màn hình: 6.67 inch AMOLED, 120Hz
- Chip: Mediatek Helio G96
- Camera: 108MP + 8MP + 2MP
- Pin: 5000 mAh, sạc 67W
- Bộ nhớ: 128GB + 8GB RAM, hỗ trợ thẻ nhớ MicroSD
- Kết nối: 4G LTE, WiFi 5, Bluetooth 5.2

💬 LƯU Ý KHI MUA
- Phù hợp học sinh, sinh viên cần máy cấu hình tốt giá rẻ
- Gặp mặt tại Đà Nẵng hoặc ship toàn quốc' WHERE `id` = 3;

-- Sản phẩm 4: OPPO Reno 8 5G
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY
- Mua mới tháng 9/2022, sử dụng nhẹ nhàng
- Máy như mới, không va đập, không trầy xước
- Màn hình AMOLED 90Hz, màu sắc tươi đẹp
- Pin còn 92%, sạc 80W siêu nhanh

📦 TRONG HỘP BAO GỒM
- OPPO Reno 8 5G 128GB màu Xanh Shimmer
- Củ sạc 80W SUPERVOOC zin
- Cáp USB-C zin
- Ốp lưng silicon trong suốt (mới)
- Kính cường lực đã dán sẵn

⚙️ THÔNG SỐ KỸ THUẬT
- Màn hình: 6.43 inch AMOLED, 90Hz
- Chip: Mediatek Dimensity 1300
- Camera: 50MP Sony IMX766 + 8MP + 2MP
- Camera trước: 32MP
- Pin: 4500 mAh, sạc 80W SUPERVOOC
- Kết nối: 5G, WiFi 6, Bluetooth 5.3

💬 LƯU Ý KHI MUA
- Hỗ trợ gặp mặt tại Cần Thơ
- Có thể ship COD, phí ship người mua chịu' WHERE `id` = 4;

-- Sản phẩm 5: MacBook Air M1
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY
- Mua mới tháng 1/2021, sử dụng làm việc văn phòng nhẹ
- Máy nguyên bản 100%, không nâng cấp, không sửa chữa
- Vỏ nhôm không trầy, bàn phím không mòn phím
- Pin còn 87 chu kỳ sạc (rất thấp, tốt cho tuổi thọ pin)
- Màn hình Retina không bóng ma, không điểm chết

📦 TRONG HỘP BAO GỒM
- MacBook Air M1 2020 8GB RAM / 256GB SSD màu Gold
- Củ sạc MagSafe 30W zin Apple
- Cáp USB-C to MagSafe zin
- Hộp zin Apple
- Túi chống sốc nỉ (tặng kèm)

⚙️ THÔNG SỐ KỸ THUẬT
- Chip: Apple M1 (8-core CPU, 7-core GPU)
- RAM: 8GB Unified Memory
- Ổ cứng: 256GB SSD NVMe
- Màn hình: 13.3 inch Retina IPS, 2560x1600
- Pin: lên đến 18 giờ sử dụng thực tế
- Cổng: 2x Thunderbolt/USB 4, jack 3.5mm
- Trọng lượng: 1.29 kg

💬 LƯU Ý KHI MUA
- Đang chạy macOS Ventura 13.6, cập nhật được lên Sonoma
- Phù hợp lập trình, thiết kế đồ họa nhẹ, văn phòng
- Gặp mặt tại TP.HCM (quận 7) để kiểm tra' WHERE `id` = 5;

-- Sản phẩm 6: Dell XPS 15 9510
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY
- Mua mới năm 2022, dùng làm đồ họa và lập trình được 1 năm
- Máy không lỗi, bàn phím có đèn nền còn sáng đều
- Màn hình OLED 4K sắc nét, không burn-in
- Pin còn 78%, thời lượng thực tế ~5-6 giờ làm việc nhẹ
- Quạt tản nhiệt hoạt động bình thường, không ồn bất thường

📦 TRONG HỘP BAO GỒM
- Dell XPS 15 9510
- Củ sạc 130W zin Dell
- Cáp nguồn
- Hộp zin (có móp nhẹ)

⚙️ THÔNG SỐ KỸ THUẬT
- CPU: Intel Core i7-11800H (8 nhân, tối đa 4.6GHz)
- RAM: 16GB DDR4 3200MHz
- SSD: 512GB NVMe PCIe Gen 4
- Màn hình: 15.6 inch OLED 4K (3840x2400), cảm ứng
- GPU: NVIDIA RTX 3050 Ti 4GB + Intel Iris Xe
- Cổng: 2x Thunderbolt 4, 1x USB-A, SD card, jack 3.5mm
- Trọng lượng: 1.86 kg

💬 LƯU Ý KHI MUA
- Phù hợp lập trình viên, kỹ sư, designer chuyên nghiệp
- Giá có thể thương lượng thêm nếu thiện chí
- Gặp mặt tại Hà Nội (quận Cầu Giấy)' WHERE `id` = 6;

-- Sản phẩm 7: Lenovo ThinkPad E14
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY
- Mua mới năm 2021, dùng văn phòng nhẹ nhàng
- Máy không trầy xước, bàn phím nổi tiếng của ThinkPad còn tốt
- Màn hình IPS chống chói, màu chuẩn cho văn phòng
- Pin còn khoảng 80%, dùng được 6-7 tiếng văn phòng
- Còn bảo hành hãng Lenovo đến tháng 6/2024

📦 TRONG HỘP BAO GỒM
- Lenovo ThinkPad E14 Gen 2
- Củ sạc 65W USB-C zin
- Cáp nguồn

⚙️ THÔNG SỐ KỸ THUẬT
- CPU: Intel Core i5-1135G7 (4 nhân, tối đa 4.2GHz)
- RAM: 8GB DDR4 (có thể nâng lên 32GB)
- SSD: 256GB NVMe (có thể nâng thêm ổ)
- Màn hình: 14 inch IPS FHD (1920x1080), chống chói
- GPU: Intel Iris Xe Graphics
- Cổng: 2x USB-A, 2x USB-C, HDMI, RJ45, đầu đọc thẻ SD
- Trọng lượng: 1.69 kg

💬 LƯU Ý KHI MUA
- Phù hợp làm việc văn phòng, kế toán, học tập
- Bàn phím ThinkPad nổi tiếng êm và chính xác
- Gặp mặt tại Đà Nẵng hoặc ship toàn quốc' WHERE `id` = 7;

-- Sản phẩm 8: ASUS VivoBook 15
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY
- Mua mới tháng 4/2023, dùng được 8 tháng
- Máy không va đập, không trầy xước
- Màn hình FHD sáng, màu tốt cho học tập
- Pin còn 88%, dùng được 6-8 tiếng
- Đang chạy Windows 11 Home bản quyền (đã kích hoạt)

📦 TRONG HỘP BAO GỒM
- ASUS VivoBook 15 X1500EA
- Củ sạc 65W zin ASUS
- Túi đựng máy ASUS (tặng kèm)
- Hộp zin

⚙️ THÔNG SỐ KỸ THUẬT
- CPU: Intel Core i3-1115G4 (2 nhân, tối đa 4.1GHz)
- RAM: 8GB DDR4
- SSD: 512GB NVMe
- Màn hình: 15.6 inch FHD IPS (1920x1080)
- GPU: Intel UHD Graphics
- Cổng: 1x USB-C, 2x USB-A 3.2, 1x USB-A 2.0, HDMI, đọc thẻ SD
- Trọng lượng: 1.8 kg

💬 LƯU Ý KHI MUA
- Phù hợp học sinh, sinh viên, nhân viên văn phòng
- Gặp mặt tại Bình Dương (TP. Thủ Dầu Một)
- Hỗ trợ ship toàn quốc qua GHN' WHERE `id` = 8;

-- Sản phẩm 9: Sony WH-1000XM5
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG SẢN PHẨM
- Mua mới tháng 5/2022, dùng ít (khoảng 3-4 lần/tuần)
- Tai nghe không trầy xước, đệm tai còn mềm mại
- Chống ồn ANC hoạt động hoàn hảo, cực kỳ hiệu quả
- Pin đầy 30 giờ như quảng cáo, sạc nhanh 3 phút = 3 tiếng
- Kết nối Bluetooth 5.2 ổn định, đa điểm (2 thiết bị cùng lúc)

📦 TRONG HỘP BAO GỒM
- Tai nghe Sony WH-1000XM5 màu Đen
- Hộp zin Sony
- Cáp sạc USB-C zin
- Cáp audio 3.5mm zin
- Túi đựng tai nghe zin (vải mềm)
- Adapter máy bay zin

⚙️ THÔNG SỐ KỸ THUẬT
- Driver: 30mm
- Tần số đáp ứng: 4Hz – 40.000Hz
- Chống ồn: ANC tích hợp chip QN1 + V1
- Pin: 30 giờ (có ANC), 40 giờ (không ANC)
- Sạc: USB-C, sạc 3 phút dùng 3 tiếng
- Kết nối: Bluetooth 5.2, LDAC, multipoint 2 thiết bị
- Trọng lượng: 250g

💬 LƯU Ý KHI MUA
- Đây là model cao cấp nhất dòng WH của Sony
- Phù hợp làm việc từ xa, nghe nhạc, đi máy bay
- Gặp mặt tại TP.HCM (quận Bình Thạnh)' WHERE `id` = 9;

-- Sản phẩm 10: AirPods Pro 2
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG SẢN PHẨM
- Mua mới tháng 10/2022, dùng khoảng 3 tháng
- Tai nghe không trầy xước, hộp sạc không trầy
- ANC và Transparency Mode hoạt động hoàn hảo
- Pin tai nghe: ~6h/lần sạc, hộp sạc còn lại ~18h
- Kết nối H2 chip, độ trễ cực thấp

📦 TRONG HỘP BAO GỒM
- AirPods Pro 2 (MNEP3LL/A)
- Hộp sạc MagSafe Lightning
- Cáp Lightning to USB-C
- 4 đầu cao su (XS, S, M, L) – còn đủ bộ
- Hộp zin Apple

⚙️ THÔNG SỐ KỸ THUẬT
- Chip: Apple H2
- Chống ồn: ANC thế hệ 2, giảm tiếng ồn gấp 2x AirPods Pro 1
- Âm thanh: Adaptive Audio, Spatial Audio với Head Tracking
- Pin: 6 giờ (ANC bật), hộp sạc thêm 24 giờ
- Sạc: Lightning, MagSafe, Apple Watch charger
- Chống nước: IPX4 (tai nghe + hộp)

💬 LƯU Ý KHI MUA
- Chỉ tương thích tốt với thiết bị Apple (iPhone/iPad/Mac)
- Kiểm tra serial tại checkcoverage.apple.com trước khi mua
- Gặp mặt tại Hà Nội (quận Hoàn Kiếm)' WHERE `id` = 10;

-- Sản phẩm 11: JBL Flip 6
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG SẢN PHẨM
- Dùng khoảng 8 tháng, chủ yếu đi dã ngoại cuối tuần
- Loa không bị móp, lưới loa không rách
- Âm thanh bass còn đầm, treble rõ ràng
- Pin còn 85%, thời lượng thực tế 10-11 tiếng
- Chống nước IP67 vẫn hoạt động (đã test)

📦 TRONG HỘP BAO GỒM
- Loa JBL Flip 6 màu Đỏ
- Cáp sạc USB-C
- Không có hộp (đã thất lạc)

⚙️ THÔNG SỐ KỸ THUẬT
- Công suất: 30W
- Chống nước: IP67 (chịu nước hoàn toàn)
- Pin: 12 giờ sử dụng liên tục
- Kết nối: Bluetooth 5.1, tầm xa 10m
- Tính năng: PartyBoost (ghép nhiều loa), USB-C sạc
- Kích thước: 178 x 68 x 72 mm, trọng lượng 550g

💬 LƯU Ý KHI MUA
- Phù hợp đi picnic, camping, pool party
- Không có hộp nhưng loa hoạt động hoàn toàn bình thường
- Gặp mặt tại Đà Nẵng hoặc ship toàn quốc' WHERE `id` = 11;

-- Sản phẩm 12: Sony Alpha A7 III
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY ẢNH
- Dùng chụp du lịch và chân dung khoảng 2 năm
- Shutter count: ~15.000 (rất thấp, tuổi thọ màn trập ~200.000)
- Body không trầy xước nặng, chỉ có vài vết mòn nhỏ ở góc
- Sensor không bụi, không điểm chết (đã kiểm tra)
- Lens 28-70mm không mốc, không trầy kính, không bụi bên trong
- Màn hình lật không trầy, EVF sắc nét

📦 TRONG HỘP BAO GỒM
- Sony A7 III Body
- Lens Sony FE 28-70mm F3.5-5.6 OSS
- Pin NP-FZ100 x2 (1 zin, 1 third-party)
- Sạc đôi BC-QZ1
- Dây đeo cổ
- Nắp body, nắp lens zin
- Túi máy Lowepro (tặng kèm)

⚙️ THÔNG SỐ KỸ THUẬT
- Sensor: Full-frame BSI CMOS 24.2MP
- ISO: 100-51.200 (mở rộng lên 204.800)
- Lấy nét: 693 điểm phase-detect, Eye-AF
- Quay phim: 4K 30fps, 1080p 120fps
- Chống rung: 5 trục IBIS (5.0 stops)
- Kết nối: WiFi, Bluetooth, NFC, USB-C
- Pin: 710 ảnh/lần sạc

💬 LƯU Ý KHI MUA
- Phù hợp nhiếp ảnh gia bán chuyên nghiệp
- Hỗ trợ kiểm tra máy trực tiếp tại TP.HCM (quận 3)
- Giá thương lượng nếu mua thêm phụ kiện' WHERE `id` = 12;

-- Sản phẩm 13: Canon EOS 90D
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG MÁY ẢNH
- Dùng chụp sự kiện và thể thao khoảng 2 năm
- Shutter count: 8.500 (còn rất thấp)
- Body không trầy nặng, grip cao su còn tốt
- Lens 18-135mm IS USM không mốc, lấy nét nhanh, chính xác
- Pin còn tốt, sạc đầy dùng được khoảng 1.300 ảnh

📦 TRONG HỘP BAO GỒM
- Canon EOS 90D Body
- Lens Canon EF-S 18-135mm f/3.5-5.6 IS USM
- Pin LP-E6NH x1 zin
- Sạc LC-E6 zin
- Dây đeo cổ
- Thẻ nhớ SanDisk 64GB U3 (tặng kèm)
- Túi máy canvas (tặng kèm)

⚙️ THÔNG SỐ KỸ THUẬT
- Sensor: APS-C CMOS 32.5MP
- ISO: 100-25.600 (mở rộng 51.200)
- Lấy nét: 45 điểm cross-type, Dual Pixel CMOS AF
- Tốc độ chụp: 10 fps liên tục
- Quay phim: 4K 30fps không crop, 1080p 120fps
- Màn hình: 3 inch cảm ứng lật hoàn toàn
- Kết nối: WiFi, Bluetooth

💬 LƯU Ý KHI MUA
- Phù hợp chụp thể thao, wildlife, sự kiện
- Hỗ trợ gặp mặt tại Hà Nội (quận Đống Đa)' WHERE `id` = 13;

-- Sản phẩm 14: Máy lọc không khí Xiaomi
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG SẢN PHẨM
- Dùng khoảng 1 năm trong phòng ngủ 25m²
- Vỏ máy không trầy xước, cánh quạt còn tốt
- Bộ lọc HEPA đã thay mới cách đây 1 tháng (hóa đơn đính kèm)
- Màn hình cảm ứng OLED hoạt động tốt, hiển thị AQI chính xác
- Kết nối WiFi và app Mi Home hoạt động ổn định

📦 TRONG HỘP BAO GỒM
- Máy lọc không khí Xiaomi Air Purifier 4 Pro (AC-M15-SC)
- Dây nguồn
- Bộ lọc HEPA mới thay (còn khoảng 11 tháng)
- Hộp zin

⚙️ THÔNG SỐ KỸ THUẬT
- Diện tích phù hợp: 40–60 m²
- CADR: 500 m³/h
- Lọc: Pre-filter + Bộ lọc HEPA H13 + Than hoạt tính
- Cảm biến: PM2.5, nhiệt độ, độ ẩm
- Màn hình: OLED hiển thị AQI thời gian thực
- Kết nối: WiFi 2.4GHz, điều khiển qua app Mi Home
- Độ ồn: 33-64 dB(A)

💬 LƯU Ý KHI MUA
- Phù hợp gia đình có trẻ nhỏ hoặc người dị ứng bụi
- Gặp mặt tại TP.HCM (quận Tân Bình)' WHERE `id` = 14;

-- Sản phẩm 15: Nồi cơm điện Cuckoo IH
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG SẢN PHẨM
- Hàng nội địa Hàn Quốc, mua qua người thân
- Dùng 2 năm, nấu cơm ngon, không bị cháy, không dính
- Lòng nồi không trầy xước, còn lớp phủ tốt
- Tất cả chức năng hoạt động tốt: nấu nhanh, nấu chậm, hẹn giờ
- Tặng kèm rổ hấp inox chính hãng

📦 TRONG HỘP BAO GỒM
- Nồi cơm điện Cuckoo CRP-DHSR0609F (1.08L / 6 cup)
- Lòng nồi zin
- Rổ hấp inox (tặng kèm)
- Muôi nhựa x2
- Cốc đong gạo
- Dây nguồn
- Hướng dẫn sử dụng (tiếng Hàn + tiếng Anh)

⚙️ THÔNG SỐ KỸ THUẬT
- Công nghệ: IH (Induction Heating) – làm nóng đều toàn nồi
- Dung tích: 1.08L (6 cup gạo)
- Áp suất: nấu áp suất cao giúp cơm dẻo hơn
- Chế độ: nấu thường, nấu nhanh, cháo, hẹn giờ, giữ ấm
- Lòng nồi: phủ lớp Tri-ply stainless steel
- Điện áp: 220V (dùng trực tiếp tại Việt Nam)

💬 LƯU Ý KHI MUA
- Nồi IH nấu ngon hơn nhiều so với nồi thường
- Gặp mặt tại Cần Thơ (quận Ninh Kiều)' WHERE `id` = 15;

-- Sản phẩm 16: Nike Air Force 1
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG SẢN PHẨM
- Giày MỚI 100%, chưa dùng ngoài đường
- Mua tại Nike Store chính hãng nhưng không vừa chân (size 42 rộng hơn dự kiến)
- Chỉ thử 1 lần trong nhà, không có vết bẩn
- Còn nguyên hộp, tag giấy, giấy nhồi giày
- Đế giày trắng tinh, không vàng

📦 TRONG HỘP BAO GỒM
- Nike Air Force 1 Low \'07 màu Triple White
- Hộp zin Nike
- Tag giấy zin
- Dây giày dự phòng (trắng)
- Giấy nhồi giày

⚙️ THÔNG SỐ KỸ THUẬT
- Model: Nike Air Force 1 Low \'07 (CW2288-111)
- Size: 42 EU / 8.5 US / 27cm
- Màu: White/White (Triple White)
- Đế: Air cushioning đặc trưng AF1
- Chất liệu upper: Da tổng hợp cao cấp
- Xuất xứ: Indonesia (Nike chính hãng)

💬 LƯU Ý KHI MUA
- Kiểm tra kỹ size trước khi đặt mua (size này hơi rộng)
- Giá gốc 2.500.000đ tại Nike Store, bán lại 1.850.000đ
- Gặp mặt tại TP.HCM (quận 1) hoặc ship toàn quốc' WHERE `id` = 16;

-- Sản phẩm 17: Túi tote Anello
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG SẢN PHẨM
- Dùng khoảng 1 tháng, đi học và đi chơi
- Vải canvas không rách, không bẩn, không phai màu
- Dây đeo chắc chắn, kéo khóa kéo trơn tru
- Không có mùi lạ

📦 TRONG HỘP BAO GỒM
- Túi tote Anello canvas màu Navy (AT-B3393)
- Không có hộp (đã bỏ)

⚙️ THÔNG SỐ KỸ THUẬT
- Chất liệu: Canvas polyester cao cấp
- Màu: Navy (xanh đậm)
- Kích thước: 35 x 12 x 36 cm
- Ngăn chính: 1 ngăn lớn có khóa kéo
- Ngăn phụ: 2 ngăn nhỏ bên trong, 1 ngăn trước
- Dây đeo: 2 quai tay + 1 dây đeo chéo có thể tháo rời
- Xuất xứ: Nhật Bản

💬 LƯU Ý KHI MUA
- Phù hợp học sinh, sinh viên đi học, đi làm
- Màu navy hợp với nhiều trang phục
- Gặp mặt tại Hà Nội hoặc ship toàn quốc' WHERE `id` = 17;

-- Sản phẩm 18: LEGO Technic Bugatti
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG SẢN PHẨM
- Bộ LEGO đã lắp ráp 1 lần hoàn chỉnh, rồi tháo ra cất hộp
- Còn đủ 100% mảnh (đã đếm lại theo checklist)
- Hướng dẫn lắp ráp còn nguyên, không rách, không thiếu trang
- Hộp có móp nhẹ ở góc, các mảnh LEGO không trầy xước

📦 TRONG HỘP BAO GỒM
- Toàn bộ 3.599 mảnh LEGO Technic
- Sách hướng dẫn lắp ráp (quyển 1 + quyển 2)
- Hộp zin LEGO (có móp nhẹ góc)

⚙️ THÔNG SỐ KỸ THUẬT
- Mã set: 42083
- Số mảnh: 3.599 mảnh
- Kích thước mô hình hoàn thành: 56 x 25 x 14 cm
- Tính năng: cửa mở, nắp capo mở, hộp số 8 tốc độ hoạt động được, động cơ W16 mô phỏng
- Độ tuổi: 18+ (người lớn)
- Thời gian lắp ráp ước tính: 15-20 giờ

💬 LƯU Ý KHI MUA
- Set hiếm, đã ngừng sản xuất từ 2021, giá thị trường secondhand rất cao
- Phù hợp collector hoặc người yêu thích siêu xe và cơ khí
- Gặp mặt tại TP.HCM (quận Phú Nhuận)' WHERE `id` = 18;

-- Sản phẩm 19: Bộ sách
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG SÁCH
- Đọc 1 lần, còn rất mới, không gấp mép, không gạch chân
- Bìa không trầy xước, gáy không bị gãy
- Không có mùi ẩm mốc

📚 CHI TIẾT TỪNG CUỐN
1. Atomic Habits (James Clear) – Bản dịch tiếng Việt NXB Lao Động
   → Cuốn sách về xây dựng thói quen tốt và loại bỏ thói quen xấu
   → Bestseller #1 Amazon, bán 10+ triệu bản toàn thế giới

2. Deep Work (Cal Newport) – Bản dịch tiếng Việt NXB Lao Động
   → Phương pháp làm việc tập trung sâu trong thời đại nhiều xao nhãng
   → Dành cho lập trình viên, nhà nghiên cứu, người muốn tăng năng suất

3. Show Your Work! (Austin Kleon) – Bản dịch tiếng Việt
   → Hướng dẫn chia sẻ công việc sáng tạo và xây dựng thương hiệu cá nhân
   → Đọc nhanh, nhiều hình minh họa, phù hợp người làm sáng tạo

💬 LƯU Ý KHI MUA
- Giá 190.000đ cho cả 3 cuốn (giá gốc ~450.000đ)
- Không bán lẻ từng cuốn
- Gặp mặt tại Đà Nẵng hoặc ship toàn quốc (30.000đ)' WHERE `id` = 19;

-- Sản phẩm 20: Xe đạp Giant ATX 810
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG XE
- Dùng đạp xe cuối tuần khoảng 1.5 năm
- Khung nhôm không bị cong, không trầy sơn nặng
- Đã thay bánh trước + sau mới cách đây 2 tháng (lốp Kenda)
- Xích đã được vệ sinh và tra dầu mới
- Hệ thống phanh đĩa hydraulic còn rất tốt, ăn phanh nhạy

📦 BAO GỒM
- Xe đạp Giant ATX 810 size M (phù hợp chiều cao 165-180cm)
- Bơm tay mini (tặng kèm)
- Đèn trước LED (tặng kèm)

⚙️ THÔNG SỐ KỸ THUẬT
- Khung: Nhôm ALUXX Grade
- Fork: SR Suntour XCT 30mm travel
- Hệ thống truyền động: Shimano Altus 3x9 = 27 tốc độ
- Phanh: Tektro hydraulic disc brake
- Vành: Giant S-X2 double wall alloy
- Lốp: Kenda Honey Badger 27.5x2.2 (mới thay)
- Trọng lượng xe: ~13.5 kg

💬 LƯU Ý KHI MUA
- Phù hợp đạp địa hình, trail, commute hàng ngày
- Size M phù hợp người cao 165-180cm
- Gặp mặt tại TP.HCM (quận Bình Thạnh), có thể test ride' WHERE `id` = 20;

-- Sản phẩm 21: Vợt cầu lông Yonex Astrox 99 Pro
UPDATE `products` SET `description` =
'✅ TÌNH TRẠNG VỢT
- Mua tại shop ủy quyền Yonex (có hóa đơn)
- Dùng thi đấu phong trào 6 tháng, khoảng 3 buổi/tuần
- Khung vợt không nứt, không bị vênh
- Dây vợt BG65Ti còn nguyên, độ căng còn tốt (~24 lbs)
- Tay cầm quấn overgrip mới (Yonex AC102)

📦 TRONG HỘP BAO GỒM
- Vợt Yonex Astrox 99 Pro màu Cherry Sunburst
- Bao đựng vợt zin Yonex (full cover)
- Hóa đơn mua hàng (chứng minh hàng chính hãng)

⚙️ THÔNG SỐ KỸ THUẬT
- Độ cứng: Extra Stiff
- Cân bằng: Head Heavy (87mm)
- Trọng lượng: 4U (83g)
- Diện tích mặt vợt: 88 sq.in (567 cm²)
- Độ căng khuyến nghị: 21-30 lbs
- Vật liệu: HM Graphite + Tungsten
- Công nghệ: Rotational Generator System, Namd

💬 LƯU Ý KHI MUA
- Vợt tấn công đầu năng, phù hợp lối chơi smash mạnh
- Không phù hợp người mới tập (cần kỹ thuật tốt)
- Gặp mặt tại Hà Nội (quận Hai Bà Trưng)' WHERE `id` = 21;

-- Kiểm tra kết quả
SELECT id, LEFT(title, 40) AS ten_san_pham, LENGTH(description) AS do_dai_mo_ta
FROM products
ORDER BY id;