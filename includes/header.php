<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArenaGO - Platform Booking Lapangan Badminton</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/main.js" defer></script>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #F8F9FA;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 8%;
            background-color: #FFFFFF;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-logo {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #004AC6;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 35px;
            list-style: none;
        }
        .nav-item a {
            text-decoration: none;
            color: #4A5568;
            font-size: 15px;
            font-weight: 500;
            padding: 8px 0;
            position: relative;
            transition: color 0.2s ease;
        }
        .nav-item a:hover {
            color: #004AC6;
        }
        .nav-item.active a {
            color: #004AC6;
            font-weight: 600;
        }
        .nav-item.active a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #004AC6;
            border-radius: 2px;
        }
        .nav-auth {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn-login {
            text-decoration: none;
            color: #004AC6;
            font-weight: 600;
            font-size: 15px;
            padding: 10px 20px;
        }
        .btn-register {
            text-decoration: none;
            background-color: #004AC6;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 15px;
            padding: 10px 24px;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }
        .btn-register:hover {
            background-color: #003794;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="nav-logo">ArenaGO</a>
        <ul class="nav-links">
            <li class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                <a href="index.php">Beranda</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'search.php') ? 'active' : ''; ?>">
                <a href="search.php">Eksplorasi</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'bantuan.php') ? 'active' : ''; ?>">
                <a href="bantuan.php">Bantuan</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item <?php echo ($current_page == 'my_bookings.php') ? 'active' : ''; ?>">
                    <a href="my_bookings.php">Booking Saya</a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="nav-auth">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span style="font-size: 14px; color: #4A5568;">Halo, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></span>
                <a href="auth/logout.php" class="btn-login" style="color: #DC3545;">Keluar</a>
            <?php else: ?>
                <a href="auth/login.php" class="btn-login">Masuk</a>
                <a href="auth/register.php" class="btn-register">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>