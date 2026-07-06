<?php
include 'config/database.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Anda wajib login atau daftar akun terlebih dahulu sebelum memesan lapangan!'); window.location.href='auth/login.php';</script>";
    exit;
}

$court_id = isset($_GET['court_id']) ? intval($_GET['court_id']) : 0;
$booking_date = isset($_GET['date']) ? $_GET['date'] : '';
$start_time = isset($_GET['time']) ? $_GET['time'] : '';

if ($court_id <= 0 || empty($booking_date) || empty($start_time)) {
    die("<script>alert('Informasi jadwal tidak valid!'); window.location.href='search.php';</script>");
}

$end_time = date('H:i:s', strtotime($start_time) + 3600);

try {
    $stmt = $pdo->prepare("SELECT c.*, v.name as venue_name, v.location, v.phone as venue_phone 
                           FROM courts c 
                           JOIN venues v ON c.venue_id = v.id 
                           WHERE c.id = :court_id");
    $stmt->execute(['court_id' => $court_id]);
    $court = $stmt->fetch();

    if (!$court) { die("Data lapangan tidak ditemukan."); }

    $stmtUsr = $pdo->prepare("SELECT phone, email FROM users WHERE id = :uid");
    $stmtUsr->execute(['uid' => $_SESSION['user_id']]);
    $user_info = $stmtUsr->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $total_price = $court['price_per_hour'] + 2000;

        $ins = $pdo->prepare("INSERT INTO bookings (user_id, court_id, booking_date, start_time, end_time, total_price, payment_type, status) 
                              VALUES (:uid, :cid, :bdate, :bstart, :bend, :total, 'online', 'success')");
        $ins->execute([
            'uid' => $_SESSION['user_id'],
            'cid' => $court_id,
            'bdate' => $booking_date,
            'bstart' => $start_time,
            'bend' => $end_time,
            'total' => $total_price
        ]);

        echo "<script>alert('Pemesanan lapangan Anda berhasil diproses!'); window.location.href='my_bookings.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("Gagal memproses transaksi: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Konfirmasi Pembayaran - ArenaGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/booking.css">
</head>
<body>
    <main class="booking-wrapper">
        <section class="booking-main">
            <h2>Konfirmasi Pembayaran Anda</h2>
            <div class="booking-section-card">
                <h3>Informasi Profil Kontak</h3>
                <div class="form-grid">
                    <div class="form-group"><label>Nama Lengkap</label><input type="text" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" class="form-input" readonly></div>
                    <div class="form-group"><label>Nomor Telepon Akun</label><input type="text" value="<?php echo htmlspecialchars($user_info['phone']); ?>" class="form-input" readonly></div>
                </div>
            </div>
            <div class="booking-section-card">
                <h3>Gerbang Pembayaran</h3>
                <p style="font-size:14px; color:#555;">Silakan selesaikan pembayaran tagihan Anda secara otomatis menggunakan Transfer Virtual Account atau QRIS terpadu.</p>
            </div>
        </section>

        <aside class="booking-sidebar">
            <div class="summary-card">
                <h3>Ringkasan Sewa</h3>
                <div class="venue-summary">
                    <h4><?php echo htmlspecialchars($court['venue_name']); ?></h4>
                    <p>📍 <?php echo htmlspecialchars($court['location']); ?></p>
                    <p style="color:#004AC6; font-size:13px; font-weight:600;">📞 Telp Lapangan: <?php echo htmlspecialchars($court['venue_phone']); ?></p>
                    <span class="badge-court"><?php echo htmlspecialchars($court['court_name']); ?></span>
                </div>
                <hr class="divider">
                <div class="booking-details-list">
                    <div class="detail-row"><span>Tanggal Sesi</span><strong><?php echo date('d M Y', strtotime($booking_date)); ?></strong></div>
                    <div class="detail-row"><span>Jam Bermain</span><strong><?php echo substr($start_time, 0, 5) . " - " . substr($end_time, 0, 5); ?></strong></div>
                </div>
                <hr class="divider">
                <div class="price-breakdown">
                    <div class="price-row"><span>Sewa per Jam</span><span>Rp <?php echo number_format($court['price_per_hour'], 0, ',', '.'); ?></span></div>
                    <div class="price-row"><span>Biaya Layanan</span><span>Rp 2.000</span></div>
                    <hr class="divider-dashed">
                    <div class="price-row total"><span>Total Bayar</span><span class="total-amount">Rp <?php echo number_format($court['price_per_hour'] + 2000, 0, ',', '.'); ?></span></div>
                </div>
                <form method="POST"><button type="submit" class="btn-pay-now">Bayar Sewa Sekarang</button></form>
            </div>
        </aside>
    </main>
</body>
</html>