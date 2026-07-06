<?php
include 'config/database.php';
include 'includes/header.php';

$venue_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($venue_id <= 0) {
    die("<script>alert('Tempat olahraga tidak ditemukan!'); window.location.href='search.php';</script>");
}

try {
    $stmtVenue = $pdo->prepare("SELECT * FROM venues WHERE id = :vid");
    $stmtVenue->execute(['vid' => $venue_id]);
    $venue = $stmtVenue->fetch();

    if (!$venue) {
        die("<script>alert('Data tempat olahraga tidak valid!'); window.location.href='search.php';</script>");
    }

    $stmtCourts = $pdo->prepare("SELECT * FROM courts WHERE venue_id = :vid");
    $stmtCourts->execute(['vid' => $venue_id]);
    $courts = $stmtCourts->fetchAll();

    $stmtReviews = $pdo->prepare("SELECT r.*, u.name as user_name 
                                  FROM reviews r 
                                  JOIN users u ON r.user_id = u.id 
                                  WHERE r.venue_id = :vid ORDER BY r.id DESC");
    $stmtReviews->execute(['vid' => $venue_id]);
    $reviews = $stmtReviews->fetchAll();

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($venue['name']); ?> - ArenaGO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/detail.css">
</head>
<body>

    <main class="detail-container">
        
        <section class="detail-main">
            <div class="main-image-placeholder" style="background: url('assets/img/<?php echo htmlspecialchars($venue['image']); ?>') center/cover no-repeat; height: 400px; border-radius: 16px; margin-bottom: 25px;"></div>
            
            <h1 class="venue-title"><?php echo htmlspecialchars($venue['name']); ?></h1>
            <p class="venue-location">📍 <?php echo htmlspecialchars($venue['location']); ?></p>
            
            <div class="contact-card" style="background: #F4F8FF; border: 1px solid #BCD4FA; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                <p style="margin: 0; font-weight: 600; color: #004AC6; font-family: 'Poppins';">📞 Nomor Telepon Operasional Lapangan:</p>
                <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: 700; color: #333;"><?php echo htmlspecialchars($venue['phone'] ?? '0812-3456-7890'); ?></p>
            </div>

            <div class="courts-section" style="margin-top: 40px;">
                <h2 style="font-family: 'Poppins'; margin-bottom: 20px;">Daftar Lapangan Tersedia</h2>
                
                <?php if (count($courts) > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php foreach ($courts as $c): ?>
                            <div style="background: white; border: 1px solid #EAEAEA; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="margin: 0 0 5px 0; font-family: 'Poppins';"><?php echo htmlspecialchars($c['court_name']); ?></h3>
                                    <span style="color: #004AC6; font-weight: 700; font-size: 16px;">Rp <?php echo number_format($c['price_per_hour'], 0, ',', '.'); ?> <span style="font-size: 12px; font-weight: 400; color: #666;">/ Jam</span></span>
                                </div>
                                
                                <form action="booking.php" method="GET" style="display: flex; gap: 10px; align-items: center;">
                                    <input type="hidden" name="court_id" value="<?php echo $c['id']; ?>">
                                    
                                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required style="padding: 8px; border: 1px solid #CCC; border-radius: 6px;">
                                    
                                    <select name="time" required style="padding: 8px; border: 1px solid #CCC; border-radius: 6px;">
                                        <option value="08:00:00">08:00 - 09:00</option>
                                        <option value="09:00:00">09:00 - 10:00</option>
                                        <option value="10:00:00">10:00 - 11:00</option>
                                        <option value="15:00:00">15:00 - 16:00</option>
                                        <option value="19:00:00">19:00 - 20:00</option>
                                    </select>
                                    
                                    <button type="submit" style="background: #004AC6; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer;">Pesan Lapangan</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #666;">Belum ada data lapangan spesifik untuk tempat olahraga ini.</p>
                <?php endif; ?>
            </div>

            <div class="reviews-section" style="margin-top: 50px;">
                <h2 style="font-family: 'Poppins'; margin-bottom: 20px;">Ulasan Pengguna (<?php echo count($reviews); ?>)</h2>
                <?php if (count($reviews) > 0): ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div style="background: #FAFAFA; border-left: 4px solid #004AC6; padding: 15px; margin-bottom: 15px; border-radius: 0 8px 8px 0;">
                            <div style="display: flex; justify-content: space-between;">
                                <strong><?php echo htmlspecialchars($rev['user_name']); ?></strong>
                                <span style="color: #FFB300;">
                                    <?php echo str_repeat('⭐', $rev['rating']); ?>
                                </span>
                            </div>
                            <p style="margin: 8px 0 0 0; color: #555; font-size: 14px;"><?php echo htmlspecialchars($rev['review_text']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #888; font-style: italic;">Belum ada ulasan untuk tempat ini. Jadilah yang pertama memberikan ulasan setelah bermain!</p>
                <?php endif; ?>
            </div>
        </section>

    </main>

</body>
</html>