<?php
include '../config/database.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_lapangan') {
    die("Akses ilegal.");
}

try {
    $stmtV = $pdo->prepare("SELECT id FROM venues WHERE user_id = :uid");
    $stmtV->execute(['uid' => $_SESSION['user_id']]);
    $venue_id = $stmtV->fetchColumn();

    $msg = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $court_name = trim($_POST['court_name']);
        $price = intval($_POST['price_per_hour']);

        if (!empty($court_name) && $price > 0) {
            $ins = $pdo->prepare("INSERT INTO courts (venue_id, court_name, price_per_hour, image) VALUES (:vid, :cname, :price, 'default_court.jpg')");
            $ins->execute(['vid' => $venue_id, 'cname' => $court_name, 'price' => $price]);
            $msg = "<p style='color:green; font-weight:700;'>Sukses menambahkan unit lapangan baru!</p>";
        }
    }

    $stmtC = $pdo->prepare("SELECT * FROM courts WHERE venue_id = :vid");
    $stmtC->execute(['vid' => $venue_id]);
    $courts = $stmtC->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Kelola Unit Lapangan - ArenaGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background:#F8F9FA; padding:40px; }
        .container { max-width: 600px; background:white; padding:30px; border-radius:12px; border:1px solid #EAEAEA; margin:0 auto; }
        input { width:100%; padding:10px; margin-bottom:15px; border:1px solid #CCC; border-radius:6px; box-sizing:border-box; }
        .btn { background:#004AC6; color:white; border:none; padding:12px; border-radius:6px; width:100%; font-weight:600; cursor:pointer; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" style="color:#004AC6; text-decoration:none; font-weight:600; font-size:14px;"><- Kembali ke Dashboard</a>
        <h2 style="font-family:'Poppins'; margin-top:15px; color:#333;">Tambah Kategori Unit Lapangan</h2>
        
        <?php echo $msg; ?>
        
        <form method="POST">
            <label style="font-weight:600; font-size:13px; display:block; margin-bottom:5px;">Nama/Nomor Sekat Lapangan</label>
            <input type="text" name="court_name" placeholder="Contoh: Lapangan Utama Premium, Lapangan 2" required>
            
            <label style="font-weight:600; font-size:13px; display:block; margin-bottom:5px;">Tarif Biaya Sewa per Jam (Rp)</label>
            <input type="number" name="price_per_hour" placeholder="Contoh: 70000" required>
            
            <button type="submit" class="btn">Simpan Data Lapangan</button>
        </form>

        <h3 style="font-family:'Poppins'; margin-top:35px; border-top:1px solid #EAEAEA; padding-top:20px;">Daftar Lapangan Aktif</h3>
        <ul style="padding-left:20px; line-height:1.8;">
            <?php foreach($courts as $c): ?>
                <li><strong><?php echo htmlspecialchars($c['court_name']); ?></strong> - Rp <?php echo number_format($c['price_per_hour'], 0, ',', '.'); ?>/jam</li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>