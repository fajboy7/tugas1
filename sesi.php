<?php
// File: sesi.php (Versi Responsif dan Telah Diperbaiki)

// =================================================================
// PERBAIKAN: Mengatur Zona Waktu ke Waktu Indonesia Barat (WIB)
// Ini memastikan semua fungsi waktu (strtotime, dll) dan input
// tanggal/waktu diproses sesuai dengan waktu lokal Indonesia.
date_default_timezone_set('Asia/Jakarta');
// =================================================================

// Memanggil file konfigurasi dan fungsi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "config.php";
require_once "functions.php";

// Pastikan hanya dosen yang bisa mengakses
if (!isDosen()) {
    header("Location: index.php");
    exit;
}

$user = getUser();
$userId = $user['id'];
$page_title = 'Buat Sesi Absensi';
$error = null;

// --- PENANGANAN FORM PEMBUATAN SESI ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['buatSesi'])) {
    $kelasId = filter_input(INPUT_POST, 'kelasId', FILTER_VALIDATE_INT);
    $waktuBuka = $_POST['waktuBuka'];
    $waktuTutup = $_POST['waktuTutup'];

    // Validasi input
    if (empty($kelasId) || empty($waktuBuka) || empty($waktuTutup)) {
        $error = "Semua kolom wajib diisi.";
    } elseif (strtotime($waktuBuka) >= strtotime($waktuTutup)) {
        $error = "Waktu tutup harus setelah waktu buka.";
    } else {
        try {
            // Menggunakan prepared statement untuk keamanan dan nama kolom yang benar
            $stmt = $conn->prepare(
                "INSERT INTO sesiabsensis (status, createdAt, updatedAt, kelasId, waktuBuka, waktuTutup)
                 VALUES ('terbuka', NOW(), NOW(), ?, ?, ?)"
            );
            $stmt->bind_param("iss", $kelasId, $waktuBuka, $waktuTutup);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Sesi absensi baru berhasil dibuat!";
                header("Location: dashboard_dosen.php");
                exit;
            } else {
                $error = "Gagal membuat sesi absensi. Silakan coba lagi.";
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            error_log("Gagal membuat sesi: " . $e->getMessage());
            $error = "Terjadi kesalahan pada server: " . $e->getMessage();
        }
    }
}

// --- MENGAMBIL DAFTAR KELAS MILIK DOSEN ---
$kelas_dosen = [];
try {
    // Menggunakan nama kolom yang benar (dosenId)
    $stmt_kelas = $conn->prepare("SELECT id, namaKelas FROM kelas WHERE dosenId = ? ORDER BY namaKelas ASC");
    $stmt_kelas->bind_param("i", $userId);
    $stmt_kelas->execute();
    $kelas_dosen = $stmt_kelas->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_kelas->close();
} catch (mysqli_sql_exception $e) {
    error_log("Gagal mengambil daftar kelas: " . $e->getMessage());
    $error = "Tidak dapat memuat daftar kelas.";
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Aplikasi Absensi</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        :root {
            --primary-color: #4F46E5; --primary-hover: #4338CA; --secondary-color: #6B7280;
            --text-primary: #111827; --text-light: #FFFFFF; --bg-light: #F9FAFB; --bg-white: #FFFFFF;
            --border-color: #E5E7EB; --error-bg: #FEE2E2; --error-text: #991B1B;
            --radius: 8px; --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); color: var(--text-primary); }
        a { color: var(--primary-color); text-decoration: none; }
        .icon { width: 20px; height: 20px; flex-shrink: 0; }
        .app-layout { position: relative; min-height: 100vh; }
        .sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: 260px; background: var(--bg-white); border-right: 1px solid var(--border-color); padding: 1.5rem; display: flex; flex-direction: column; transition: transform 0.3s ease-in-out; z-index: 2000;}
        .main-content { transition: margin-left 0.3s ease-in-out; margin-left: 260px; padding: 2rem; }
        .sidebar-header { font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; padding-bottom: 1.5rem; }
        .sidebar-nav { flex-grow: 1; display: flex; flex-direction: column; gap: 0.5rem; }
        .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); font-weight: 500; color: var(--secondary-color); transition: all 0.2s ease; }
        .sidebar-nav a:hover { background-color: var(--bg-light); color: var(--text-primary); }
        .sidebar-nav a.active { background-color: var(--primary-color); color: var(--text-light); }
        .sidebar-footer { margin-top: auto; }
        .main-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .main-header h1 { font-size: 1.75rem; margin: 0; }
        .user-profile { display: flex; align-items: center; gap: 0.75rem; }
        .user-profile span { font-weight: 600; display: none; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background-color: var(--primary-color); color: var(--text-light); display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .card { background: var(--bg-white); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow-sm); max-width: 600px; margin: 0 auto; }
        .card-header { padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1.5rem; }
        .card-header h3 { font-size: 1.25rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-control { width: 100%; padding: 0.75rem; border-radius: var(--radius); border: 1px solid var(--border-color); font-size: 1rem; transition: border-color 0.2s, box-shadow 0.2s; -webkit-appearance: none; appearance: none; }
        select.form-control { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; }
        .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2); }
        .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; }
        .msg-error { padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; background: var(--error-bg); color: var(--error-text); }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-weight: 600; padding: 0.75rem 1.25rem; border-radius: var(--radius); border: 1px solid transparent; cursor: pointer; transition: all 0.2s ease; font-size: 0.9rem; }
        .btn-primary { background-color: var(--primary-color); color: var(--text-light); }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-outline { background-color: transparent; color: var(--secondary-color); border-color: var(--border-color); }
        .btn-outline:hover { background-color: var(--bg-light); color: var(--text-primary); }
        .btn.w-full { width: 100%; }
        .menu-toggle { display: none; background: none; border: none; cursor: pointer; padding: 0.5rem; }
        
        @media (min-width: 768px) {
            .user-profile span { display: inline; }
        }
        @media (max-width: 767px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1.5rem; }
            .main-header h1 { font-size: 1.5rem; }
            .menu-toggle { display: block; }
            .card { padding: 1rem; }
            .form-actions { flex-direction: column-reverse; gap: 0.75rem; }
            .form-actions .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <span>AbsensiKu</span>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Dashboard</span>
                </a>
                <a href="dashboard_dosen.php" class="active">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    <span>Manajemen Kelas</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="btn btn-outline w-full">Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="menu-toggle" id="menu-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </button>
                    <h1><?= htmlspecialchars($page_title) ?></h1>
                </div>
                <div class="user-profile">
                    <span><?= htmlspecialchars($user['name'] ?? 'Dosen') ?></span>
                    <div class="avatar"><?= htmlspecialchars(strtoupper(substr($user['name'] ?? 'D', 0, 1))) ?></div>
                </div>
            </header>

            <div class="card">
                <div class="card-header">
                    <h3>Detail Sesi Absensi</h3>
                </div>
                
                <?php if ($error): ?>
                    <div class="msg-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="sesi.php">
                    <div class="form-group">
                        <label for="kelasId">Pilih Kelas</label>
                        <select name="kelasId" id="kelasId" class="form-control" required>
                            <option value="" disabled selected>-- Pilih salah satu kelas --</option>
                            <?php if (empty($kelas_dosen)): ?>
                                <option value="" disabled>Anda belum memiliki kelas</option>
                            <?php else: ?>
                                <?php foreach ($kelas_dosen as $kelas): ?>
                                    <option value="<?= $kelas['id'] ?>"><?= htmlspecialchars($kelas['namaKelas']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="waktuBuka">Waktu Buka</label>
                        <input type="datetime-local" name="waktuBuka" id="waktuBuka" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="waktuTutup">Waktu Tutup</label>
                        <input type="datetime-local" name="waktuTutup" id="waktuTutup" class="form-control" required>
                    </div>
                    <div class="form-actions">
                        <a href="dashboard_dosen.php" class="btn btn-outline">Kembali</a>
                        <button type="submit" name="buatSesi" class="btn btn-primary" <?= empty($kelas_dosen) ? 'disabled' : '' ?>>
                            Buat Sesi
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script>
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menu-toggle');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        document.addEventListener('click', (event) => {
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>