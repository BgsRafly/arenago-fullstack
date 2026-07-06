<?php
include 'includes/header.php';
?>
<style>
    .hero-section {
        background: linear-gradient(135deg, #004AC6 0%, #00266B 100%);
        color: #FFFFFF;
        padding: 100px 8%;
        text-align: center;
    }
    .hero-section h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }
    .hero-section p {
        font-size: 18px;
        color: #E2E8F0;
        margin-bottom: 40px;
    }
    .search-container {
        max-width: 600px;
        margin: 0 auto;
        display: flex;
        gap: 12px;
    }
    .search-input {
        flex: 1;
        padding: 16px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        color: #1A202C;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    .search-input:focus {
        outline: 3px solid rgba(255, 255, 255, 0.3);
    }
    .search-btn {
        background-color: #FFFFFF;
        color: #004AC6;
        border: none;
        padding: 16px 36px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: background-color 0.2s ease;
    }
    .search-btn:hover {
        background-color: #F1F5F9;
    }
</style>

<div class="hero-section">
    <h1>Temukan Lapangan Olahraga Terbaikmu</h1>
    <p>Cari, pilih, dan sewa lapangan olahraga bulu tangkis favoritmu secara instan.</p>
    <form action="search.php" method="GET" class="search-container">
        <input type="text" name="query" class="search-input" placeholder="Cari nama lapangan atau lokasi...">
        <button type="submit" class="search-btn">Cari</button>
    </form>
</div>

<?php
include 'includes/footer.php';
?>