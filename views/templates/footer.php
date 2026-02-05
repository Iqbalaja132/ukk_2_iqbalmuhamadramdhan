<!-- JS SIDEBAR & UTILS -->
<script>
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');

  function toggleSidebar() {
    sidebar.classList.toggle('active');
    sidebarOverlay.classList.toggle('hidden');
  }

  // Close sidebar when clicking overlay
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', toggleSidebar);
  }

  // Close sidebar on resize to desktop
  window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
      sidebar.classList.remove('active');
      sidebarOverlay.classList.add('hidden');
    }
  });
</script>

</body>
</html>
