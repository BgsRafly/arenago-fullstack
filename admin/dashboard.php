<?php
include '../config/database.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_lapangan') {
    die("Akses Ditolak! Halaman ini dikunci khusus akun Admin Lapangan.");
}

try {
    $stmtV = $pdo->prepare("SELECT id, name, status FROM venues WHERE user_id = :uid");
    $stmtV->execute(['uid' => $_SESSION['user_id']]);
    $my_venue = $stmtV->fetch();

    if (!$my_venue) {
        die("Anda belum memiliki properti lapangan olahraga yang terdaftar.");
    }

    if ($my_venue['status'] !== 'approved') {
        die("<h2>Akun Toko/Lapangan Anda Belum Aktif!</h2><p>Pendaftaran tempat olahraga Anda masih dalam antrean peninjauan oleh tim Superadmin ArenaGO. Silakan tunggu hingga status divalidasi berkas keasliannya.</p><br><a href='../auth/logout.php'>Keluar Sistem</a>");
    }

    $venue_id = $my_venue['id'];

    $msg = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_offline_booking'])) {
        $court_id = intval($_POST['court_id']);
        $bdate = $_POST['booking_date'];
        $bstart = $_POST['start_time'];
        $bend = date('H:i:s', strtotime($bstart) + 3600);
        $cust_name = trim($_POST['customer_name']);
        $cust_phone = trim($_POST['customer_phone']);
        $price = intval($_POST['price_snap']);

        $checkOverlap = $pdo->prepare("SELECT id FROM bookings 
                                       WHERE court_id = :cid AND booking_date = :bdate 
                                       AND ((start_time <= :bstart AND end_time > :bstart) 
                                       OR (start_time < :bend AND end_time >= :bend))");
        $checkOverlap->execute(['cid' => $court_id, 'bdate' => $bdate, 'bstart' => $bstart, 'bend' => $bend]);

        if ($checkOverlap->rowCount() > 0) {
            $msg = "<span style='color:red; font-weight:700;'>Gagal! Sesi jadwal tersebut sudah dipesan oleh pengguna lain. Silakan pilih opsi jam atau lapangan berbeda.</span>";
        } else {
            $insOffline = $pdo->prepare("INSERT INTO bookings (court_id, customer_name_offline, customer_phone_offline, booking_date, start_time, end_time, total_price, payment_type, status) 
                                         VALUES (:cid, :name, :phone, :bdate, :bstart, :bend, :price, 'offline', 'success')");
            $insOffline->execute(['cid' => $court_id, 'name' => $cust_name, 'phone' => $cust_phone, 'bdate' => $bdate, 'bstart' => $bstart, 'bend' => $bend, 'price' => $price]);
            $msg = "<span style='color:green; font-weight:700;'>Sukses! Berhasil mengunci slot jadwal secara manual (Booking Offline).</span>";
        }
    }

    $stmtC = $pdo->prepare("SELECT * FROM courts WHERE venue_id = :vid");
    $stmtC->execute(['vid' => $venue_id]);
    $my_courts = $stmtC->fetchAll();

    $stmtB = $pdo->prepare("SELECT b.*, c.court_name, u.name as cust_online_name, u.phone as cust_online_phone 
                            FROM bookings b 
                            JOIN courts c ON b.court_id = c.id 
                            LEFT JOIN users u ON b.user_id = u.id 
                            WHERE c.venue_id = :vid ORDER BY b.id DESC");
    $stmtB->execute(['vid' => $venue_id]);
    $all_bookings = $stmtB->fetchAll();

} catch (PDOException $e) {
    die("Error operasional: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Panel Mitra Lapangan - ArenaGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background:#F8F9FA; margin:0; padding:25px; }
        .flex-layout { display: flex; gap: 25px; margin-top:30px; }
        .box-panel { background:white; padding:20px; border-radius:10px; border:1px solid #EAEAEA; }
        table { width:100%; border-collapse:collapse; font-size:13px; margin-top:15px; }
        table th, table td { padding:10px; text-align:left; border-bottom:1px solid #EAEAEA; }
        table th { background:#F4F8FF; color:#004AC6; }
        input, select { width:100%; padding:8px; margin-bottom:12px; border:1px solid #CCC; border-radius:4px; box-sizing: border-box; }
        .btn-action { background:#004AC6; color:white; font-weight:600; border:none; padding:10px; border-radius:4px; cursor:pointer; width:100%; }
    </style>
</head>
<body>
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 style="margin:0; font-family:'Poppins'; color:#004AC6;">Panel Operasional: <?php echo htmlspecialchars($my_venue['name']); ?></h1>
            <p style="margin:4px 0 0 0; color:#666;">Kelola kuota penambahan unit sekat lapangan dan input pencatatan kasir main offline.</p>
        </div>
        <a href="../auth/logout.php" style="background:#DC3545; color:white; padding:8px 16px; text-decoration:none; border-radius:6px; font-weight:600; font-size:14px;">Keluar</a>
    </div>

    <?php if(!empty($msg)) echo "<div style='margin-top:20px;'>$msg</div>"; ?>

    <div class="flex-layout">
        <div class="box-panel" style="flex: 1; height: fit-content;">
            <h3 style="font-family:'Poppins'; margin-top:0; border-bottom:2px solid #F4F8FF; padding-bottom:8px;">Pencatatan Sewa Offline (Manual)</h3>
            <form method="POST">
                <input type="hidden" name="action_offline_booking" value="1">
                
                <label>Pilih Unit Lapangan</label>
                <select name="court_id" required>
                    <?php foreach($my_courts as $mc): ?>
                        <option value="<?php echo $mc['id']; ?>"><?php echo htmlspecialchars($mc['court_name']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Nama Tamu Lapangan</label>
                <input type="text" name="customer_name" placeholder="Contoh: Budi Walk-In" required>

                <label>Nomor Telepon Tamu</label>
                <input type="text" name="customer_phone" placeholder="Contoh: 08999888777" required>

                <label>Tanggal Bermain</label>
                <input type="date" name="booking_date" value="<?php echo date('Y-m-d'); ?>" required>

                <label>Jam Mulai Sesi (Durasi Otomatis 1 Jam)</label>
                <select name="start_time" required>
                    <option value="08:00:00">08:00 - 09:00</option>
                    <option value="09:00:00">09:00 - 10:00</option>
                    <option value="10:00:00">10:00 - 11:00</option>
                    <option value="15:00:00">15:00 - 16:00</option>
                    <option value="19:00:00">19:00 - 20:00</option>
                </select>

                <label>Biaya Sewa Cash (Rp)</label>
                <input type="number" name="price_snap" placeholder="Contoh: 75000" required>

                <button type="submit" class="btn-action">Kunci Jadwal Offline</button>
            </form>

            <div style="margin-top:35px; border-top:2px solid #F4F8FF; padding-top:20px;">
                <h3 style="font-family:'Poppins'; margin-top:0;">Tambah Unit Sekat Kategori Lapangan Baru</h3>
                <a href="courts.php" style="display:inline-block; text-align:center; background:#28A745; color:white; font-weight:600; padding:10px; border-radius:4px; text-decoration:none; width:100%; box-sizing:border-box;">Buka Menu Pengelola Lapangan (Courts)</a>
            </div>
        </div>

        <div class="box-panel" style="flex: 2;">
            <h3 style="font-family:'Poppins'; margin-top:0; border-bottom:2px solid #F4F8FF; padding-bottom:8px;">Arus Sesi Log Pesanan Terjadwal</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tipe Unit</th>
                        <th>Nama Penyewa</th>
                        <th>Kontak Telepon</th>
                        <th>Sesi Jadwal</th>
                        <th>Metode</th>
                        <th>Jumlah Terima Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($all_bookings) > 0): ?>
                        <?php foreach($all_bookings as $ab): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($ab['court_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($ab['payment_type'] === 'online' ? $ab['cust_online_name'] : $ab['customer_name_offline']); ?></td>
                                <td>📞 <?php echo htmlspecialchars($ab['payment_type'] === 'online' ? $ab['cust_online_phone'] : $ab['customer_phone_offline']); ?></td>
                                <td>📅 <?php echo date('d/m/Y', strtotime($ab['booking_date'])); ?><br><small style="color:#666; font-weight:600;"><?php echo substr($ab['start_time'],0,5) . " - " . substr($ab['end_time'],0,5); ?></small></td>
                                <td>
                                    <span style="padding:3px 6px; border-radius:4px; font-size:11px; font-weight:700; background: <?php echo $ab['payment_type'] === 'online' ? '#E8F0FE; color:#1A73E8;' : '#E6F4EA; color:#137333;'; ?>">
                                        <?php echo strtoupper($ab['payment_type']); ?>
                                    </span>
                                </td>
                                <td style="font-weight:700; color:#28A745;">Rp <?php echo number_format($ab['total_price'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; color:#777; font-style:italic;">Belum ada catatan aktivitas pemesanan terjadwal untuk saat ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>