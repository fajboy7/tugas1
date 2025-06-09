<?php
// File: absen.php (Versi dengan Perbaikan Logika Waktu & Nama Kolom)

// Atur zona waktu default ke Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

// Memanggil file konfigurasi dan fungsi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "config.php";
require_once "functions.php";

// Pastikan hanya siswa yang bisa mengakses
if (!isSiswa()) {
    header("Location: index.php");
    exit;
}

// Inisialisasi variabel
$user = getUser();
$userId = $user['id'];
$page_title = 'Lakukan Absensi';
$error = null;
$sesi = null;
$sudah_absen = false;

try {
    $waktuSekarang = date('Y-m-d H:i:s');

    // 1. Cari sesi absensi yang sedang terbuka untuk kelas yang diikuti siswa
    // PERUBAHAN: Mengganti semua 'kelasId' menjadi 'kelasid' sesuai struktur database.
    $query_sesi = "SELECT s.id, k.namaKelas, s.waktuTutup 
                   FROM sesiabsensis s
                   JOIN kelassiswas ks ON s.kelasid = ks.kelasid
                   JOIN kelas k ON s.kelasid = k.id
                   WHERE ks.userId = ? 
                     AND s.status = 'terbuka' 
                     AND ? BETWEEN s.waktuBuka AND s.waktuTutup
                   ORDER BY s.waktuBuka DESC 
                   LIMIT 1";
                      
    $stmt_sesi = $conn->prepare($query_sesi);
    $stmt_sesi->bind_param("is", $userId, $waktuSekarang);
    $stmt_sesi->execute();
    $sesi = $stmt_sesi->get_result()->fetch_assoc();
    $stmt_sesi->close();

    // 2. Jika sesi ditemukan, cek apakah siswa sudah melakukan absensi untuk sesi ini
    if ($sesi) {
        $stmt_cek = $conn->prepare("SELECT id FROM absensis WHERE sesiId = ? AND userId = ?");
        $stmt_cek->bind_param("ii", $sesi['id'], $userId);
        $stmt_cek->execute();
        if ($stmt_cek->get_result()->num_rows > 0) {
            $sudah_absen = true;
        }
        $stmt_cek->close();
    }

    // 3. Proses form jika disubmit
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['lakukanAbsen']) && $sesi && !$sudah_absen) {
        $status = $_POST['status'];
        $allowed_statuses = ['hadir', 'izin', 'sakit'];
        
        if (in_array($status, $allowed_statuses)) {
            $waktuAbsen = $waktuSekarang;
            $stmt_insert = $conn->prepare(
                "INSERT INTO absensis (waktuAbsen, status, createdAt, updatedAt, sesiId, userId)
                 VALUES (?, ?, NOW(), NOW(), ?, ?)"
            );
            $stmt_insert->bind_param("ssii", $waktuAbsen, $status, $sesi['id'], $userId);
            
            if ($stmt_insert->execute()) {
                $_SESSION['success_message'] = "Absensi Anda untuk kelas '" . htmlspecialchars($sesi['namaKelas']) . "' telah berhasil direkam!";
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Gagal merekam absensi. Silakan coba lagi.";
            }
            $stmt_insert->close();
        } else {
            $error = "Status absensi tidak valid.";
        }
    }

} catch (mysqli_sql_exception $e) {
    error_log("Absen Error: " . $e->getMessage());
    $error = "Terjadi kesalahan pada server. Tidak dapat memproses absensi. Pesan: " . htmlspecialchars($e->getMessage());
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
            --border-color: #E5E7EB; --success-bg: #dcfce7; --success-text: #166534;
            --info-bg: #eff6ff; --info-text: #1e40af; --error-bg: #FEE2E2; --error-text: #991B1B;
            --radius: 8px; --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); color: var(--text-primary); line-height: 1.5; }
        a { color: var(--primary-color); text-decoration: none; }
        .icon { width: 20px; height: 20px; flex-shrink: 0; }
        .app-layout { position: relative; min-height: 100vh; }
        .sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: 260px; background: var(--bg-white); border-right: 1px solid var(--border-color); padding: 1.5rem; display: flex; flex-direction: column; transition: transform 0.3s ease-in-out; z-index: 2000; }
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
        .form-control { width: 100%; padding: 0.75rem; border-radius: var(--radius); border: 1px solid var(--border-color); font-size: 1rem; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2); }
        .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; }
        .info-box, .msg-error { padding: 1.25rem; border-radius: var(--radius); text-align: center; }
        .info-box { background: var(--info-bg); color: var(--info-text); }
        .info-box h4 { font-size: 1.1rem; margin-bottom: 0.25rem; color: #1d4ed8; }
        .msg-error { background: var(--error-bg); color: var(--error-text); margin-bottom: 1.5rem; }
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
                <a href="absen.php" class="active">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span>Lakukan Absensi</span>
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
                    <span><?= htmlspecialchars($user['name'] ?? 'Siswa') ?></span>
                    <div class="avatar"><?= htmlspecialchars(strtoupper(substr($user['name'] ?? 'S', 0, 1))) ?></div>
                </div>
            </header>

            <div class="card">
                <?php if ($error): ?>
                    <div class="msg-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($sesi && !$sudah_absen): ?>
                    <div class="card-header">
                        <h3>Sesi Absensi: <?= htmlspecialchars($sesi['namaKelas']) ?></h3>
                    </div>
                    <form method="post" action="absen.php">
                        <div class="form-group">
                            <label for="status">Pilih Status Kehadiran Anda</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="hadir" selected>Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </div>
                        <p style="font-size: 0.9rem; color: var(--secondary-color); margin-top: -0.5rem; margin-bottom: 1.5rem;">
                            Sesi ini akan ditutup pada: <?= htmlspecialchars(date('d M Y, H:i', strtotime($sesi['waktuTutup']))) ?>
                        </p>
                        <div class="form-actions">
                            <a href="dashboard.php" class="btn btn-outline">Kembali</a>
                            <button type="submit" name="lakukanAbsen" class="btn btn-primary">Kirim Absensi</button>
                        </div>
                    </form>
                <?php elseif ($sesi && $sudah_absen): ?>
                    <div class="info-box" style="background-color: var(--success-bg); color: var(--success-text);">
                        <h4 style="color: var(--success-text);">Absensi Sudah Direkam</h4>
                        <p>Anda telah berhasil melakukan absensi untuk sesi kelas <strong><?= htmlspecialchars($sesi['namaKelas']) ?></strong>.</p>
                        <br>
                        <a href="dashboard.php" class="btn btn-primary" style="background-color: var(--success-text);">Kembali ke Dashboard</a>
                    </div>
                <?php else: ?>
                    <div class="info-box">
                        <h4>Tidak Ada Sesi Absensi</h4>
                        <p>Saat ini tidak ada sesi absensi yang terbuka untuk kelas Anda. Silakan hubungi dosen Anda atau coba lagi nanti.</p>
                        <br>
                        <a href="dashboard.php" class="btn btn-outline">Kembali ke Dashboard</a>
                    </div>
                <?php endif; ?>
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
