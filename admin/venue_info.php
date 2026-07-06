<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_lapangan') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

$stmt = $pdo->prepare("SELECT * FROM venues WHERE user_id = ?");
$stmt->execute([$user_id]);
$venue = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);

    if ($venue) {
        $updateStmt = $pdo->prepare("UPDATE venues SET name = ?, location = ?, description = ? WHERE user_id = ?");
        if ($updateStmt->execute([$name, $location, $description, $user_id])) {
            $message = "<p style='color:green;'>Profil gedung berhasil diperbarui!</p>";
            $stmt->execute([$user_id]);
            $venue = $stmt->fetch();
        }
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO venues (user_id, name, location, description, status) VALUES (?, ?, ?, ?, 'pending')");
        if ($insertStmt->execute([$user_id, $name, $location, $description])) {
            $message = "<p style='color:green;'>Profil gedung berhasil dibuat! Menunggu validasi superadmin.</p>";
            $stmt->execute([$user_id]);
            $venue = $stmt->fetch();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Info Gedung - ArenaGO</title>
    <style>
        body { font-family: Poppins, sans-serif; margin: 0; display: flex; background: #f4f4f4; }
        .sidebar { width: 250px; background: #1a1a1a; color: white; height: 100vh; padding: 20px; }
        .sidebar h2 { color: #00bfa5; text-align: center; }
        .sidebar a { display: block; color: white; text-decoration: none; padding: 10px; margin: 10px 0; background: #333; border-radius: 5px; }
        .sidebar a:hover { background: #00bfa5; }
        .content { flex: 1; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 600px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #00bfa5; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #009985; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Arena<span>GO</span></h2>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="venue_info.php" style="background: #00bfa5;">🏢 Info Gedung (Venue)</a>
        <a href="courts.php">🏸 Kelola Lapangan</a>
        <a href="bookings.php">📅 Jadwal Booking</a>
        <a href="../auth/logout.php" style="background: #e74c3c;">🚪 Logout</a>
    </div>

    <div class="content">
        <h1>Informasi Gedung (Venue)</h1>
        
        <div class="card">
            <?= $message ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label>Nama Gedung Olahraga</label>
                    <input type="text" name="name" value="<?= $venue ? htmlspecialchars($venue['name']) : '' ?>" required placeholder="Contoh: GOR Arena Merdeka">
                </div>
                
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="location" rows="3" required placeholder="Jalan, RT/RW, Kota..."><?= $venue ? htmlspecialchars($venue['location']) : '' ?></textarea>
                </div>

                <div class="form-group">
                    <label>Fasilitas / Deskripsi Singkat</label>
                    <textarea name="description" rows="3" placeholder="Ada kantin, toilet bersih, parkir luas..."><?= $venue ? htmlspecialchars($venue['description']) : '' ?></textarea>
                </div>
                
                <button type="submit"><?= $venue ? 'Simpan Perubahan' : 'Daftarkan Gedung' ?></button>
            </form>
        </div>
    </div>

</body>
</html>