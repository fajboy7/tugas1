<?php
// File: kelas_siswa_detail.php (Versi Responsif)

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

$user = getUser();
$userId = $user['id'];
$kelasId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = null;
$success_msg = null;

if (!$kelasId) {
    die("Error: ID kelas tidak valid.");
}

// --- PENGAMBILAN DATA & VALIDASI ---
try {
    $stmt_kelas = $conn->prepare("SELECT namaKelas FROM kelas WHERE id = ?");
    $stmt_kelas->bind_param("i", $kelasId);
    $stmt_kelas->execute();
    $kelas = $stmt_kelas->get_result()->fetch_assoc();
    $stmt_kelas->close();

    if (!$kelas) {
        die("Kelas dengan ID #{$kelasId} tidak ditemukan.");
    }

    $stmt_enroll = $conn->prepare("SELECT userId FROM kelassiswas WHERE kelasId = ? AND userId = ?");
    $stmt_enroll->bind_param("ii", $kelasId, $userId);
    $stmt_enroll->execute();
    if ($stmt_enroll->get_result()->num_rows === 0) {
        die("Anda tidak terdaftar di kelas ini.");
    }
    $stmt_enroll->close();

} catch (mysqli_sql_exception $e) {
    error_log("SQL Error di kelas_siswa_detail.php (Validasi): " . $e->getMessage());
    die("Terjadi kesalahan pada database saat validasi data. Pesan error: " . htmlspecialchars($e->getMessage()));
}

$page_title = 'Detail Kelas: ' . htmlspecialchars($kelas['namaKelas']);

// --- LOGIKA PENANGGALAN & NAVIGASI MINGGUAN ---
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
if (isset($_GET['week'])) {
    $modifier = ($_GET['week'] == 'prev') ? '-7 days' : '+7 days';
    $new_tanggal = date('Y-m-d', strtotime("$tanggal $modifier"));
    header("Location: kelas_siswa_detail.php?id=$kelasId&tanggal=$new_tanggal");
    exit;
}
$timestamp = strtotime($tanggal);
$mingguAwal = date('Y-m-d', strtotime('monday this week', $timestamp));
$mingguAkhir = date('Y-m-d', strtotime('sunday this week', $timestamp));

// --- PENGAMBILAN DATA RIWAYAT ABSENSI MINGGUAN ---
$riwayatMingguan = [];
try {
    $query = "SELECT s.id as sesiId, DATE(s.waktuBuka) as tanggal, s.waktuBuka, s.waktuTutup, s.status as statusSesi, a.status as statusAbsen
              FROM sesiabsensis s
              LEFT JOIN absensis a ON a.sesiId = s.id AND a.userId = ?
              WHERE s.kelasId = ? AND DATE(s.waktuBuka) BETWEEN ? AND ?
              ORDER BY s.waktuBuka ASC";
    $stmt_riwayat = $conn->prepare($query);
    $stmt_riwayat->bind_param("iiss", $userId, $kelasId, $mingguAwal, $mingguAkhir);
    $stmt_riwayat->execute();
    $resultRiwayat = $stmt_riwayat->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($resultRiwayat as $r) {
        $riwayatMingguan[$r['tanggal']] = $r;
    }
    $stmt_riwayat->close();
} catch (mysqli_sql_exception $e) {
    error_log("Gagal mengambil riwayat mingguan: " . $e->getMessage());
    $error = "Gagal memuat riwayat absensi.";
}

$sesiHariIni = $riwayatMingguan[$tanggal] ?? null;

// --- PENANGANAN FORM ABSENSI (POST) ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['doAbsensi'])) {
    $status = $_POST['status'];
    if ($sesiHariIni && $sesiHariIni['statusSesi'] == 'terbuka' && !$sesiHariIni['statusAbsen']) {
        $now = date('Y-m-d H:i:s');
        if (strtotime($now) >= strtotime($sesiHariIni['waktuBuka']) && strtotime($now) <= strtotime($sesiHariIni['waktuTutup'])) {
            $stmt_insert = $conn->prepare("INSERT INTO absensis (waktuAbsen, status, createdAt, updatedAt, sesiId, userId) VALUES (NOW(), ?, NOW(), NOW(), ?, ?)");
            $stmt_insert->bind_param("sii", $status, $sesiHariIni['sesiId'], $userId);
            if ($stmt_insert->execute()) {
                $_SESSION['success_message'] = "Absensi berhasil direkam!";
            } else {
                $_SESSION['error_message'] = "Gagal merekam absensi.";
            }
            $stmt_insert->close();
            header("Location: kelas_siswa_detail.php?id=$kelasId&tanggal=$tanggal");
            exit;
        } else {
            $error = "Sesi absensi sudah berakhir atau belum dimulai.";
        }
    } else {
        $error = "Tidak bisa melakukan absensi saat ini. Sesi mungkin tertutup atau Anda sudah absen.";
    }
}
// Menangkap pesan dari session
if(isset($_SESSION['success_message'])){
    $success_msg = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if(isset($_SESSION['error_message'])){
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
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
        :root {
            --primary-color: #4F46E5; --primary-hover: #4338CA; --secondary-color: #6B7280;
            --text-primary: #111827; --text-light: #FFFFFF; --bg-light: #F9FAFB; --bg-white: #FFFFFF;
            --border-color: #E5E7EB; --success-bg: #dcfce7; --success-text: #166534;
            --error-bg: #FEE2E2; --error-text: #991B1B; --radius: 8px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --hadir-bg: #D1FAE5; --hadir-text: #065F46; --izin-bg: #FEF3C7; --izin-text: #92400E;
            --sakit-bg: #DBEAFE; --sakit-text: #1E40AF; --alpa-bg: #FEE2E2; --alpa-text: #991B1B;
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
        .main-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .main-header h1 { font-size: 1.75rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-profile { display: flex; align-items: center; gap: 0.75rem; }
        .user-profile span { font-weight: 600; display: none; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background-color: var(--primary-color); color: var(--text-light); display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-weight: 600; padding: 0.75rem 1.25rem; border-radius: var(--radius); border: 1px solid transparent; cursor: pointer; transition: all 0.2s ease; font-size: 0.9rem; }
        .btn-primary { background-color: var(--primary-color); color: var(--text-light); }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-outline { background-color: transparent; color: var(--secondary-color); border-color: var(--border-color); }
        .btn-outline:hover { background-color: var(--bg-light); color: var(--text-primary); }
        .w-full { width: 100%; }
        .card { background: var(--bg-white); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow-sm); }
        .msg-success, .msg-error { padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-weight: 500;}
        .msg-success { background: var(--success-bg); color: var(--success-text); }
        .msg-error { background: var(--error-bg); color: var(--error-text); }
        
        /* Kalender Navigasi */
        .calendar-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .calendar-nav span { font-size: 1rem; font-weight: 600; text-align: center; margin: 0 0.5rem; }
        .calendar-nav .btn-outline { padding: 0.5rem 0.75rem; }
        
        /* Kalender Bar */
        .calendar-bar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.5rem; }
        .calendar-day { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 0.75rem 0.25rem; border-radius: var(--radius); transition: all 0.2s ease; border: 2px solid transparent; aspect-ratio: 1 / 1; cursor: pointer;}
        .calendar-day.no-session { background: #f3f4f6; color: #9ca3af; }
        .calendar-day.no-session:hover { background: #e5e7eb; }
        .calendar-day.selected { border-color: var(--primary-color); font-weight: 600; box-shadow: 0 0 0 2px var(--primary-color); }
        .calendar-day-name { font-size: 0.8rem; text-transform: uppercase; }
        .calendar-day-date { font-size: 1.1rem; font-weight: 600; margin-top: 0.25rem; }
        
        /* Status Absensi */
        .status-hadir { background: var(--hadir-bg); color: var(--hadir-text); }
        .status-izin { background: var(--izin-bg); color: var(--izin-text); }
        .status-sakit { background: var(--sakit-bg); color: var(--sakit-text); }
        .status-alpa { background: var(--alpa-bg); color: var(--alpa-text); }
        
        /* Detail Card */
        .detail-card { text-align: center; padding: 2rem; }
        .detail-card h2 { margin-bottom: 0.5rem; font-size: 1.5rem; }
        .detail-card .status-display { font-size: 1.25rem; font-weight: 600; margin-top: 0.5rem; }
        .detail-card .btn-absen { margin-top: 1.5rem; }

        /* Modal */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(17, 24, 39, 0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 3000; opacity: 0; transition: opacity 0.3s ease; }
        .modal.active { display: flex; opacity: 1; }
        .modal-content { background: var(--bg-white); padding: 1.5rem; border-radius: var(--radius); max-width: 400px; width: 95%; text-align: center; transform: scale(0.95); transition: transform 0.3s ease; }
        .modal.active .modal-content { transform: scale(1); }
        .modal-footer { margin-top: 1.5rem; display: flex; flex-direction: column-reverse; gap: 0.75rem; }
        .form-group { margin: 1.5rem 0; text-align: left; }
        .radio-group label { display: flex; align-items: center; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius); margin-bottom: 0.5rem; cursor: pointer; }
        .radio-group input { margin-right: 0.75rem; accent-color: var(--primary-color); }
        
        /* Toggle & Overlay untuk Mobile */
        .menu-toggle { display: none; background: none; border: none; cursor: pointer; padding: 0.5rem; z-index: 2100; }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1999; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .overlay.active { opacity: 1; visibility: visible; }

        @media (min-width: 768px) {
            .user-profile span { display: inline; }
            .modal-footer { flex-direction: row; }
            .modal-footer .btn { width: auto; }
        }

        @media (max-width: 767px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1.5rem 1rem; }
            .main-header h1 { font-size: 1.25rem; }
            .menu-toggle { display: block; }
            .card { padding: 1rem; }
            
            /* Perbaikan Kalender Navigasi di Mobile */
            .calendar-nav span { font-size: 0.9rem; white-space: nowrap; }
            .calendar-nav .btn-outline { padding: 0.5rem; }
            
            /* Perbaikan Kalender Bar di Mobile */
            .calendar-bar { gap: 0.25rem; }
            .calendar-day { aspect-ratio: unset; min-height: 60px; padding: 0.5rem 0.1rem; }
            .calendar-day-name { font-size: 0.7rem; }
            .calendar-day-date { font-size: 1rem; }

            /* Perbaikan Modal di Mobile */
            .modal-footer .btn { width: 100%; }
            .detail-card h2 { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
<div class="app-layout">
    <div class="overlay" id="overlay"></div>
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
            <a href="dashboard_siswa.php" class="active">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                <span>Kelas Saya</span>
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
                <h1><?= htmlspecialchars($kelas['namaKelas']) ?></h1>
            </div>
             <div class="user-profile">
                <span><?= htmlspecialchars($user['name'] ?? 'Siswa') ?></span>
                <div class="avatar"><?= htmlspecialchars(strtoupper(substr($user['name'] ?? 'S', 0, 1))) ?></div>
            </div>
        </header>

        <?php if ($success_msg): ?>
            <div class="msg-success"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="msg-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="calendar-nav">
                <a href="?id=<?= $kelasId ?>&week=prev&tanggal=<?= $tanggal ?>" class="btn btn-outline">&larr;</a>
                <span><?= date('d M', strtotime($mingguAwal)) ?> - <?= date('d M Y', strtotime($mingguAkhir)) ?></span>
                <a href="?id=<?= $kelasId ?>&week=next&tanggal=<?= $tanggal ?>" class="btn btn-outline">&rarr;</a>
            </div>
            <div class="calendar-bar">
                <?php
                $namaHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                for ($i = 0; $i < 7; $i++) {
                    $tgl_loop = date('Y-m-d', strtotime("$mingguAwal +$i day"));
                    $dataSesiLoop = $riwayatMingguan[$tgl_loop] ?? null;
                    $class = 'no-session';
                    if ($dataSesiLoop) {
                         // Default 'alpa' jika sesi ada tapi siswa belum absen dan sesi sudah lewat
                        $status_absen = 'alpa'; 
                        if (isset($dataSesiLoop['statusAbsen'])) {
                            $status_absen = $dataSesiLoop['statusAbsen'];
                        }
                        $class = 'status-' . strtolower($status_absen);
                    }
                    $isSelected = ($tgl_loop == $tanggal) ? 'selected' : '';
                    echo "<a class='calendar-day {$class} {$isSelected}' href='?id={$kelasId}&tanggal={$tgl_loop}'>";
                    echo "<div class='calendar-day-name'>{$namaHari[$i]}</div>";
                    echo "<div class='calendar-day-date'>" . date('d', strtotime($tgl_loop)) . "</div>";
                    echo "</a>";
                }
                ?>
            </div>
        </div>
        
        <div class="card detail-card" style="margin-top: 1.5rem;">
            <h2>Absensi: <?= date('l, d F Y', $timestamp) ?></h2>
            <?php
            $now = new DateTime("now", new DateTimeZone('Asia/Jakarta'));
            $sesiBuka = $sesiHariIni ? new DateTime($sesiHariIni['waktuBuka']) : null;
            $sesiTutup = $sesiHariIni ? new DateTime($sesiHariIni['waktuTutup']) : null;
            $sesiAktif = $sesiHariIni && $sesiHariIni['statusSesi'] == 'terbuka' && $now >= $sesiBuka && $now <= $sesiTutup;
            
            if ($sesiHariIni) {
                if ($sesiHariIni['statusAbsen']) {
                    echo "<p class='status-display'>Anda tercatat: <strong style='text-transform: capitalize; color: var(--".strtolower($sesiHariIni['statusAbsen'])."-text);'>" . htmlspecialchars($sesiHariIni['statusAbsen']) . "</strong></p>";
                } elseif ($sesiAktif) {
                    echo "<p>Sesi dibuka hingga " . date('H:i', strtotime($sesiHariIni['waktuTutup'])) . ". Silakan lakukan absensi.</p>";
                    echo "<button onclick=\"openModal('absen-modal')\" class='btn btn-primary btn-absen'>Lakukan Absensi Sekarang</button>";
                } else {
                    $keterangan = $sesiHariIni['statusSesi'] == 'tertutup' ? 'Sesi ditutup oleh dosen' : 'Sesi telah berakhir atau belum dimulai';
                    echo "<p class='status-display'>Anda tercatat: <strong style='color: var(--alpa-text);'>Alpa</strong></p><p style='font-size:0.9rem; margin-top: 0.5rem; color: var(--secondary-color);'>($keterangan)</p>";
                }
            } else {
                echo "<p style='color: var(--secondary-color);'>Tidak ada sesi absensi pada tanggal ini.</p>";
            }
            ?>
        </div>
    </main>
</div>

<div id="absen-modal" class="modal">
    <div class="modal-content">
        <h3>Pilih Status Kehadiran</h3>
        <form method="post" action="?id=<?= $kelasId ?>&tanggal=<?= $tanggal ?>">
            <div class="form-group radio-group">
                <label><input type="radio" name="status" value="hadir" checked> Hadir</label>
                <label><input type="radio" name="status" value="izin"> Izin</label>
                <label><input type="radio" name="status" value="sakit"> Sakit</label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('absen-modal')">Batal</button>
                <button type="submit" name="doAbsensi" class="btn btn-primary">Konfirmasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menu-toggle');
    const overlay = document.getElementById('overlay');
    
    function toggleMenu() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    menuToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMenu();
    });

    overlay.addEventListener('click', () => {
        if (sidebar.classList.contains('active')) {
            toggleMenu();
        }
    });

    function openModal(modalId) {
        document.getElementById(modalId)?.classList.add('active');
    }

    function closeModal(modalId) {
        document.getElementById(modalId)?.classList.remove('active');
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            document.querySelectorAll('.modal.active').forEach(modal => closeModal(modal.id));
            if (sidebar.classList.contains('active')) {
                toggleMenu();
            }
        }
    });
</script>
</body>
</html>
