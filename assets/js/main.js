document.addEventListener("DOMContentLoaded", function() {
    console.log("Sistem Logika JavaScript ArenaGO Siap Digunakan.");

    const filterInputs = document.querySelectorAll('.filter-sidebar input[type="radio"], .filter-sidebar input[type="checkbox"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            const form = this.closest('form');
            if(form) {
                form.submit();
            }
        });
    });

    const btnPesanBiasa = document.querySelectorAll(".btn-intercept-login");
    btnPesanBiasa.forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            alert("Akses Dikunci! Anda wajib masuk/daftar akun terlebih dahulu untuk melanjutkan penyewaan jam lapangan.");
            window.location.href = "auth/login.php";
        });
    });
});