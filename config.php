<?php
// File: config.php (Versi Final yang Sudah Dimodifikasi)

// --- 1. KONFIGURASI KONEKSI DATABASE ---
// Definisikan konstanta untuk detail koneksi agar mudah dikelola.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'absensi_db1');

// --- 2. PENGATURAN LAPORAN ERROR MYSQLI ---
// Mengaktifkan pelaporan error untuk mysqli. 
// Ini akan membuat PHP otomatis melempar 'exception' jika ada kesalahan pada query SQL.
// Sangat berguna selama masa pengembangan (development).
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// --- 3. MEMBUAT KONEKSI DATABASE ---
// Membuat instance objek mysqli baru untuk koneksi.
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Periksa apakah koneksi berhasil dibuat.
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan eksekusi skrip dan tampilkan pesan yang ramah.
    // Hindari menampilkan detail error teknis seperti $conn->connect_error di lingkungan production.
    error_log("Database Connection Failed: " . $conn->connect_error); // Catat error sebenarnya di log server
    die("Tidak dapat terhubung ke server database. Mohon coba lagi nanti.");
}

// --- 4. MENGATUR CHARACTER SET ---
// Mengatur character set koneksi ke 'utf8mb4' untuk mendukung berbagai macam karakter, termasuk emoji.
$conn->set_charset("utf8mb4");

// --- 5. MANAJEMEN SESI (SESSION) ---
// Bagian ini adalah perbaikan utama.
// Memeriksa apakah sesi sudah aktif sebelum mencoba memulainya.
// Ini akan mencegah notifikasi "session_start(): Ignoring session_start() because a session is already active".
if (session_status() == PHP_SESSION_NONE) {
    // Jika belum ada sesi yang aktif, maka mulai sesi baru.
    session_start([
        // Mengatur masa berlaku cookie sesi (dalam detik). 86400 detik = 1 hari.
        'cookie_lifetime' => 86400,
        
        // Opsi 'read_and_close' dapat meningkatkan performa pada halaman yang hanya membaca data sesi.
        // Namun, ini akan mencegah penulisan data ke sesi setelahnya (misalnya, untuk flash messages).
        // Sebaiknya nonaktifkan (beri komentar) jika Anda menggunakan sistem notifikasi berbasis sesi.
        // 'read_and_close'  => true,
    ]);
}
?>
