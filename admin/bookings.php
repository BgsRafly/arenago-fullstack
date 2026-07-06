<?php
include '../config/database.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_lapangan') {
    die("Akses Ilegal!");
}

try {
    $stmtV = $pdo->prepare("SELECT id FROM venues WHERE user_id = :uid");
    $stmtV->execute(['uid' => $_SESSION['user_id']]);
    $venue_id = $stmtV->fetchColumn();

    if (isset($_GET['cancel_id'])) {
        $cancel_id = intval($_GET['cancel_id']);
        $upd = $pdo->prepare("UPDATE bookings b 
                              JOIN courts c ON b.court_id = c.id 
                              SET b.status = 'cancelled' 
                              WHERE b.id = :bid AND c.venue_id = :vid");
        $upd->execute(['bid' => $cancel_id, 'vid' => $venue_id]);
        header("Location: bookings.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT b.*, c.court_name, u.name as online_name 
                           FROM bookings b 
                           JOIN courts c ON b.court_id = c.id 
                           LEFT JOIN users u ON b.user_id = u.id 
                           WHERE c.venue_id = :vid ORDER BY b.booking_date DESC, b.start_time DESC");
    $stmt->execute(['vid' => $venue_id]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal memproses data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Manajemen Pesanan - ArenaGO Mitra</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background:#F8F9FA; padding:30px; margin:0; }
        .container { background:white; padding:25px; border-radius:10px; border:1px solid #EAEAEA; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        table th, table td { padding:12px; border-bottom:1px solid #EAEAEA; text-align:left; }
        table th { background:#F4F8FF; color:#004AC6; }
        .badge { padding:4px 8px; border-radius:4px; font-size:12px; font-weight:700; }
        .btn-cancel { background:#DC3545; color:white; padding:5px 10px; border-radius:4px; text-decoration:none; font-size:12px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" style="color:#004AC6; text-decoration:none; font-weight:600;"><- Kembali</a>
        <h2>Daftar Seluruh Reservasi Sesi Lapangan</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kategori Unit</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal Main</th>
                    <th>Jam Sesi</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Aksi Pengosongan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $b): ?>
                <tr>
                    <td>#<?php echo $b['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($b['court_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($b['payment_type'] === 'online' ? $b['online_name'] : $b['customer_name_offline']); ?></td>
                    <td><?php echo $b['booking_date']; ?></td>
                    <td><?php echo substr($b['start_time'],0,5) . " - " . substr($b['end_time'],0,5); ?></td>
                    <td><span class="badge" style="background:#EAEAEA;"><?php echo strtoupper($b['payment_type']); ?></span></td>
                    <td>
                        <span class="badge" style="background:<?php echo $b['status']=='success'?'#E6F4EA;color:#137333;':'#FCE8E6;color:#C5221F;'; ?>">
                            <?php echo strtoupper($b['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if($b['status'] === 'success'): ?>
                            <a href="bookings.php?cancel_id=<?php echo $b['id']; ?>" class="btn-cancel" onclick="return confirm('Batalkan sesi jadwal ini?')">Batalkan</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>