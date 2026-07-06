<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $login_sukses = false;

                if (password_verify($password, $user['password'])) {
                    $login_sukses = true;
                } elseif (md5($password) === $user['password']) {
                    $login_sukses = true;
                }

                if ($login_sukses) {
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name']    = $user['name'];
                    $_SESSION['role']    = $user['role'];

                    if ($redirect === 'search') {
                        header("Location: ../search.php");
                        exit;
                    }

                    if ($user['role'] === 'superadmin') {
                        header("Location: ../superadmin/dashboard.php");
                        exit;
                    } elseif ($user['role'] === 'admin_lapangan') {
                        header("Location: ../admin/dashboard.php");
                        exit;
                    } else {
                        header("Location: ../index.php");
                        exit;
                    }
                } else {
                    $error = "Email atau password salah!";
                }
            } else {
                $error = "Email atau password salah!";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    } else {
        $error = "Silakan isi semua kolom!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - ArenaGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8F9FA; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-card { background: #FFFFFF; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); width: 100%; max-width: 420px; }
        .login-card h2 { color: #004AC6; margin-bottom: 8px; font-weight: 700; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #4A5568; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 6px; font-size: 15px; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: #004AC6; }
        .alert-danger { background-color: #FED7D7; color: #C53030; padding: 12px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; }
        .btn-submit { background-color: #004AC6; color: #FFFFFF; width: 100%; padding: 14px; border: none; border-radius: 6px; font-weight: 600; font-size: 16px; cursor: pointer; transition: background-color 0.2s; }
        .btn-submit:hover { background-color: #003794; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Masuk ArenaGO</h2>
    <p style="color: #718096; font-size: 14px; margin-bottom: 24px;">Silakan masuk ke panel kendali Anda.</p>

    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan alamat email Anda" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
        </div>
        <button type="submit" class="btn-submit">Masuk</button>
    </form>
</div>

</body>
</html>