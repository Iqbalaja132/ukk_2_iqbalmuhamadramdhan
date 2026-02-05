<?php
$role = $_SESSION['data']['role'];
$current = basename($_SERVER['PHP_SELF']);
?>

<div class="fixed inset-0 bg-black/50 hidden transition-opacity z-30" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="fixed left-0 top-0 w-64 h-screen pt-16 overflow-y-auto transition-transform -translate-x-full md:translate-x-0 z-40" id="sidebar" style="background: var(--bg-sidebar); color: var(--text-light);">
  
  <div class="px-6 py-8">
    <a href="#" class="flex items-center gap-3 no-underline">
      <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-lg" style="background: var(--primary);">
        <i class="fas fa-parking"></i>
      </div>
      <span class="text-lg font-bold">ParkSmart</span>
    </a>
  </div>

  <nav class="px-4 space-y-2">
    <!-- Main Menu Label -->
    <div class="px-4 py-3 text-xs font-semibold uppercase tracking-wider opacity-60">Menu Utama</div>

    <a href="dashboard.php" class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-lg no-underline transition-colors <?= $current == 'dashboard.php' ? 'active' : '' ?>" style="color: var(--text-light);">
      <i class="fas fa-th-large w-5"></i>
      <span class="font-medium">Home</span>
    </a>

    <?php if ($role === 'admin'): ?>
      <!-- Management Label -->
      <div class="px-4 py-3 mt-6 text-xs font-semibold uppercase tracking-wider opacity-60">Manajemen</div>

      <a href="user.php" class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-lg no-underline transition-colors <?= $current == 'user.php' ? 'active' : '' ?>" style="color: var(--text-light);">
        <i class="fas fa-users w-5"></i>
        <span class="font-medium">User</span>
      </a>

      <a href="tarif.php" class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-lg no-underline transition-colors <?= $current == 'tarif.php' ? 'active' : '' ?>" style="color: var(--text-light);">
        <i class="fas fa-money-bill-wave w-5"></i>
        <span class="font-medium">Tarif</span>
      </a>

      <a href="area_parkir.php" class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-lg no-underline transition-colors <?= $current == 'area_parkir.php' ? 'active' : '' ?>" style="color: var(--text-light);">
        <i class="fas fa-map-marked-alt w-5"></i>
        <span class="font-medium">Area</span>
      </a>

      <a href="kendaraan.php" class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-lg no-underline transition-colors <?= $current == 'kendaraan.php' ? 'active' : '' ?>" style="color: var(--text-light);">
        <i class="fas fa-car w-5"></i>
        <span class="font-medium">Kendaraan</span>
      </a>
    <?php endif; ?>

    <?php if ($role === 'staff'): ?>
      <!-- Operational Label -->
      <div class="px-4 py-3 mt-6 text-xs font-semibold uppercase tracking-wider opacity-60">Operasional</div>

      <a href="struk.php" class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-lg no-underline transition-colors <?= $current == 'struk.php' ? 'active' : '' ?>" style="color: var(--text-light);">
        <i class="fas fa-receipt w-5"></i>
        <span class="font-medium">Struk</span>
      </a>

      <a href="transaksi.php" class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-lg no-underline transition-colors <?= $current == 'transaksi.php' ? 'active' : '' ?>" style="color: var(--text-light);">
        <i class="fas fa-exchange-alt w-5"></i>
        <span class="font-medium">Transaksi</span>
      </a>
    <?php endif; ?>

    <?php if ($role === 'owner'): ?>
      <!-- Report Label -->
      <div class="px-4 py-3 mt-6 text-xs font-semibold uppercase tracking-wider opacity-60">Laporan</div>

      <a href="rekap.php" class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-lg no-underline transition-colors <?= $current == 'rekap.php' ? 'active' : '' ?>" style="color: var(--text-light);">
        <i class="fas fa-file-invoice-dollar w-5"></i>
        <span class="font-medium">Rekap</span>
      </a>
    <?php endif; ?>
  </nav>

  <!-- Footer -->
  <div class="absolute bottom-0 left-0 right-0 p-4 border-t" style="border-color: rgba(255,255,255,.1);">
    <a href="../../controllers/c_login.php?aksi=logout" class="flex items-center gap-2 px-4 py-2 rounded-lg text-red-400 no-underline transition-colors hover:bg-red-500/10" onclick="return confirm('Yakin ingin logout?')">
      <i class="fas fa-sign-out-alt"></i>
      <span class="font-medium">Logout</span>
    </a>
  </div>
</aside>

<style>
  .sidebar-menu-link.active {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  }

  .sidebar-menu-link:hover:not(.active) {
    background: var(--bg-sidebar-hover) !important;
  }

  #sidebar {
    @media (max-width: 768px) {
      transform: translateX(-100%);
    }
  }

  #sidebar.active {
    transform: translateX(0) !important;
  }
</style>
