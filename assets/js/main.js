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
            const targetUrl = this.getAttribute('href') || "auth/login.php";
            Swal.fire({
                icon: 'warning',
                title: 'Akses Dikunci',
                text: 'Anda wajib masuk/daftar akun terlebih dahulu untuk melanjutkan penyewaan jam lapangan.',
                confirmButtonColor: '#004AC6'
            }).then(() => {
                window.location.href = targetUrl;
            });
        });
    });
});