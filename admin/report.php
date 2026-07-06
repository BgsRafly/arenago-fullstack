<?php
include '../config/database.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_lapangan') {
    die("Akses Terlarang!");
}

try {
    $stmtV = $pdo->prepare("SELECT id FROM venues WHERE user_id = :uid");
    $stmtV->execute(['uid' => $_SESSION['user_id']]);
    $venue_id = $stmtV->fetchColumn();

    $stOn = $pdo->prepare("SELECT SUM(total_price) FROM bookings b JOIN courts c ON b.court_id = c.id WHERE c.venue_id = :vid AND b.payment_type = 'online' AND b.status = 'success'");
    $stOn->execute(['vid' => $venue_id]);
    $revenue_online = $stOn->fetchColumn() ?? 0;

    $stOff = $pdo->prepare("SELECT SUM(total_price) FROM bookings b JOIN courts c ON b.court_id = c.id WHERE c.venue_id = :vid AND b.payment_type = 'offline' AND b.status = 'success'");
    $stOff->execute(['vid' => $venue_id]);
    $revenue_offline = $stOff->fetchColumn() ?? 0;

} catch (PDOException $e) {
    die("Gagal memuat rekapitulasi: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Laporan Keuangan Bisnis - ArenaGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background:#F8F9FA; padding:40px; margin:0; }
        .report-grid { display:grid; grid-template-columns: repeat(3, 1fr); gap:20px; margin-top:20px; }
        .card { background:white; padding:25px; border-radius:10px; border:1px solid #EAEAEA; text-align:center; }
    </style>
</head>
<body>
    <a href="dashboard.php" style="color:#004AC6; text-decoration:none; font-weight:600;"><- Kembali</a>
    <h2>Analisis Kas Rekapitulasi Lapangan</h2>
    
    <div class="report-grid">
        <div class="card">
            <h4 style="margin:0; color:#1A73E8;">Pendapatan Saldo Online Apps</h4>
            <p style="font-size:24px; font-weight:700; margin:10px 0 0 0; color:#1A73E8;">Rp <?php echo number_format($revenue_online, 0, ',', '.'); ?></p>
        </div>
        <div class="card">
            <h4 style="margin:0; color:#137333;">Pendapatan Tunai/Cash Offline</h4>
            <p style="font-size:24px; font-weight:700; margin:10px 0 0 0; color:#137333;">Rp <?php echo number_format($revenue_offline, 0, ',', '.'); ?></p>
        </div>
        <div class="card" style="background:#004AC6; color:white;">
            <h4 style="margin:0;">Akumulasi Omzet Bersih</h4>
            <p style="font-size:24px; font-weight:700; margin:10px 0 0 0;">Rp <?php echo number_format($revenue_online + $revenue_offline, 0, ',', '.'); ?></p>
        </div>
    </div>
</body>
</html>