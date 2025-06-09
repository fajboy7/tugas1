<?php
// File: index.php (Versi Final yang Sudah Diperbaiki)

// functions.php sudah menyertakan config.php (yang memulai sesi)
require_once "functions.php";

// Jika pengguna sudah login, langsung arahkan ke dasbor utama.
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$success = "";
// Tentukan mode halaman (login atau register) berdasarkan parameter URL.
$isLoginMode = !isset($_GET["register"]);

// Logika untuk memproses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // ==== PROSES LOGIN ====
    if (isset($_POST["login"])) {
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        $role = $_POST["role"];

        // Memanggil fungsi login dengan parameter $conn yang diperlukan.
        if (login($conn, $email, $password, $role)) {
            // Jika login berhasil, arahkan ke dasbor.
            header("Location: dashboard.php");
            exit;
        } else {
            // Jika gagal, tampilkan pesan error.
            $error = "Email, password atau peran salah!";
        }
    } 
    // ==== PROSES REGISTRASI ====
    elseif (isset($_POST["register"])) {
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        $role = $_POST["role"];
        $isLoginMode = false; // Tetap di mode register jika ada error

        // Validasi input dasar
        if (empty($name) || empty($email) || empty($password)) {
            $error = "Semua kolom wajib diisi!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format email tidak valid!";
        } elseif (strlen($password) < 6) {
            $error = "Password minimal harus 6 karakter.";
        } else {
            try {
                // Cek apakah email sudah terdaftar menggunakan prepared statement
                $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt_check->bind_param("s", $email);
                $stmt_check->execute();
                
                if ($stmt_check->get_result()->num_rows > 0) {
                    $error = "Email ini sudah terdaftar!";
                } else {
                    // Hash password untuk keamanan
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $now = date("Y-m-d H:i:s");
                    
                    // Insert pengguna baru menggunakan prepared statement
                    $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, role, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt_insert->bind_param("ssssss", $name, $email, $hashed_password, $role, $now, $now);
                    
                    if ($stmt_insert->execute()) {
                         $success = "Registrasi berhasil! Silakan login.";
                         $isLoginMode = true; // Arahkan ke form login setelah sukses
                    } else {
                        $error = "Registrasi gagal, terjadi kesalahan pada server.";
                    }
                    $stmt_insert->close();
                }
                $stmt_check->close();

            } catch (mysqli_sql_exception $e) {
                error_log("Register Error: " . $e->getMessage()); // Catat error ke log server
                $error = "Terjadi masalah pada server. Coba lagi nanti.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $isLoginMode ? "Login" : "Registrasi" ?> - Aplikasi Absensi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        body { 
            background: #f0f2ff; 
            min-height: 100vh; 
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .bg-shape { 
            position: absolute; 
            width: 400px; 
            height: 400px; 
            border-radius: 50%; 
            z-index: 0; 
            filter: blur(100px); 
            opacity: 0.5; 
        }
        .bg-shape1 { top: -150px; left: -150px; background: #818cf8; }
        .bg-shape2 { bottom: -150px; right: -150px; background: #60a5fa; }
        .auth-card { 
            position: relative; 
            z-index: 1; 
            width: 100%;
            max-width: 400px; 
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.2); 
            border-radius: 16px; 
            box-shadow: 0 8px 32px rgba(60, 72, 110, 0.15); 
            padding: 2.5rem;
            margin: 1rem;
        }
        .auth-card h2 { 
            font-size: 1.8rem; 
            color: #1f2937; 
            font-weight: 700; 
            margin-bottom: 1.5rem; 
            text-align: center; 
        }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: .5rem; color: #4b5563; font-weight: 500; }
        .form-group input, .form-group select { 
            width: 100%; 
            box-sizing: border-box; 
            padding: .8rem; 
            border-radius: 8px; 
            border: 1px solid #d1d5db; 
            outline: none; 
            font-size: 1rem; 
            background: #f9fafb; 
            color: #1f2937; 
            transition: all .2s; 
        }
        .form-group input:focus, .form-group select:focus { 
            border-color: #4f46e5; 
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2); 
            background: #fff;
        }
        .btn-main { 
            width: 100%; 
            background: #4f46e5; 
            color: #fff; 
            border: none; 
            padding: .9rem 0; 
            border-radius: 8px; 
            font-size: 1rem; 
            font-weight: 600; 
            cursor: pointer; 
            transition: background .2s; 
        }
        .btn-main:hover { background: #4338ca; }
        .switch-text { text-align: center; margin-top: 1.5rem; color: #4b5563; }
        .switch-link { color: #4f46e5; font-weight: 600; text-decoration: none; }
        .switch-link:hover { text-decoration: underline; }
        .msg-error, .msg-success { 
            text-align: center; 
            padding: .8rem; 
            border-radius: 8px; 
            margin-bottom: 1.25rem; 
            font-weight: 500;
        }
        .msg-error { background: #fee2e2; color: #b91c1c; }
        .msg-success { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <div class="bg-shape bg-shape1"></div>
    <div class="bg-shape bg-shape2"></div>

    <div class="auth-card">
        <h2><?= $isLoginMode ? "Selamat Datang" : "Buat Akun Baru" ?></h2>
        
        <?php if($error): ?><div class="msg-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if($success): ?><div class="msg-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <?php if($isLoginMode): ?>
        <!-- Form Login -->
        <form method="post" action="index.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Masuk sebagai</label>
                <select id="role" name="role">
                    <option value="siswa" <?= (($_POST['role'] ?? '') === 'siswa' ? 'selected' : '') ?>>Siswa</option>
                    <option value="dosen" <?= (($_POST['role'] ?? '') === 'dosen' ? 'selected' : '') ?>>Dosen</option>
                </select>
            </div>
            <button class="btn-main" type="submit" name="login">Login</button>
        </form>
        <p class="switch-text">
            Belum punya akun? <a href="index.php?register=1" class="switch-link">Daftar sekarang</a>
        </p>
        <?php else: ?>
        <!-- Form Registrasi -->
        <form method="post" action="index.php?register=1">
            <div class="form-group">
                <label for="name-reg">Nama Lengkap</label>
                <input id="name-reg" type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email-reg">Email</label>
                <input id="email-reg" type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password-reg">Password</label>
                <input id="password-reg" type="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role-reg">Daftar sebagai</label>
                <select id="role-reg" name="role">
                    <option value="siswa" <?= (($_POST['role'] ?? '') === 'siswa' ? 'selected' : '') ?>>Siswa</option>
                    <option value="dosen" <?= (($_POST['role'] ?? '') === 'dosen' ? 'selected' : '') ?>>Dosen</option>
                </select>
            </div>
            <button class="btn-main" type="submit" name="register">Buat Akun</button>
        </form>
        <p class="switch-text">
            Sudah punya akun? <a href="index.php" class="switch-link">Login di sini</a>
        </p>
        <?php endif; ?>
    </div>
</body>
</html>
