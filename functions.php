<?php
// File: functions.php (Versi Final yang Sudah Diperbaiki)

// Selalu panggil file ini di setiap halaman yang memerlukan koneksi DB dan fungsi.
// config.php sudah bertanggung jawab untuk memulai sesi.
require_once "config.php";

/**
 * Memverifikasi login pengguna berdasarkan email, password, dan peran.
 * Menggunakan prepared statement untuk mencegah SQL Injection.
 *
 * @param mysqli $conn Objek koneksi database.
 * @param string $email Email pengguna.
 * @param string $password Password pengguna.
 * @param string $role Peran pengguna ('dosen' atau 'siswa').
 * @return bool True jika login berhasil, false jika gagal.
 */
function login($conn, $email, $password, $role) {
    try {
        // Menyiapkan query untuk mencari pengguna
        $stmt = $conn->prepare("SELECT id, name, email, role, password FROM users WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Jika pengguna ditemukan (satu baris)
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password yang di-hash
            if (password_verify($password, $user['password'])) {
                // Hapus password dari array sebelum disimpan di sesi
                unset($user['password']);

                // Regenerasi ID sesi untuk keamanan (mencegah session fixation)
                session_regenerate_id(true);
                
                // Simpan data pengguna ke dalam sesi
                $_SESSION['user'] = $user;
                return true;
            }
        }
        // Jika email, role, atau password salah
        return false;

    } catch (mysqli_sql_exception $e) {
        // Catat error ke log server untuk debugging, jangan tampilkan ke pengguna.
        error_log("Login Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Memeriksa apakah ada pengguna yang sedang login.
 *
 * @return bool True jika ada sesi 'user', false jika tidak.
 */
function isLoggedIn() {
    return isset($_SESSION['user']);
}

/**
 * Memeriksa apakah pengguna yang login adalah seorang 'dosen'.
 *
 * @return bool
 */
function isDosen() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'dosen';
}

/**
 * Memeriksa apakah pengguna yang login adalah seorang 'siswa'.
 *
 * @return bool
 */
function isSiswa() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'siswa';
}

/**
 * Mendapatkan data lengkap dari pengguna yang sedang login.
 *
 * @return array|null Mengembalikan array data pengguna atau null jika tidak login.
 */
function getUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Melakukan logout dengan cara menghancurkan sesi yang aktif.
 */
function logout() {
    // Hapus semua variabel dari array $_SESSION
    $_SESSION = [];

    // Hancurkan cookie sesi jika ada
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Hancurkan sesi secara permanen
    session_destroy();
}
?>
