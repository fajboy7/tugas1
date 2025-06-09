<?php
// File: kelas_detail.php
require_once "config.php";
require_once "functions.php";

date_default_timezone_set('Asia/Jakarta');
setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID');

if (!isDosen()) {
    header("Location: index.php");
    exit;
}

$user = getUser();
$userId = $user['id'];
$kelasId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = null;

if (!$kelasId) {
    die("Error: ID kelas tidak valid.");
}

// --- PENGAMBILAN DATA AWAL (KELAS & SISWA) ---
try {
    $stmt_kelas = $conn->prepare("SELECT namaKelas, kodeKelas FROM kelas WHERE id = ? AND dosenId = ?");
    $stmt_kelas->bind_param("ii", $kelasId, $userId);
    $stmt_kelas->execute();
    $kelas = $stmt_kelas->get_result()->fetch_assoc();
    $stmt_kelas->close();

    if (!$kelas) {
        die("Kelas tidak ditemukan atau Anda tidak memiliki hak akses.");
    }

    $stmt_siswa = $conn->prepare("SELECT u.id, u.name FROM kelassiswas ks JOIN users u ON ks.userId = u.id WHERE ks.kelasId = ? ORDER BY u.name ASC");
    $stmt_siswa->bind_param("i", $kelasId);
    $stmt_siswa->execute();
    $daftarSiswa = $stmt_siswa->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_siswa->close();

} catch (mysqli_sql_exception $e) {
    error_log("Gagal mengambil data kelas/siswa: " . $e->getMessage());
    die("Terjadi kesalahan saat memuat data dasar kelas.");
}

$page_title = 'Detail: ' . htmlspecialchars($kelas['namaKelas']);

// --- LOGIKA PENANGGALAN ---
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$timestamp = strtotime($tanggal);
$mingguAwal = date('Y-m-d', strtotime('monday this week', $timestamp));
$mingguAkhir = date('Y-m-d', strtotime('sunday this week', $timestamp));

// --- Logika untuk navigasi minggu ---
$tglSebelumnya = date('Y-m-d', strtotime("$mingguAwal -1 week"));
$tglBerikutnya = date('Y-m-d', strtotime("$mingguAwal +1 week"));

// --- PENGAMBILAN DATA SESI & ABSENSI ---
$allSesiMingguan = [];
$sesiHariIni = null;
$laporan = [];
$stats = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'tanpa keterangan' => 0]; // Diperbarui sesuai enum

try {
    // PERBAIKAN 1: Menggunakan tabel sesi yang benar
    // Asumsi ada tabel 'sesi' untuk menyimpan sesi absensi
    $stmt_sesi_mingguan = $conn->prepare("SELECT DATE(waktuBuka) as tanggal_sesi FROM sesi WHERE kelasId = ? AND waktuBuka BETWEEN ? AND ?");
    $mingguAwal_datetime = $mingguAwal . ' 00:00:00';
    $mingguAkhir_datetime = $mingguAkhir . ' 23:59:59';
    $stmt_sesi_mingguan->bind_param("iss", $kelasId, $mingguAwal_datetime, $mingguAkhir_datetime);
    $stmt_sesi_mingguan->execute();
    $resultSesiMingguan = $stmt_sesi_mingguan->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($resultSesiMingguan as $s) {
        $allSesiMingguan[] = $s['tanggal_sesi'];
    }
    $stmt_sesi_mingguan->close();

    // PERBAIKAN 2: Mengambil sesi dari tabel 'sesi'
    $stmt_sesi_hari_ini = $conn->prepare("SELECT id, status, waktuTutup FROM sesi WHERE kelasId = ? AND DATE(waktuBuka) = ? ORDER BY waktuBuka DESC LIMIT 1");
    $stmt_sesi_hari_ini->bind_param("is", $kelasId, $tanggal);
    $stmt_sesi_hari_ini->execute();
    $sesiHariIni = $stmt_sesi_hari_ini->get_result()->fetch_assoc();
    $stmt_sesi_hari_ini->close();

    if ($sesiHariIni) {
          // PERBAIKAN 3: Menggunakan tabel 'absensi' sesuai struktur
        $stmt_absensis = $conn->prepare("SELECT userId, status FROM absensis WHERE sesiId = ?");
        $stmt_absensis->bind_param("i", $sesiHariIni['id']);
        $stmt_absensis->execute();
        $resultAbsensis = $stmt_absensis->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $absensiSiswa = [];
        foreach ($resultAbsensi as $absen) {
            $absensiSiswa[$absen['userId']] = $absen['status'];
            if (isset($stats[$absen['status']])) {
                $stats[$absen['status']]++;
            }
        }
        
        foreach ($daftarSiswa as $siswa) {
            $status = $absensiSiswa[$siswa['id']] ?? 'tanpa keterangan';
            $laporan[] = ['id' => $siswa['id'], 'name' => $siswa['name'], 'status' => $status];
        }
        $stats['tanpa keterangan'] = count($daftarSiswa) - ($stats['hadir'] + $stats['izin'] + $stats['sakit']);

    } else {
        foreach ($daftarSiswa as $siswa) {
            $laporan[] = ['id' => $siswa['id'], 'name' => $siswa['name'], 'status' => 'tanpa keterangan'];
        }
        $stats['tanpa keterangan'] = count($daftarSiswa);
    }

} catch (mysqli_sql_exception $e) {
    error_log("Gagal mengambil data sesi/absensi: " . $e->getMessage());
    $error = "Gagal memuat data absensi. Silakan coba lagi.";
}


// --- PENANGANAN FORM (POST REQUEST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Buka Sesi Baru
    if (isset($_POST['buatSesi'])) {
        $waktuBuka = $_POST['waktuBuka'];
        $waktuTutup = $_POST['waktuTutup'];

        // Validasi waktu
        if (strtotime($waktuBuka) >= strtotime($waktuTutup)) {
            $error = "Waktu tutup harus setelah waktu buka.";
        } else {
            // PERBAIKAN 4: Simpan sesi ke tabel 'sesi'
            $stmt_check = $conn->prepare("SELECT id FROM sesi WHERE kelasId = ? AND DATE(waktuBuka) = ?");
            $tanggalBuka = date('Y-m-d', strtotime($waktuBuka));
            $stmt_check->bind_param("is", $kelasId, $tanggalBuka);
            $stmt_check->execute();
            $existing_session = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if ($existing_session) {
                $error = "Sesi untuk tanggal " . htmlspecialchars($tanggalBuka) . " sudah ada.";
            } else {
                $stmt_buka = $conn->prepare("INSERT INTO sesi (status, createdAt, updatedAt, kelasId, waktuBuka, waktuTutup) VALUES ('dibuka', NOW(), NOW(), ?, ?, ?)");
                $stmt_buka->bind_param("iss", $kelasId, $waktuBuka, $waktuTutup);
                $stmt_buka->execute();
                $stmt_buka->close();
                header("Location: kelas_detail.php?id=$kelasId&tanggal=" . date('Y-m-d', strtotime($waktuBuka)) . "&success=Sesi baru berhasil dibuka!");
                exit;
            }
        }
    }
    
    // Handle Tutup Sesi
    if (isset($_POST['tutupSesi']) && $sesiHariIni && $sesiHariIni['status'] == 'dibuka') {
        $stmt_tutup = $conn->prepare("UPDATE sesi SET status='ditutup', waktuTutup=NOW(), updatedAt=NOW() WHERE id = ?");
        $stmt_tutup->bind_param("i", $sesiHariIni['id']);
        $stmt_tutup->execute();
        $stmt_tutup->close();
        header("Location: kelas_detail.php?id=$kelasId&tanggal=$tanggal&success=Sesi berhasil ditutup!");
        exit;
    }
}

// Helper untuk memformat tanggal ke Bahasa Indonesia
function format_tanggal_indonesia($timestamp) {
    $bulan = array (
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $split = explode('-', date('d-n-Y', $timestamp));
    return $split[0] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[2];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        /* CSS Tetap Sama */
        :root {
            --primary-color: #4F46E5; --primary-hover: #4338CA; --secondary-color: #6B7280;
            --text-primary: #111827; --text-light: #FFFFFF; --bg-light: #F9FAFB; --bg-white: #FFFFFF;
            --border-color: #E5E7EB; --success-bg: #dcfce7; --success-text: #166534;
            --error-bg: #FEE2E2; --error-text: #991B1B; --radius: 8px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --hadir-bg: #D1FAE5; --hadir-text: #065F46; --izin-bg: #FEF3C7; --izin-text: #92400E;
            --sakit-bg: #DBEAFE; --sakit-text: #1E40AF; --alpa-bg: #FEE2E2; --alpa-text: #991B1B;
            --info-bg: #E0E7FF; --info-text: #3730A3;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); color: var(--text-primary); }
        a { color: var(--primary-color); text-decoration: none; }
        .icon { width: 20px; height: 20px; }
        
        .app-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: var(--bg-white); border-right: 1px solid var(--border-color); padding: 1.5rem; display: flex; flex-direction: column; flex-shrink: 0; }
        .main-content { flex-grow: 1; padding: 2rem; overflow-x: hidden; }
        
        .sidebar-header { font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; padding-bottom: 1.5rem; }
        .sidebar-nav { flex-grow: 1; display: flex; flex-direction: column; gap: 0.5rem; }
        .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius); font-weight: 500; color: var(--secondary-color); transition: all 0.2s ease; }
        .sidebar-nav a:hover { background-color: var(--bg-light); color: var(--text-primary); }
        .sidebar-nav a.active { background-color: var(--primary-color); color: var(--text-light); }
        .sidebar-footer { margin-top: auto; }
        
        .mobile-menu-btn, .sidebar-overlay { display: none; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-weight: 600; padding: 0.75rem 1.25rem; border-radius: var(--radius); border: 1px solid transparent; cursor: pointer; transition: all 0.2s ease; font-size: 0.9rem; }
        .btn-primary { background-color: var(--primary-color); color: var(--text-light); }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-danger { background-color: #ef4444; color: var(--text-light); }
        .btn-danger:hover { background-color: #dc2626; }
        .btn-outline { background-color: transparent; color: var(--secondary-color); border-color: var(--border-color); }
        .btn-outline:hover { background-color: var(--bg-light); color: var(--text-primary); }
        .btn-disabled { background-color: #D1D5DB; color: #6B7280; cursor: not-allowed; }
        .w-full { width: 100%; }
        
        .card { background: var(--bg-white); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow-sm); }
        .msg-success, .msg-error, .msg-info { padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-weight: 500;}
        .msg-success { background: var(--success-bg); color: var(--success-text); }
        .msg-error { background: var(--error-bg); color: var(--error-text); }
        .msg-info { background: var(--info-bg); color: var(--info-text); }
        
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .page-header h1 { font-size: 1.75rem; margin: 0; line-height: 1.2; }
        .kode-kelas { font-family: monospace; background: #eee; padding: 4px 10px; border-radius: 6px; font-weight: 600; }

        .calendar-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0 0.25rem; }
        .calendar-nav-title { font-weight: 600; color: var(--text-primary); }
        .calendar-nav-arrow { display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius); width: 36px; height: 36px; transition: background-color 0.2s ease; }
        .calendar-nav-arrow:hover { background-color: var(--bg-light); }
        .calendar-nav-arrow .icon { color: var(--secondary-color); }

        .calendar-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; padding-bottom: 0.5rem; }
        .calendar-bar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.75rem; margin-bottom: 1rem; min-width: 500px; }
        .calendar-day { display: block; text-align: center; padding: 0.75rem 0.5rem; border-radius: var(--radius); transition: all 0.2s ease; background: #f3f4f6; }
        .calendar-day:hover { background: #e5e7eb; }
        .calendar-day.selected { background: var(--primary-color); color: var(--text-light); font-weight: 600; }
        .calendar-day.has-session { position: relative; }
        .calendar-day.has-session::after { content: ''; display: block; position: absolute; bottom: 6px; left: 50%; transform: translateX(-50%); width: 6px; height: 6px; border-radius: 50%; background: #60a5fa; }
        .calendar-day.selected.has-session::after { background: var(--text-light); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--bg-light); padding: 1rem; border-radius: var(--radius); }
        .stat-card-title { font-weight: 500; color: var(--secondary-color); font-size: 0.9rem; }
        .stat-card-value { font-size: 2rem; font-weight: 700; }

        .laporan-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem; }
        .laporan-header h2 { font-size: 1.25rem; }
        .laporan-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-radius: var(--radius); background: var(--bg-light); margin-bottom: 0.5rem; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 999px; font-weight: 600; font-size: 0.8rem; text-transform: capitalize; }
        .status-hadir { background: var(--hadir-bg); color: var(--hadir-text); }
        .status-izin { background: var(--izin-bg); color: var(--izin-text); }
        .status-sakit { background: var(--sakit-bg); color: var(--sakit-text); }
        .status-tanpa-keterangan { background: var(--alpa-bg); color: var(--alpa-text); }
        
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(17, 24, 39, 0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 1010; opacity: 0; transition: opacity 0.3s ease; }
        .modal.active { display: flex; opacity: 1; }
        .modal-content { background: var(--bg-white); padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow-md); max-width: 450px; width: 95%; transform: scale(0.95); transition: transform 0.3s ease; }
        .modal.active .modal-content { transform: scale(1); }
        .modal-header { padding-bottom: 1rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); }
        .modal-header h3 { font-size: 1.25rem; }
        .modal-footer { margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-control { width: 100%; padding: 0.75rem; border-radius: var(--radius); border: 1px solid var(--border-color); font-size: 1rem; }

        @media (max-width: 768px) {
            .sidebar { position: fixed; left: 0; top: 0; height: 100%; z-index: 100; transform: translateX(-100%); transition: transform 0.3s ease-in-out; box-shadow: var(--shadow-md); }
            .sidebar.active { transform: translateX(0); }
            .main-content { padding: 1rem; }
            .mobile-menu-btn { display: block; background: none; border: none; cursor: pointer; padding: 0.5rem; margin-right: 0.5rem; }
            .mobile-menu-btn .icon { width: 24px; height: 24px; }
            .sidebar-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99; }
            .sidebar-overlay.active { display: block; }
            .page-header { align-items: center; }
            .page-header > div:first-of-type { flex-grow: 1; }
            .page-header .btn, .page-header form .btn { width: 100%; margin-top: 1rem; }
            .page-header h1 { font-size: 1.5rem; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .laporan-item span:first-child { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
        }
    </style>
</head>
<body>
<div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="app-layout">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
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
        <div class="page-header">
            <button class="mobile-menu-btn" onclick="toggleSidebar()">
                 <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>
            <div>
                <h1><?= htmlspecialchars($kelas['namaKelas']) ?></h1>
                <p>Kode Kelas: <span class="kode-kelas"><?= htmlspecialchars($kelas['kodeKelas']) ?></span></p>
            </div>

            <?php if (!$sesiHariIni): ?>
                <button onclick="openModal('modal-buka')" class="btn btn-primary">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    <span>Buka Sesi Baru</span>
                </button>
            <?php elseif ($sesiHariIni['status'] == 'dibuka'): ?>
                <form method="post" onsubmit="return confirm('Anda yakin ingin menutup sesi ini?');">
                    <button name="tutupSesi" class="btn btn-danger">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><rect x="9" y="9" width="6" height="6"></rect></svg>
                        <span>Tutup Sesi Sekarang</span>
                    </button>
                </form>
            <?php else: ?>
                 <span class="btn btn-disabled">Sesi Telah Ditutup</span>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="msg-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="msg-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($sesiHariIni && $sesiHariIni['status'] == 'ditutup'): ?>
            <div class="msg-info">Sesi absensi untuk hari ini telah ditutup.</div>
        <?php endif; ?>

        <div class="card">
            
            <div class="calendar-nav">
                <a href="?id=<?= $kelasId ?>&tanggal=<?= $tglSebelumnya ?>" class="calendar-nav-arrow" title="Minggu Sebelumnya">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </a>
                <span class="calendar-nav-title"><?= format_tanggal_indonesia($timestamp) ?></span>
                <a href="?id=<?= $kelasId ?>&tanggal=<?= $tglBerikutnya ?>" class="calendar-nav-arrow" title="Minggu Berikutnya">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>

            <div class="calendar-wrapper">
                <div class="calendar-bar">
                    <?php
                    $hari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                    for ($i = 0; $i < 7; $i++) {
                        $tgl_loop = date('Y-m-d', strtotime("$mingguAwal +$i day"));
                        $isSelected = ($tgl_loop == $tanggal) ? 'selected' : '';
                        $hasSession = in_array($tgl_loop, $allSesiMingguan) ? 'has-session' : '';
                        echo "<a class='calendar-day {$isSelected} {$hasSession}' href='?id={$kelasId}&tanggal={$tgl_loop}'>";
                        echo "<div>{$hari[$i]}</div>";
                        echo "<div>" . date('d', strtotime($tgl_loop)) . "</div>";
                        echo "</a>";
                    }
                    ?>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card"><div class="stat-card-title">Hadir</div><div class="stat-card-value" style="color:var(--hadir-text);"><?= $stats['hadir'] ?></div></div>
                <div class="stat-card"><div class="stat-card-title">Izin</div><div class="stat-card-value" style="color:var(--izin-text);"><?= $stats['izin'] ?></div></div>
                <div class="stat-card"><div class="stat-card-title">Sakit</div><div class="stat-card-value" style="color:var(--sakit-text);"><?= $stats['sakit'] ?></div></div>
                <div class="stat-card"><div class="stat-card-title">Tanpa Keterangan</div><div class="stat-card-value" style="color:var(--alpa-text);"><?= $stats['tanpa keterangan'] ?></div></div>
            </div>

            <div class="laporan-list">
                <div class="laporan-header">
                    <h2>Laporan Kehadiran - <?= format_tanggal_indonesia(strtotime($tanggal)) ?></h2>
                    </div>

                <?php if (empty($laporan)): ?>
                    <p>Tidak ada siswa di kelas ini.</p>
                <?php else: ?>
                    <?php foreach ($laporan as $item): ?>
                    <div class="laporan-item">
                        <span><?= htmlspecialchars($item['name']) ?></span>
                        <span class="status-badge status-<?= str_replace(' ', '-', $item['status']) ?>"><?= $item['status'] ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<div id="modal-buka" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Buka Sesi Absensi Baru</h3>
        </div>
        <form method="post">
            <div class="form-group">
                <label for="waktuBuka">Waktu Buka</label>
                <input class="form-control" type="datetime-local" name="waktuBuka" id="waktuBuka" value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>
            <div class="form-group">
                <label for="waktuTutup">Waktu Tutup</label>
                <input class="form-control" type="datetime-local" name="waktuTutup" id="waktuTutup" value="<?= date('Y-m-d\TH:i', time() + 3600) ?>" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-buka')">Batal</button>
                <button class="btn btn-primary" name="buatSesi">Buka Sesi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
    }
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }
    
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebar-overlay').classList.toggle('active');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                closeModal(activeModal.id);
            }
            const activeSidebar = document.querySelector('.sidebar.active');
            if(activeSidebar) {
                toggleSidebar();
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target.id);
        }
    });
</script>
</body>
</html>