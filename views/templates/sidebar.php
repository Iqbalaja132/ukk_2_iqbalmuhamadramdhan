<?php
$role = $_SESSION['data']['role'];
$current = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <a href="#" class="sidebar-logo">
      <div class="sidebar-logo-icon">
        <i class="fas fa-parking"></i>
      </div>
      <span class="sidebar-logo-text">ParkSmart</span>
    </a>
  </div>

  <ul class="sidebar-menu">
    <li class="sidebar-menu-label">Menu Utama</li>

    <li>
      <a href="dashboard.php"
         class="sidebar-menu-link <?= $current == 'dashboard.php' ? 'active' : '' ?>">
        <i class="fas fa-th-large"></i> Home
      </a>
    </li>

    <?php if ($role === 'admin'): ?>
      <li class="sidebar-menu-label">Manajemen</li>

      <li>
        <a href="user.php"
           class="sidebar-menu-link <?= $current == 'user.php' ? 'active' : '' ?>">
          <i class="fas fa-users"></i> User
        </a>
      </li>

      <li>
        <a href="tarif.php"
           class="sidebar-menu-link <?= $current == 'tarif.php' ? 'active' : '' ?>">
          <i class="fas fa-money-bill-wave"></i> Tarif
        </a>
      </li>

      <li>
        <a href="area_parkir.php"
           class="sidebar-menu-link <?= $current == 'area_parkir.php' ? 'active' : '' ?>">
          <i class="fas fa-map-marked-alt"></i> Area
        </a>
      </li>

      <li>
        <a href="kendaraan.php"
           class="sidebar-menu-link <?= $current == 'kendaraan.php' ? 'active' : '' ?>">
          <i class="fas fa-car"></i> Kendaraan
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role === 'staff'): ?>
      <li class="sidebar-menu-label">Operasional</li>

      <li>
        <a href="struk.php"
           class="sidebar-menu-link <?= $current == 'struk.php' ? 'active' : '' ?>">
          <i class="fas fa-receipt"></i> Struk
        </a>
      </li>

      <li>
        <a href="transaksi.php"
           class="sidebar-menu-link <?= $current == 'transaksi.php' ? 'active' : '' ?>">
          <i class="fas fa-exchange-alt"></i> Transaksi
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role === 'owner'): ?>
      <li class="sidebar-menu-label">Laporan</li>

      <li>
        <a href="rekap.php"
           class="sidebar-menu-link <?= $current == 'rekap.php' ? 'active' : '' ?>">
          <i class="fas fa-file-invoice-dollar"></i> Rekap
        </a>
      </li>
    <?php endif; ?>
  </ul>

  <div class="sidebar-footer">
    <a href="../../controllers/c_login.php?aksi=logout" class="logout-btn" onclick="return confirm('Yakin hapus data?')">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>
</aside>
