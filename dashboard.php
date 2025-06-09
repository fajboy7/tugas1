<?php
// File: dashboard.php (Versi Responsif)

// Memanggil file konfigurasi dan fungsi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "config.php";
require_once "functions.php";

// Redirect jika pengguna belum login
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$user = getUser();
$userId = $user['id'];

// Inisialisasi variabel
$riwayat_absensi = [];
$rekap_sesi = [];
$db_error = null;
$page_title = 'Dashboard';

try {
    // Ambil data sesuai peran (role) pengguna
    if (isSiswa()) {
        // --- Query untuk Siswa: 5 riwayat absensi terakhir ---
        $stmt_absensi = $conn->prepare(
            "SELECT a.waktuAbsen, a.status, k.namaKelas
             FROM absensis a 
             JOIN sesiabsensis s ON a.sesiId = s.id
             JOIN kelas k ON s.kelasId = k.id
             WHERE a.userId = ? 
             ORDER BY a.waktuAbsen DESC 
             LIMIT 5"
        );
        $stmt_absensi->bind_param("i", $userId);
        $stmt_absensi->execute();
        $riwayat_absensi = $stmt_absensi->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_absensi->close();

    } elseif (isDosen()) {
        // --- Query untuk Dosen: 5 sesi absensi terakhir yang dibuat ---
        $stmt_sesi = $conn->prepare(
            "SELECT k.id as kelasId, s.status, s.waktuBuka, k.namaKelas, 
                    (SELECT COUNT(*) FROM absensis WHERE sesiId = s.id) AS jumlahPeserta
             FROM sesiabsensis s 
             JOIN kelas k ON s.kelasId = k.id 
             WHERE k.dosenId = ? 
             ORDER BY s.waktuBuka DESC 
             LIMIT 5"
        );
        $stmt_sesi->bind_param("i", $userId);
        $stmt_sesi->execute();
        $rekap_sesi = $stmt_sesi->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_sesi->close();
    }
} catch (mysqli_sql_exception $e) {
    error_log("Database error on main dashboard: " . $e->getMessage());
    $db_error = "Gagal memuat data dari database. Pesan Error: " . htmlspecialchars($e->getMessage());
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
            --error-bg: #FEE2E2; --error-text: #991B1B; --radius: 8px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); color: var(--text-primary); line-height: 1.5; }
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
        .card { background: var(--bg-white); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow-sm); }
        .card-header { display: flex; flex-direction: column; gap: 1rem; align-items: flex-start; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1.5rem; }
        .card-header h3 { font-size: 1.25rem; }
        .card-body { padding-top: 0.5rem; overflow-x: auto; }
        .msg-error { padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; background: var(--error-bg); color: var(--error-text); }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border-color); white-space: nowrap; }
        .data-table th { font-weight: 600; color: var(--secondary-color); font-size: 0.875rem; text-transform: uppercase; }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 999px; font-weight: 600; font-size: 0.8rem; display: inline-block; }
        .status-hadir { background-color: #d1fae5; color: #065f46; } .status-izin { background-color: #fef3c7; color: #92400e; }
        .status-sakit { background-color: #dbeafe; color: #1e40af; } .status-alpa { background-color: #fee2e2; color: #991b1b; }
        .status-terbuka { background-color: #d1fae5; color: #065f46; } .status-ditutup { background-color: #f3f4f6; color: #4b5563; }
        .empty-state { text-align: center; padding: 2rem; color: var(--secondary-color); }
        .empty-state h4 { color: var(--text-primary); font-size: 1.1rem; margin-bottom: 0.25rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-weight: 600; padding: 0.75rem 1.25rem; border-radius: var(--radius); border: 1px solid transparent; cursor: pointer; transition: all 0.2s ease; font-size: 0.9rem; }
        .btn-primary { background-color: var(--primary-color); color: var(--text-light); }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-outline { background-color: transparent; color: var(--secondary-color); border-color: var(--border-color); }
        .btn-outline:hover { background-color: var(--bg-light); color: var(--text-primary); }
        .btn.w-full { width: 100%; }
        .menu-toggle { display: none; background: none; border: none; cursor: pointer; padding: 0.5rem; }
        
        @media (min-width: 768px) {
            .user-profile span { display: inline; }
            .card-header { flex-direction: row; align-items: center; }
        }
        @media (max-width: 767px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1.5rem; }
            .main-header { flex-wrap: wrap; gap: 1rem; }
            .main-header h1 { font-size: 1.5rem; }
            .menu-toggle { display: block; }
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
                <a href="dashboard.php" class="active">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span>Dashboard</span>
                </a>
                <?php if (isDosen()): ?>
                <a href="dashboard_dosen.php">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    <span>Manajemen Kelas</span>
                </a>
                <?php elseif (isSiswa()): ?>
                <a href="dashboard_siswa.php">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    <span>Kelas Saya</span>
                </a>
                <?php endif; ?>
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
                    <h1>Selamat Datang, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>!</h1>
                </div>
                <div class="user-profile">
                    <span><?= htmlspecialchars($user['name'] ?? 'User') ?></span>
                    <div class="avatar"><?= htmlspecialchars(strtoupper(substr($user['name'] ?? 'U', 0, 1))) ?></div>
                </div>
            </header>

            <?php if ($db_error): ?>
                <div class="msg-error"><?= htmlspecialchars($db_error) ?></div>
            <?php endif; ?>

            <?php if (isSiswa()): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Riwayat Absensi Terakhir</h3>
                    <a href="absen.php" class="btn btn-primary">Absen Sekarang</a>
                </div>
                <div class="card-body">
                    <?php if (empty($riwayat_absensi) && !$db_error): ?>
                        <div class="empty-state">
                            <h4>Belum Ada Riwayat</h4>
                            <p>Anda belum pernah melakukan absensi.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr><th>Waktu Absen</th><th>Kelas</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($riwayat_absensi as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($r['waktuAbsen']))) ?></td>
                                    <td><?= htmlspecialchars($r['namaKelas']) ?></td>
                                    <td><span class="status-badge status-<?= strtolower(htmlspecialchars($r['status'])) ?>"><?= ucfirst(htmlspecialchars($r['status'])) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isDosen()): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Rekap Sesi Terakhir</h3>
                    <a href="sesi.php" class="btn btn-primary">Buat Sesi</a>
                </div>
                <div class="card-body">
                    <?php if (empty($rekap_sesi) && !$db_error): ?>
                         <div class="empty-state">
                            <h4>Belum Ada Sesi</h4>
                            <p>Anda belum pernah membuat sesi absensi.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr><th>Kelas</th><th>Waktu Buka</th><th>Status</th><th>Peserta</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rekap_sesi as $r): ?>
                                <tr>
                                    <td><a href="kelas_detail.php?id=<?= $r['kelasId'] ?>"><?= htmlspecialchars($r['namaKelas']) ?></a></td>
                                    <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($r['waktuBuka']))) ?></td>
                                    <td><span class="status-badge status-<?= strtolower(htmlspecialchars($r['status'])) ?>"><?= ucfirst(htmlspecialchars($r['status'])) ?></span></td>
                                    <td><?= htmlspecialchars($r['jumlahPeserta']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
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
