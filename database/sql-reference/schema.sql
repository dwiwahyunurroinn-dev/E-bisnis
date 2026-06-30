-- ============================================================
--  E-Bisnis : Furnitur Kayu Daur Ulang
--  Skema Database (MySQL 8+)
--  Fokus Fase 1-2: Inventory real-time + Checkout
-- ============================================================

CREATE DATABASE IF NOT EXISTS ebisnis
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ebisnis;

-- Urutan drop dibalik agar tidak melanggar foreign key
DROP TABLE IF EXISTS detail_pesanan;
DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS alamat;
DROP TABLE IF EXISTS pelanggan;
DROP TABLE IF EXISTS produk_bahan_baku;
DROP TABLE IF EXISTS bahan_baku;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;

-- ------------------------------------------------------------
--  Katalog
-- ------------------------------------------------------------
CREATE TABLE kategori (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    nama  VARCHAR(80)  NOT NULL,
    slug  VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE produk (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT          NOT NULL,
    nama        VARCHAR(150) NOT NULL,
    slug        VARCHAR(180) NOT NULL UNIQUE,
    deskripsi   TEXT,
    dimensi     VARCHAR(100),                 -- mis. "120 x 60 x 75 cm"
    harga       DECIMAL(12,2) NOT NULL DEFAULT 0,
    berat_gram  INT           NOT NULL DEFAULT 1000,  -- utk hitung ongkir
    stok        INT           NOT NULL DEFAULT 0,
    gambar      VARCHAR(255),
    status      ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori
        FOREIGN KEY (kategori_id) REFERENCES kategori(id),
    INDEX idx_produk_status (status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Inventory / Supply Chain : bahan baku daur ulang
--  Relasi many-to-many produk <-> bahan_baku
-- ------------------------------------------------------------
CREATE TABLE bahan_baku (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    nama    VARCHAR(120) NOT NULL,            -- mis. "Kayu jati bekas peti kemas"
    satuan  VARCHAR(20)  NOT NULL DEFAULT 'kg',
    stok    DECIMAL(12,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE produk_bahan_baku (
    produk_id     INT NOT NULL,
    bahan_baku_id INT NOT NULL,
    jumlah        DECIMAL(12,2) NOT NULL DEFAULT 0, -- bahan dipakai per 1 produk
    PRIMARY KEY (produk_id, bahan_baku_id),
    CONSTRAINT fk_pbb_produk
        FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE,
    CONSTRAINT fk_pbb_bahan
        FOREIGN KEY (bahan_baku_id) REFERENCES bahan_baku(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Pelanggan & alamat pengiriman
-- ------------------------------------------------------------
CREATE TABLE pelanggan (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nama       VARCHAR(120) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    telepon    VARCHAR(25),
    password   VARCHAR(255) NOT NULL,         -- simpan hasil password_hash()
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE alamat (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    pelanggan_id  INT NOT NULL,
    label         VARCHAR(40),                -- "Rumah", "Kantor"
    penerima      VARCHAR(120) NOT NULL,
    telepon       VARCHAR(25)  NOT NULL,
    provinsi      VARCHAR(80),
    kota          VARCHAR(80),
    kota_id       INT,                        -- id kota utk RajaOngkir
    kecamatan     VARCHAR(80),
    alamat_lengkap TEXT NOT NULL,
    kode_pos      VARCHAR(10),
    CONSTRAINT fk_alamat_pelanggan
        FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Pesanan (single-page checkout)
-- ------------------------------------------------------------
CREATE TABLE pesanan (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    kode         VARCHAR(30) NOT NULL UNIQUE,  -- mis. "INV-20260630-0001"
    pelanggan_id INT NOT NULL,
    alamat_id    INT NOT NULL,
    kurir        VARCHAR(20),                  -- "jne","pos","tiki"
    layanan      VARCHAR(40),                  -- "REG","YES","OKE"
    subtotal     DECIMAL(12,2) NOT NULL DEFAULT 0,
    ongkir       DECIMAL(12,2) NOT NULL DEFAULT 0,
    total        DECIMAL(12,2) NOT NULL DEFAULT 0,
    metode_bayar VARCHAR(40),                  -- "midtrans","xendit","transfer"
    status       ENUM('pending','lunas','diproses','dikirim','selesai','batal')
                 NOT NULL DEFAULT 'pending',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pesanan_pelanggan
        FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id),
    CONSTRAINT fk_pesanan_alamat
        FOREIGN KEY (alamat_id) REFERENCES alamat(id),
    INDEX idx_pesanan_status (status)
) ENGINE=InnoDB;

CREATE TABLE detail_pesanan (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id  INT NOT NULL,
    produk_id   INT NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,         -- snapshot nama saat dibeli
    harga       DECIMAL(12,2) NOT NULL,        -- snapshot harga saat dibeli
    jumlah      INT NOT NULL,
    subtotal    DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_detail_pesanan
        FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    CONSTRAINT fk_detail_produk
        FOREIGN KEY (produk_id) REFERENCES produk(id)
) ENGINE=InnoDB;

-- ============================================================
--  TRIGGER : kurangi stok otomatis saat pesanan LUNAS
--  (real-time inventory). Hanya jalan saat status berubah
--  menjadi 'lunas' agar tidak dobel pengurangan.
-- ============================================================
DELIMITER //

CREATE TRIGGER trg_kurangi_stok_produk
AFTER UPDATE ON pesanan
FOR EACH ROW
BEGIN
    IF NEW.status = 'lunas' AND OLD.status <> 'lunas' THEN
        -- Kurangi stok produk jadi
        UPDATE produk p
        JOIN detail_pesanan dp ON dp.produk_id = p.id
        SET p.stok = p.stok - dp.jumlah
        WHERE dp.pesanan_id = NEW.id;

        -- Kurangi stok bahan baku sesuai komposisi (supply chain)
        UPDATE bahan_baku b
        JOIN produk_bahan_baku pbb ON pbb.bahan_baku_id = b.id
        JOIN detail_pesanan dp      ON dp.produk_id = pbb.produk_id
        SET b.stok = b.stok - (pbb.jumlah * dp.jumlah)
        WHERE dp.pesanan_id = NEW.id;
    END IF;
END//

DELIMITER ;
