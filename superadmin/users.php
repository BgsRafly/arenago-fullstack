<?php
include '../config/database.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    die("Akses ditolak.");
}

try {
    $stmt = $pdo->query("SELECT id, name, email, phone, role, created_at FROM users ORDER BY id DESC");
    $all_users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal menarik data master user: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Master Users - Superadmin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background:#F8F9FA; padding:30px; margin:0; }
        table { width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden; border:1px solid #EAEAEA; }
        table th, table td { padding:12px; border-bottom:1px solid #EAEAEA; text-align:left; }
        table th { background:#F4F8FF; color:#004AC6; }
    </style>
</head>
<body>
    <a href="dashboard.php" style="color:#004AC6; text-decoration:none; font-weight:600;"><- Kembali ke Utama</a>
    <h2>Manajemen Seluruh Akun Pengguna Terdaftar</h2>
    <table>
        <thead>
            <tr>
                <th>ID Akun</th>
                <th>Nama Lengkap</th>
                <th>Alamat Email</th>
                <th>No. Telepon Kontak</th>
                <th>Hak Akses Peran</th>
                <th>Tanggal Bergabung</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($all_users as $u): ?>
            <tr>
                <td>#<?php echo $u['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['phone']); ?></td>
                <td><span style="font-weight:700; color:<?php echo $u['role']=='superadmin'?'red':($u['role']=='admin_lapangan'?'orange':'green'); ?>"><?php echo strtoupper($u['role']); ?></span></td>
                <td><?php echo $u['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>