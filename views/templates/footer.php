<!-- JS SIDEBAR -->
<script>
  const sidebar = document.getElementById('sidebar');
  const topbar = document.querySelector('.topbar');
  const content = document.querySelector('.content');
  const overlay = document.getElementById('sidebarOverlay');

  function toggleSidebar() {
    // DESKTOP
    sidebar.classList.toggle('closed');
    topbar.classList.toggle('full');
    content.classList.toggle('full');

    // MOBILE
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
  }

  overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
  });

  const menuToggle = document.querySelector('.fa-bars'); // Contoh jika pakai font-awesome
  const body = document.body;

  // Jika Anda menggunakan tombol untuk toggle sidebar, tambahkan event listener
  if (menuToggle) {
    menuToggle.addEventListener('click', function() {
      body.classList.toggle('sidebar-toggled');
    });
  }
</script>


</body>

</html>