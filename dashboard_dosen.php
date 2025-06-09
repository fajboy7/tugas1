<?php
// File: dashboard_dosen.php (Versi Responsif)

// Memulai sesi hanya jika belum ada yang aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php";
require_once "functions.php";

// Pastikan hanya pengguna dengan peran 'dosen' yang dapat mengakses
if (!isDosen()) {
    header("Location: index.php");
    exit;
}

$user = getUser();
$error = "";
$success_msg = "";

// Menangani notifikasi dari session
if (isset($_SESSION['success_message'])) {
    $success_msg = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_GET['success'])) { // Menangkap dari redirect sesi.php
    $success_msg = htmlspecialchars($_GET['success']);
}

// --- LOGIKA UNTUK MEMBUAT KELAS ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["buatKelas"])) {
    $namaKelas = trim($_POST["namaKelas"]);

    if (!empty($namaKelas)) {
        try {
            // Loop untuk memastikan kode kelas yang dihasilkan unik
            do {
                $kodeKelas = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
                $stmt_cek = $conn->prepare("SELECT id FROM kelas WHERE kodeKelas = ?");
                $stmt_cek->bind_param("s", $kodeKelas);
                $stmt_cek->execute();
            } while ($stmt_cek->get_result()->num_rows > 0);
            
            $now = date('Y-m-d H:i:s');
            $dosenId = $user['id'];

            // PERBAIKAN: Menggunakan nama kolom 'dosenId' yang benar
            $stmt_insert = $conn->prepare("INSERT INTO kelas (namaKelas, kodeKelas, createdAt, updatedAt, dosenId) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("ssssi", $namaKelas, $kodeKelas, $now, $now, $dosenId);
            
            if ($stmt_insert->execute()) {
                $_SESSION['success_message'] = "Kelas '{$namaKelas}' berhasil dibuat!";
                header("Location: dashboard_dosen.php");
                exit;
            } else {
                $error = "Gagal menyimpan kelas ke database.";
            }
        } catch (mysqli_sql_exception $e) {
            error_log("Database Error: " . $e->getMessage());
            $error = "Terjadi masalah pada server. Pesan: " . $e->getMessage();
        }
    } else {
        $error = "Nama kelas tidak boleh kosong!";
    }
}

// --- LOGIKA UNTUK MENGAMBIL DAFTAR KELAS MILIK DOSEN ---
$kelas_dosen = [];
try {
    // PERBAIKAN: Menggunakan nama kolom 'dosenId' yang benar
    $stmt = $conn->prepare("SELECT id, namaKelas, kodeKelas FROM kelas WHERE dosenId = ? ORDER BY createdAt DESC");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $kelas_dosen = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    $error = "Gagal memuat daftar kelas karena masalah server. Pesan: " . $e->getMessage();
}

$page_title = 'Manajemen Kelas';
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
        .msg-success, .msg-error { padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-weight: 500; }
        .msg-success { background: var(--success-bg); color: var(--success-text); }
        .msg-error { background: var(--error-bg); color: var(--error-text); }
        .class-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
        .class-card { display: block; background-color: var(--bg-light); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border-color); transition: all 0.2s ease; }
        .class-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: var(--primary-color); }
        .class-card-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; }
        .class-card-code { font-size: 0.9rem; color: var(--secondary-color); font-family: monospace; }
        .class-card-code span { font-weight: 600; color: var(--primary-color); }
        .empty-state { text-align: center; padding: 3rem; border: 2px dashed var(--border-color); border-radius: var(--radius); }
        .empty-state h3 { font-size: 1.25rem; }
        .empty-state p { margin-top: 0.5rem; color: var(--secondary-color); max-width: 400px; margin-left: auto; margin-right: auto; }
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(17, 24, 39, 0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 3000; opacity: 0; transition: opacity 0.3s ease; }
        .modal.active { display: flex; opacity: 1; }
        .modal-content { background: var(--bg-white); padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow-md); max-width: 450px; width: 95%; transform: scale(0.95); transition: transform 0.3s ease; }
        .modal.active .modal-content { transform: scale(1); }
        .modal-header { padding-bottom: 1rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); }
        .modal-header h3 { font-size: 1.25rem; }
        .modal-footer { margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-control { width: 100%; padding: 0.75rem; border-radius: var(--radius); border: 1px solid var(--border-color); font-size: 1rem; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2); }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-weight: 600; padding: 0.75rem 1.25rem; border-radius: var(--radius); border: 1px solid transparent; cursor: pointer; transition: all 0.2s ease; font-size: 0.9rem; }
        .btn-primary { background-color: var(--primary-color); color: var(--text-light); }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-outline { background-color: transparent; color: var(--secondary-color); border-color: var(--border-color); }
        .btn-outline:hover { background-color: var(--bg-light); color: var(--text-primary); }
        .btn.w-full { width: 100%; }
        .menu-toggle { display: none; background: none; border: none; cursor: pointer; padding: 0.5rem; }

        /* --- RESPONSIVE STYLES --- */
        @media (min-width: 768px) {
            .user-profile span { display: inline; }
            .card-header { flex-direction: row; align-items: center; }
            .class-grid { grid-template-columns: repeat(2, 1fr); }
            .desktop-only { display: inline !important; }
            .mobile-only { display: none !important; }
        }
        @media (min-width: 1024px) {
            .class-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
        }
        @media (max-width: 767px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1.5rem; }
            .main-header h1 { font-size: 1.5rem; }
            .menu-toggle { display: block; }
            .desktop-only { display: none !important; }
            .mobile-only { display: inline !important; }
            .modal-footer { flex-direction: column-reverse; gap: 0.75rem; }
            .modal-footer .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
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
                    <h3>Daftar Kelas Anda</h3>
                    <button onclick="openModal('modal-buat')" class="btn btn-primary">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span class="desktop-only">Buat Kelas</span>
                        <span class="mobile-only">Baru</span>
                    </button>
                </div>

                <?php if ($success_msg): ?>
                    <div class="msg-success"><?= $success_msg ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="msg-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (empty($kelas_dosen)): ?>
                    <div class="empty-state">
                        <h3>Anda belum memiliki kelas</h3>
                        <p>Silakan buat kelas pertama Anda agar siswa dapat bergabung.</p>
                    </div>
                <?php else: ?>
                    <div class="class-grid">
                        <?php foreach ($kelas_dosen as $k): ?>
                            <a href="kelas_detail.php?id=<?= $k['id'] ?>" class="class-card">
                                <h4 class="class-card-title"><?= htmlspecialchars($k['namaKelas']) ?></h4>
                                <p class="class-card-code">Kode: <span><?= htmlspecialchars($k['kodeKelas']) ?></span></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- MODAL UNTUK TAMBAH KELAS -->
    <div id="modal-buat" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Buat Kelas Baru</h3>
            </div>
            <form method="post" action="dashboard_dosen.php">
                <div class="form-group">
                    <label for="namaKelas">Nama Kelas</label>
                    <input type="text" name="namaKelas" id="namaKelas" class="form-control" placeholder="Contoh: Pemrograman Web" required>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('modal-buat')" class="btn btn-outline">Batal</button>
                    <button type="submit" name="buatKelas" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
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

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) modal.classList.add('active');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) modal.classList.remove('active');
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === "Escape") {
                document.querySelectorAll('.modal.active').forEach(modal => closeModal(modal.id));
            }
        });
    </script>
</body>
</html>
