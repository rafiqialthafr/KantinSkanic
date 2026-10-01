# Product Requirement Document (PRD)

## Sistem Pre-Order Kantin Sekolah

## 1. Ringkasan Proyek (Project Overview)

* **Nama Aplikasi:** KantinSkanic
* **Deskripsi:** Sistem pemesanan makanan/minuman berbasis web untuk kantin sekolah. Siswa dapat langsung memesan makanan (pre-order) tanpa perlu membuat akun/login guna memangkas waktu antrean. Penjual kantin mengelola menu dan pesanan melalui dashboard khusus.
* **Teknologi Utama:**
  * **Framework:** Laravel (PHP 8.3+)
  * **Database:** MySQL
  * **Frontend:** Laravel Blade + Tailwind CSS
  * **Authentication:** Native Laravel Auth (Khusus Penjual & Admin)

---

## 2. Peran Pengguna (User Roles)

### A. Siswa / Pembeli (Guest Customer - Tanpa Login)
1. Membuka website tanpa harus register/login.
2. Memilih stand kantin dan melihat daftar menu yang tersedia.
3. Memasukkan makanan/minuman ke keranjang belanja.
4. Melakukan Checkout dengan mengisi:
   * **Nama Pemesan** (Contoh: Budi)
   * **Kelas** (Contoh: XI PPLG 1)
   * **Jam Pengambilan** (Istirahat 1 / Istirahat 2)
   * **Catatan Pesanan** (Opsional, contoh: "Pedas, esnya dikit")
5. Menerima **Kode Pesanan Unik** (contoh: `#PO-8921`) dan struk digital untuk ditunjukkan ke penjual.

### B. Penjual / Ibu Kantin (Vendor - Wajib Login)
1. Login ke Dashboard Penjual.
2. Kelola Menu Stand (Tambah, Edit, Hapus, Set Stok Habis/Tersedia).
3. Memantau pesanan masuk secara real-time khusus untuk stand milik mereka.
4. Mengubah status pesanan (*Pending* $\rightarrow$ *Diproses* $\rightarrow$ *Siap Diambil* $\rightarrow$ *Selesai*).

### C. Admin Sekolah (System Admin - Wajib Login)
1. Membuka akun untuk penjual baru dan mendaftarkan nama Stand Kantin.

---

## 3. Skema Database (MySQL ERD Design)

### 1. Tabel `users` (Khusus Penjual & Admin)
| Column Name | Type | Key | Description |
| --- | --- | --- | --- |
| `id` | BigInt (Primary) | PK | Unique ID User |
| `name` | String | - | Nama Penjual / Admin |
| `email` | String | Unique | Email Login |
| `password` | String | - | Hashed Password |
| `role` | Enum | - | `'penjual'`, `'admin'` |
| `timestamps` | Timestamp | - | `created_at`, `updated_at` |

### 2. Tabel `stands`
| Column Name | Type | Key | Description |
| --- | --- | --- | --- |
| `id` | BigInt (Primary) | PK | Unique ID Stand |
| `user_id` | BigInt | FK (`users.id`) | Owner/Pemilik Stand |
| `nama_stand` | String | - | Contoh: "Stand 1 - Bakso Mas Eko" |
| `nomor_stand` | String | - | Contoh: "Kantin No. 01" |
| `deskripsi` | Text | Nullable | Deskripsi singkat stand |
| `timestamps` | Timestamp | - | `created_at`, `updated_at` |

### 3. Tabel `menus`
| Column Name | Type | Key | Description |
| --- | --- | --- | --- |
| `id` | BigInt (Primary) | PK | Unique ID Menu |
| `stand_id` | BigInt | FK (`stands.id`) | Stand Pemilik |
| `nama_menu` | String | - | Nama makanan/minuman |
| `kategori` | Enum | - | `'makanan'`, `'minuman'`, `'snack'` |
| `harga` | Integer | - | Harga dalam Rupiah |
| `stok` | Integer | - | Jumlah stok |
| `is_available` | Boolean | - | `true` (Tersedia), `false` (Habis) |
| `foto` | String | Nullable | Path gambar menu |
| `timestamps` | Timestamp | - | `created_at`, `updated_at` |

### 4. Tabel `orders` (Guest Checkout)
| Column Name | Type | Key | Description |
| --- | --- | --- | --- |
| `id` | BigInt (Primary) | PK | Unique ID Pesanan |
| `kode_tr` | String | Unique | Kode Transaksi (misal: `PO-8921`) |
| `stand_id` | BigInt | FK (`stands.id`) | Stand tujuan pesanan |
| `nama_pemesan` | String | - | Nama siswa |
| `kelas` | String | - | Kelas siswa |
| `total_harga` | Integer | - | Total tagihan |
| `jam_pengambilan` | Enum | - | `'Istirahat 1'`, `'Istirahat 2'` |
| `status` | Enum | - | `'pending'`, `'diproses'`, `'siap_diambil'`, `'selesai'`, `'dibatalkan'` |
| `catatan` | Text | Nullable | Catatan pembeli |
| `timestamps` | Timestamp | - | `created_at`, `updated_at` |

### 5. Tabel `order_items`
| Column Name | Type | Key | Description |
| --- | --- | --- | --- |
| `id` | BigInt (Primary) | PK | Unique ID Detail Item |
| `order_id` | BigInt | FK (`orders.id`) | Relasi ke Parent Order |
| `menu_id` | BigInt | FK (`menus.id`) | Relasi ke Menu |
| `jumlah` | Integer | - | Jumlah porsi |
| `harga_satuan` | Integer | - | Harga per porsi |
| `subtotal` | Integer | - | `jumlah * harga_satuan` |
| `timestamps` | Timestamp | - | `created_at`, `updated_at` |

---

## 4. Alur Kerja Aplikasi (User Flow)

### A. Flow Pemesanan Siswa (Guest)
1. Siswa membuka web $\rightarrow$ Halaman Utama (Daftar Stand & Menu populer).
2. Siswa memilih Stand & menu yang diinginkan $\rightarrow$ Masuk ke Keranjang.
3. Siswa mengisi Form Pemesanan (**Nama**, **Kelas**, **Jam Pengambilan**, **Catatan**).
4. Siswa mengonfirmasi pesanan.
5. Sistem menerbitkan **Kode Pesanan** & Ringkasan Struk Digital.
6. Siswa mengambil pesanan di kantin sesuai jam yang dipilih dengan menyebutkan Kode Pesanan / Nama.

### B. Flow Pemrosesan Penjual
1. Penjual Login $\rightarrow$ Masuk ke Dashboard Penjual.
2. Penjual melihat pesanan masuk dengan status `pending`.
3. Penjual klik **"Terima & Proses"** (Status berubah jadi `diproses`).
4. Setelah pesanan selesai dimasak, penjual klik **"Siap Diambil"** (Status berubah jadi `siap_diambil`).
5. Siswa menyerahkan pembayaran di tempat (COD Kantin), penjual menyerahkan makanan lalu klik **"Selesai"**.

---

## 5. Rencana Tahapan Eksekusi (Implementation Steps for AI)

1. **Phase 1: Database & Model Creation**
   * Buat Migration & Model untuk `Stand`, `Menu`, `Order`, dan `OrderItem`.
   * Atur relasi Eloquent (`belongsTo`, `hasMany`).

2. **Phase 2: Authentication & Database Seeder**
   * Setup Auth sederhana untuk Penjual/Admin.
   * Buat `DatabaseSeeder` berisi dummy data (1 Stand Bakso, 1 Stand Es/Snack, beberapa menu, dan akun Penjual).

3. **Phase 3: Fitur Pembeli (Katalog & Guest Checkout)**
   * Tampilan Katalog Menu & Filter Stand.
   * Session/LocalStorage Cart & Form Checkout Guest.
   * Halaman Struk / Success Page dengan Kode Pesanan.

4. **Phase 4: Fitur Penjual (Dashboard & Management)**
   * CRUD Menu milik Stand sendiri.
   * Dashboard monitoring pesanan masuk & Ubah Status Pesanan.