-- ============================================
-- Sample Data Layanan dengan Kategori
-- ============================================
-- File ini berisi contoh data layanan untuk testing
-- Jalankan setelah migration berhasil

-- Hapus data sample lama jika ada (opsional)
-- DELETE FROM services WHERE id > 0;

-- ============================================
-- LAYANAN MOBIL
-- ============================================

-- Layanan Mobil - Standard
INSERT INTO services (name, description, price, duration_minutes, type, category, is_active, created_at, updated_at) VALUES
('Cuci Mobil Standard', 'Cuci eksterior mobil dengan shampo khusus', 25000, 30, 'standard', 'mobil', 1, NOW(), NOW()),
('Cuci Mobil Express', 'Cuci cepat eksterior mobil', 20000, 20, 'standard', 'mobil', 1, NOW(), NOW());

-- Layanan Mobil - Premium
INSERT INTO services (name, description, price, duration_minutes, type, category, is_active, created_at, updated_at) VALUES
('Cuci Mobil Premium', 'Cuci eksterior + interior + vacuum', 50000, 60, 'premium', 'mobil', 1, NOW(), NOW()),
('Cuci Mobil Premium Plus', 'Cuci eksterior + interior + vacuum + poles dashboard', 65000, 75, 'premium', 'mobil', 1, NOW(), NOW());

-- Layanan Mobil - Detail
INSERT INTO services (name, description, price, duration_minutes, type, category, is_active, created_at, updated_at) VALUES
('Cuci Mobil Detail', 'Cuci lengkap + poles + wax + interior detail', 100000, 120, 'detail', 'mobil', 1, NOW(), NOW()),
('Cuci Mobil Detail Premium', 'Cuci lengkap + coating + interior detail + engine bay', 150000, 180, 'detail', 'mobil', 1, NOW(), NOW());

-- ============================================
-- LAYANAN MOTOR
-- ============================================

-- Layanan Motor - Standard
INSERT INTO services (name, description, price, duration_minutes, type, category, is_active, created_at, updated_at) VALUES
('Cuci Motor Standard', 'Cuci eksterior motor dengan shampo khusus', 15000, 20, 'standard', 'motor', 1, NOW(), NOW()),
('Cuci Motor Express', 'Cuci cepat motor', 10000, 15, 'standard', 'motor', 1, NOW(), NOW());

-- Layanan Motor - Premium
INSERT INTO services (name, description, price, duration_minutes, type, category, is_active, created_at, updated_at) VALUES
('Cuci Motor Premium', 'Cuci motor + semir ban + poles body', 25000, 30, 'premium', 'motor', 1, NOW(), NOW()),
('Cuci Motor Premium Plus', 'Cuci motor + semir ban + poles body + pembersihan mesin', 35000, 45, 'premium', 'motor', 1, NOW(), NOW());

-- Layanan Motor - Detail
INSERT INTO services (name, description, price, duration_minutes, type, category, is_active, created_at, updated_at) VALUES
('Cuci Motor Detail', 'Cuci lengkap + poles + wax + pembersihan mesin detail', 50000, 60, 'detail', 'motor', 1, NOW(), NOW());

-- ============================================
-- LAYANAN LAINNYA (Truk, Bus, dll)
-- ============================================

-- Layanan Lainnya - Standard
INSERT INTO services (name, description, price, duration_minutes, type, category, is_active, created_at, updated_at) VALUES
('Cuci Truk Standard', 'Cuci eksterior truk/pickup', 75000, 60, 'standard', 'lainnya', 1, NOW(), NOW()),
('Cuci Bus Standard', 'Cuci eksterior bus', 100000, 90, 'standard', 'lainnya', 1, NOW(), NOW());

-- Layanan Lainnya - Premium
INSERT INTO services (name, description, price, duration_minutes, type, category, is_active, created_at, updated_at) VALUES
('Cuci Truk Premium', 'Cuci eksterior + interior truk/pickup', 125000, 90, 'premium', 'lainnya', 1, NOW(), NOW()),
('Cuci Bus Premium', 'Cuci eksterior + interior bus', 175000, 120, 'premium', 'lainnya', 1, NOW(), NOW());

-- Layanan Lainnya - Detail
INSERT INTO services (name, description, price, duration_minutes, type, category, is_active, created_at, updated_at) VALUES
('Cuci Truk Detail', 'Cuci lengkap truk + poles + interior detail', 200000, 150, 'detail', 'lainnya', 1, NOW(), NOW());

-- ============================================
-- VERIFIKASI DATA
-- ============================================

-- Tampilkan semua layanan yang baru ditambahkan
SELECT 
    id,
    name,
    type,
    category,
    CONCAT('Rp ', FORMAT(price, 0, 'id_ID')) as harga,
    duration_minutes as durasi_menit,
    CASE WHEN is_active = 1 THEN 'Aktif' ELSE 'Nonaktif' END as status
FROM services
ORDER BY category, type, price;

-- Statistik per kategori
SELECT 
    category as Kategori,
    COUNT(*) as 'Total Layanan',
    MIN(price) as 'Harga Minimum',
    MAX(price) as 'Harga Maximum',
    AVG(price) as 'Harga Rata-rata'
FROM services
GROUP BY category
ORDER BY category;

-- Statistik per tipe
SELECT 
    type as Tipe,
    COUNT(*) as 'Total Layanan',
    MIN(price) as 'Harga Minimum',
    MAX(price) as 'Harga Maximum',
    AVG(price) as 'Harga Rata-rata'
FROM services
GROUP BY type
ORDER BY 
    CASE type
        WHEN 'standard' THEN 1
        WHEN 'premium' THEN 2
        WHEN 'detail' THEN 3
    END;

-- Statistik kombinasi kategori dan tipe
SELECT 
    category as Kategori,
    type as Tipe,
    COUNT(*) as 'Total Layanan',
    AVG(price) as 'Harga Rata-rata'
FROM services
GROUP BY category, type
ORDER BY category, 
    CASE type
        WHEN 'standard' THEN 1
        WHEN 'premium' THEN 2
        WHEN 'detail' THEN 3
    END;
