</main>
        </div> 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                });
            }
            
            // Opsional: Menutup sidebar saat area konten di-klik (di mobile)
            const mainContent = document.getElementById('mainContent');
            if(mainContent && sidebar) {
                mainContent.addEventListener('click', function() {
                    // Hanya tutup jika sidebar sedang 'open' dan layar kecil (tombol toggle terlihat)
                    if (sidebar.classList.contains('open') && sidebarToggle.offsetParent !== null) {
                        sidebar.classList.remove('open');
                    }
                });
            }
        });
    </script>
</body>
</html>