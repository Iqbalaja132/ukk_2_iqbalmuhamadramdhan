<?php
if (!isset($_SESSION['data'])) {
  header("Location: ../login.php");
  exit;
}

$role = $_SESSION['data']['role'];
$current = basename($_SERVER['PHP_SELF']);
$userName = $_SESSION['data']['nama_lengkap'] ?? 'User';
$userInitial = strtoupper(substr($userName, 0, 1));
?>

<!-- Sidebar Overlay -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden transition-opacity duration-300 opacity-0 invisible" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="fixed top-0 left-0 h-full w-72 gradient-sidebar text-white z-50 transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out shadow-2xl" id="sidebar">
  <!-- Sidebar Header -->
  <div class="p-6 border-b border-white/10">
    <div class="flex items-center space-x-4">
      <div class="relative">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg">
          <i class="fas fa-parking text-xl text-white"></i>
        </div>
        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-dark-800"></div>
      </div>
      <div>
        <h1 class="text-xl font-bold tracking-tight">Park<span class="text-primary-400">Smart</span></h1>
        <p class="text-xs text-gray-400 mt-1">Sistem Parkir Premium</p>
      </div>
    </div>
  </div>

  <!-- Navigation -->
  <div class="flex-1 overflow-y-auto py-4 scrollbar-thin">
    <div class="px-4 space-y-1">
      <!-- Dashboard -->
      <div class="mb-4">
        <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold px-3 mb-2">Menu Utama</p>
        <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $current == 'dashboard.php' ? 'bg-primary-500/20 text-primary-300 border-l-4 border-primary-400' : 'hover:bg-white/5 text-gray-300 hover:text-white' ?>">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $current == 'dashboard.php' ? 'bg-primary-500/20' : 'bg-white/5' ?>">
            <i class="fas fa-home text-sm"></i>
          </div>
          <span class="font-medium">Dashboard</span>
          <?php if ($current == 'dashboard.php'): ?>
            <span class="ml-auto w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
          <?php endif; ?>
        </a>
      </div>

      <?php if ($role === 'admin'): ?>
        <!-- Admin Menu -->
        <div class="mb-4">
          <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold px-3 mb-2">Manajemen</p>

          <div class="space-y-2">
            <a href="user.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $current == 'user.php' ? 'bg-primary-500/20 text-primary-300 border-l-4 border-primary-400' : 'hover:bg-white/5 text-gray-300 hover:text-white' ?>">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $current == 'user.php' ? 'bg-primary-500/20' : 'bg-white/5' ?>">
                <i class="fas fa-users text-sm"></i>
              </div>
              <span class="font-medium">Pengguna</span>
              <?php if ($current == 'user.php'): ?>
                <span class="ml-auto w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
              <?php endif; ?>
            </a>

            <a href="tarif.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $current == 'tarif.php' ? 'bg-primary-500/20 text-primary-300 border-l-4 border-primary-400' : 'hover:bg-white/5 text-gray-300 hover:text-white' ?>">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $current == 'tarif.php' ? 'bg-primary-500/20' : 'bg-white/5' ?>">
                <i class="fas fa-money-bill-wave text-sm"></i>
              </div>
              <span class="font-medium">Tarif Parkir</span>
              <?php if ($current == 'tarif.php'): ?>
                <span class="ml-auto w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
              <?php endif; ?>
            </a>

            <a href="area_parkir.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $current == 'area_parkir.php' ? 'bg-primary-500/20 text-primary-300 border-l-4 border-primary-400' : 'hover:bg-white/5 text-gray-300 hover:text-white' ?>">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $current == 'area_parkir.php' ? 'bg-primary-500/20' : 'bg-white/5' ?>">
                <i class="fas fa-map-marked-alt text-sm"></i>
              </div>
              <span class="font-medium">Area Parkir</span>
              <?php if ($current == 'area_parkir.php'): ?>
                <span class="ml-auto w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
              <?php endif; ?>
            </a>

            <a href="kendaraan.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $current == 'kendaraan.php' ? 'bg-primary-500/20 text-primary-300 border-l-4 border-primary-400' : 'hover:bg-white/5 text-gray-300 hover:text-white' ?>">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $current == 'kendaraan.php' ? 'bg-primary-500/20' : 'bg-white/5' ?>">
                <i class="fas fa-car text-sm"></i>
              </div>
              <span class="font-medium">Kendaraan</span>
              <?php if ($current == 'kendaraan.php'): ?>
                <span class="ml-auto w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
              <?php endif; ?>
            </a>

            <a href="log_aktivitas.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $current == 'log_aktivitas.php' ? 'bg-primary-500/20 text-primary-300 border-l-4 border-primary-400' : 'hover:bg-white/5 text-gray-300 hover:text-white' ?>">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $current == 'log_aktivitas.php' ? 'bg-primary-500/20' : 'bg-white/5' ?>">
                <i class="fas fa-history text-sm"></i>
              </div>
              <span class="font-medium">Log Aktivitas</span>
              <?php if ($current == 'log_aktivitas.php'): ?>
                <span class="ml-auto w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
              <?php endif; ?>
            </a>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($role === 'staff'): ?>
        <!-- Staff Menu -->
        <div class="mb-4">
          <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold px-3 mb-2">Operasional</p>

          <a href="struk.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $current == 'struk.php' ? 'bg-primary-500/20 text-primary-300 border-l-4 border-primary-400' : 'hover:bg-white/5 text-gray-300 hover:text-white' ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $current == 'struk.php' ? 'bg-primary-500/20' : 'bg-white/5' ?>">
              <i class="fas fa-receipt text-sm"></i>
            </div>
            <span class="font-medium">Cetak Struk</span>
          </a>

          <a href="transaksi.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $current == 'transaksi.php' ? 'bg-primary-500/20 text-primary-300 border-l-4 border-primary-400' : 'hover:bg-white/5 text-gray-300 hover:text-white' ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $current == 'transaksi.php' ? 'bg-primary-500/20' : 'bg-white/5' ?>">
              <i class="fas fa-exchange-alt text-sm"></i>
            </div>
            <span class="font-medium">Transaksi</span>
          </a>
        </div>
      <?php endif; ?>

      <?php if ($role === 'owner'): ?>
        <!-- Owner Menu -->
        <div class="mb-4">
          <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold px-3 mb-2">Analitik</p>

          <a href="rekap.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $current == 'rekap.php' ? 'bg-primary-500/20 text-primary-300 border-l-4 border-primary-400' : 'hover:bg-white/5 text-gray-300 hover:text-white' ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?= $current == 'rekap.php' ? 'bg-primary-500/20' : 'bg-white/5' ?>">
              <i class="fas fa-file-invoice-dollar text-sm"></i>
            </div>
            <span class="font-medium">Laporan</span>
            <span class="ml-auto text-xs bg-primary-500 text-white px-2 py-1 rounded-full">New</span>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Logout Button -->
  <div class="p-6 border-t border-white/10">
    <a href="../../controllers/c_login.php?aksi=logout" onclick="return confirm('Apakah Anda yakin ingin logout?')"
      class="flex items-center justify-center space-x-2 px-4 py-3 bg-gradient-to-r from-red-500/20 to-red-600/20 hover:from-red-500/30 hover:to-red-600/30 text-red-300 hover:text-white rounded-xl transition-all duration-200 group">
      <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-500/20 group-hover:bg-red-500/30">
        <i class="fas fa-sign-out-alt text-sm"></i>
      </div>
      <span class="font-medium">Logout</span>
    </a>
  </div>
</aside>