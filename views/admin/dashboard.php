<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar.php'; ?>

<main class="md:ml-64 mt-16 p-6 bg-slate-50 min-h-screen">
  <!-- Page Header -->
  <div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">Dashboard Admin</h1>
    <p class="text-slate-600 mt-2">Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['data']['username']); ?>!</p>
  </div>

  <!-- Stats Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4" style="border-color: var(--primary);">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-slate-600 text-sm font-medium">Total Users</p>
          <p class="text-3xl font-bold text-slate-900 mt-2">0</p>
          <p class="text-slate-500 text-xs mt-2">Pengguna aktif</p>
        </div>
        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white text-xl" style="background: var(--primary); opacity: 0.1; color: var(--primary);">
          <i class="fas fa-users"></i>
        </div>
      </div>
    </div>

    <!-- Total Tarif -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-emerald-500">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-slate-600 text-sm font-medium">Total Tarif</p>
          <p class="text-3xl font-bold text-slate-900 mt-2">0</p>
          <p class="text-slate-500 text-xs mt-2">Paket tersedia</p>
        </div>
        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-emerald-600 text-xl opacity-20">
          <i class="fas fa-money-bill-wave"></i>
        </div>
      </div>
    </div>

    <!-- Total Area -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-amber-500">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-slate-600 text-sm font-medium">Total Area</p>
          <p class="text-3xl font-bold text-slate-900 mt-2">0</p>
          <p class="text-slate-500 text-xs mt-2">Area parkir</p>
        </div>
        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-amber-600 text-xl opacity-20">
          <i class="fas fa-map-marked-alt"></i>
        </div>
      </div>
    </div>

    <!-- Total Kendaraan -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-slate-600 text-sm font-medium">Total Kendaraan</p>
          <p class="text-3xl font-bold text-slate-900 mt-2">0</p>
          <p class="text-slate-500 text-xs mt-2">Kendaraan terdaftar</p>
        </div>
        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-blue-600 text-xl opacity-20">
          <i class="fas fa-car"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <a href="user.php" class="p-4 rounded-lg text-center no-underline transition-colors hover:bg-slate-50" style="border: 1px solid var(--primary); color: var(--primary);">
        <i class="fas fa-user-plus text-2xl block mb-2"></i>
        <span class="text-sm font-medium">Tambah User</span>
      </a>
      <a href="tarif.php" class="p-4 rounded-lg text-center no-underline transition-colors hover:bg-emerald-50" style="border: 1px solid #10b981; color: #10b981;">
        <i class="fas fa-plus text-2xl block mb-2"></i>
        <span class="text-sm font-medium">Tambah Tarif</span>
      </a>
      <a href="area_parkir.php" class="p-4 rounded-lg text-center no-underline transition-colors hover:bg-amber-50" style="border: 1px solid #f59e0b; color: #f59e0b;">
        <i class="fas fa-plus-square text-2xl block mb-2"></i>
        <span class="text-sm font-medium">Tambah Area</span>
      </a>
      <a href="kendaraan.php" class="p-4 rounded-lg text-center no-underline transition-colors hover:bg-blue-50" style="border: 1px solid #0ea5e9; color: #0ea5e9;">
        <i class="fas fa-plus-circle text-2xl block mb-2"></i>
        <span class="text-sm font-medium">Tambah Kendaraan</span>
      </a>
    </div>
  </div>
</main>

<?php include '../templates/footer.php'; ?>
