-- ============================================================
--  Data contoh untuk pengembangan
-- ============================================================
USE ebisnis;

INSERT INTO kategori (nama, slug) VALUES
  ('Meja',        'meja'),
  ('Rak & Lemari','rak-lemari'),
  ('Kursi',       'kursi'),
  ('Dekorasi',    'dekorasi');

INSERT INTO bahan_baku (nama, satuan, stok) VALUES
  ('Kayu jati bekas peti kemas', 'kg',     500),
  ('Kayu pinus palet bekas',     'kg',     800),
  ('Besi hollow daur ulang',     'batang', 120),
  ('Cat water-based ramah lingkungan', 'liter', 60);

INSERT INTO produk (kategori_id, nama, slug, deskripsi, dimensi, harga, berat_gram, stok, gambar) VALUES
  (1, 'Meja Belajar Minimalis Jati Reclaimed', 'meja-belajar-jati-reclaimed',
      'Meja belajar dari kayu jati bekas peti kemas, finishing natural water-based.',
      '120 x 60 x 75 cm', 1250000, 18000, 12, 'meja-belajar.jpg'),
  (2, 'Rak Buku 4 Tingkat Kayu Palet', 'rak-buku-4-tingkat-palet',
      'Rak buku kokoh dari kayu pinus palet daur ulang, cocok untuk ruang kerja.',
      '80 x 30 x 150 cm', 890000, 15000, 8, 'rak-buku.jpg'),
  (3, 'Kursi Cafe Industrial Reclaimed', 'kursi-cafe-industrial',
      'Kursi dengan dudukan kayu reclaimed dan kaki besi hollow daur ulang.',
      '45 x 45 x 90 cm', 450000, 6000, 25, 'kursi-cafe.jpg'),
  (4, 'Lampu Hias Gantung Kayu', 'lampu-hias-gantung-kayu',
      'Dekorasi lampu gantung dari potongan kayu sisa produksi.',
      '25 x 25 x 30 cm', 275000, 2000, 30, 'lampu-hias.jpg'),
  (1, 'Meja Kopi Bulat Live Edge', 'meja-kopi-bulat-live-edge',
      'Meja kopi dengan tepi alami (live edge) dari potongan kayu utuh.',
      'Diameter 70 x 40 cm', 1650000, 22000, 5, 'meja-kopi.jpg'),
  (2, 'Lemari Pajang 2 Pintu', 'lemari-pajang-2-pintu',
      'Lemari pajang dua pintu dari kombinasi kayu jati dan pinus reclaimed.',
      '100 x 40 x 180 cm', 2350000, 35000, 4, 'lemari-pajang.jpg');

-- Komposisi bahan (many-to-many)
INSERT INTO produk_bahan_baku (produk_id, bahan_baku_id, jumlah) VALUES
  (1, 1, 12), (1, 4, 0.5),
  (2, 2, 10), (2, 4, 0.4),
  (3, 2, 3),  (3, 3, 1), (3, 4, 0.2),
  (4, 2, 1.5),
  (5, 1, 15), (5, 4, 0.6),
  (6, 1, 20), (6, 2, 8), (6, 4, 0.8);
