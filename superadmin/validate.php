<?php
include '../config/database.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    die("Akses ilegal.");
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$venue_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($venue_id > 0 && ($action === 'approve' || $action === 'reject')) {
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    try {
        $stmt = $pdo->prepare("UPDATE venues SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $venue_id]);
        
        echo "<script>alert('Keputusan validasi berkas berhasil disimpan!'); window.location.href='dashboard.php';</script>";
    } catch (PDOException $e) {
        die("Gagal memproses keputusan berkas: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
}
exit;
?>