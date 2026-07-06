<?php
include 'config/database.php';
include 'includes/header.php';

try {
    $stmt = $pdo->prepare("SELECT b.*, c.court_name, v.name as venue_name, v.location 
                           FROM bookings b 
                           JOIN courts c ON b.court_id = c.id 
                           JOIN venues v ON c.venue_id = v.id 
                           WHERE b.user_id = :uid ORDER BY b.id DESC");
    $stmt->execute(['uid' => $_SESSION['user_id']]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Pesanan Saya - ArenaGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/home.css">
    <style>
        .container { padding: 40px 8%; max-width: 1200px; margin: 0 auto; font-family: 'Inter', sans-serif; }
        h2 { font-family: 'Poppins', sans-serif; margin-bottom: 25px; }
        .booking-card { background: white; border: 1px solid #EAEAEA; border-radius: 12px; padding: 20px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .badge-status { background: #28A745; color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .btn-rev { background: #004AC6; color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Riwayat Transaksi Pemesanan Anda</h2>
        <?php if (count($bookings) > 0): ?>
            <?php foreach ($bookings as $b): ?>
                <div class="booking-card">
                    <div>
                        <h3 style="margin:0 0 5px 0; font-family:'Poppins';"><?php echo htmlspecialchars($b['venue_name']); ?></h3>
                        <p style="margin:0 0 5px 0; color:#666; font-size:14px;">📍 <?php echo htmlspecialchars($b['location']); ?> | <strong><?php echo htmlspecialchars($b['court_name']); ?></strong></p>
                        <p style="margin:0; font-size:13px; color:#444;">📅 <?php echo date('d M Y', strtotime($b['booking_date'])); ?> (<?php echo substr($b['start_time'],0,5); ?> - <?php echo substr($b['end_time'],0,5); ?>)</p>
                    </div>
                    <div style="text-align: right; display:flex; flex-direction:column; gap:10px; align-items:flex-end;">
                        <span class="badge-status"><?php echo strtoupper($b['status']); ?></span>
                        <strong style="color:#004AC6;">Rp <?php echo number_format($b['total_price'], 0, ',', '.'); ?></strong>
                        <a href="write_review.php?venue_id=<?php echo $b['court_id']; ?>" class="btn-rev">Tulis Ulasan</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Anda belum memiliki riwayat pemesanan lapangan.</p>
        <?php endif; ?>
    </div>
</body>
</html>