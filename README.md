# Share File

Share File adalah aplikasi manajemen dokumen berbasis web yang dibangun dengan framework Laravel. Aplikasi ini memungkinkan Anda untuk mengunggah, mengelola, dan mengorganisir file dan folder dengan antarmuka modern yang menggunakan gaya desain _Glassmorphism_.

## ✨ Fitur Utama

- 📁 **Manajemen Folder**: Buat struktur folder tanpa batas dan organisir file Anda dengan rapi.
- ☁️ **Upload & Drag-and-Drop (OS)**: Unggah file dengan sangat cepat dan intuitif. Cukup tarik file dari File Explorer di komputer Anda dan lepaskan (drop) ke area aplikasi untuk langsung mengunggahnya.
- 🔄 **Pindahkan File/Folder**: Susun ulang file Anda dengan menarik baris file (drag) dan melepaskannya (drop) ke dalam folder tujuan.
- 🔍 **Pencarian Dokumen**: Cari file atau folder dengan cepat berdasarkan nama atau pemilik.
- 📝 **Editor Dokumen Web**: Buat dan edit dokumen teks sederhana (HTML/JSON) langsung dari dalam browser.
- 📦 **Download Folder (ZIP)**: Unduh seluruh isi folder sekaligus dalam bentuk satu file ZIP.
- 🎨 **UI/UX Modern**: Desain antarmuka _Glassmorphism_ yang elegan, responsif, dilengkapi animasi mikro untuk pengalaman pengguna yang mewah.
- 📎 **Integrasi MS Office Local**: Buka dan edit file Word/Excel secara otomatis menggunakan aplikasi MS Office lokal (jika diakses melalui jaringan lokal/SMB).

## 📋 Persyaratan Sistem

- **PHP**: Versi 8.1 atau lebih baru
- **Composer**: Untuk manajemen dependensi PHP
- **Ekstensi PHP**: OpenSSL, PDO, Mbstring, Tokenizer, XML, Zip
- **Database**: MySQL, PostgreSQL, atau SQLite

## 🚀 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di lingkungan lokal Anda:

1. **Buka terminal dan arahkan ke direktori proyek:**

    ```bash
    cd path/to/dashboard
    ```

2. **Instal dependensi proyek menggunakan Composer:**

    ```bash
    composer install
    ```

3. **Konfigurasi Environment:**
   Salin file konfigurasi bawaan dan sesuaikan kredensial database Anda.

    ```bash
    cp .env.example .env
    ```

    _Buka file `.env` dan pastikan pengaturan `DB_DATABASE`, `DB_USERNAME`, dll. sudah sesuai dengan database Anda._

4. **Generate Application Key:**

    ```bash
    php artisan key:generate
    ```

5. **Migrasi Database:**
   Buat tabel-tabel yang diperlukan di database.

    ```bash
    php artisan migrate
    ```

6. **Tautkan Storage Direktori:**
   Agar file yang diunggah dapat diakses dari browser, Anda harus membuat symlink storage.

    ```bash
    php artisan storage:link
    ```

7. **Jalankan Server Lokal:**

    ```bash
    php artisan serve
    ```

8. **Selesai!**
   Buka browser Anda dan akses aplikasi di: `http://localhost:8000/data-file`

## 💡 Tips Penggunaan Drag and Drop File Explorer

Fitur ini sangat memudahkan Anda mengunggah file tanpa harus membuka jendela pencarian file:

1. Buka halaman **Data File** di browser Anda.
2. Buka jendela **File Explorer** (Windows) atau **Finder** (Mac).
3. Klik dan tahan file yang ingin Anda unggah.
4. Seret (drag) file tersebut ke atas halaman web Share File (ke dalam area kotak data putih). Layar akan memunculkan indikator Drop Zone biru.
5. Lepaskan (drop) file, dan sistem akan langsung mengunggah file tersebut!

## 📄 Lisensi

Proyek ini bersifat _Open-Source_ dan dirilis di bawah lisensi [MIT](https://opensource.org/licenses/MIT). Framework Laravel adalah hak cipta milik Taylor Otwell.
