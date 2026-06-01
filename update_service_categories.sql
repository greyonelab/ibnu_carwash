-- ============================================
-- SQL Script untuk Update Kategori Layanan
-- ============================================
-- File ini berisi query untuk update kategori layanan yang sudah ada
-- Jalankan setelah migration berhasil

-- 1. Set default kategori 'mobil' untuk semua layanan yang belum ada kategori
UPDATE services 
SET category = 'mobil' 
WHERE category IS NULL;

-- 2. Update kategori berdasarkan nama layanan (contoh)
-- Sesuaikan dengan data layanan Anda

-- Update layanan motor
UPDATE services 
SET category = 'motor' 
WHERE name LIKE '%Motor%' 
   OR name LIKE '%motor%'
   OR name LIKE '%Sepeda Motor%'
   OR name LIKE '%Motorcycle%';

-- Update layanan mobil (jika ada kata kunci spesifik)
UPDATE services 
SET category = 'mobil' 
WHERE name LIKE '%Mobil%' 
   OR name LIKE '%mobil%'
   OR name LIKE '%Car%'
   OR name LIKE '%Sedan%'
   OR name LIKE '%SUV%'
   OR name LIKE '%MPV%'
   OR name LIKE '%Hatchback%';

-- Update layanan lainnya (truk, bus, dll)
UPDATE services 
SET category = 'lainnya' 
WHERE name LIKE '%Truk%' 
   OR name LIKE '%truk%'
   OR name LIKE '%Bus%'
   OR name LIKE '%bus%'
   OR name LIKE '%Truck%'
   OR name LIKE '%Pickup%';

-- 3. Verifikasi hasil update
SELECT 
    id,
    name,
    type,
    category,
    price,
    is_active
FROM services
ORDER BY category, name;

-- 4. Cek layanan yang masih NULL (seharusnya tidak ada)
SELECT 
    id,
    name,
    category
FROM services
WHERE category IS NULL;

-- 5. Statistik kategori
SELECT 
    category,
    COUNT(*) as total_layanan,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as aktif,
    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as nonaktif
FROM services
GROUP BY category;
