<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';
require_once '../config/auth.php';

// Jika sudah login, arahkan ke dashboard masing-masing
if (isLoggedIn()) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: ../admin/index.php");
    } else {
        header("Location: ../kasir/index.php");
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Cegah Session Fixation Attack: perbarui Session ID setelah login sukses
                session_regenerate_id(true);

                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect berdasarkan role
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/index.php");
                } else {
                    header("Location: ../kasir/index.php");
                }
                exit;
            } else {
                $error = 'Username atau password salah.';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pemesanan - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        neutral: {
                            50: '#FAFAFA',
                            100: '#F5F5F5',
                            200: '#E5E5E5',
                            800: '#262626',
                            900: '#171717',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-neutral-50 h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm bg-white p-8 border border-neutral-200">
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-medium tracking-tighter mb-2">System.</h1>
            <p class="text-xs uppercase tracking-widest text-neutral-500 font-medium">Authorized Access Only</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-black text-white p-3 mb-6 text-sm border border-black">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <?= csrf_field() ?>
            <div class="floating-input">
                <input type="text" name="username" id="username" required 
                       class="w-full pb-2 pt-4 text-sm bg-transparent" 
                       placeholder="Username" autocomplete="off">
            </div>
            
            <div class="floating-input">
                <input type="password" name="password" id="password" required 
                       class="w-full pb-2 pt-4 text-sm bg-transparent" 
                       placeholder="Password">
            </div>

            <button type="submit" 
                    class="w-full py-4 bg-black text-white text-sm uppercase tracking-widest font-medium hover-invert transition-snappy mt-8">
                Login
            </button>
        </form>
        
        <div class="mt-8 text-center border-t border-neutral-100 pt-6">
            <p class="text-xs text-neutral-400">&copy; <?= date('Y') ?> Minimalist Cafe System</p>
        </div>
    </div>

</body>
</html>
