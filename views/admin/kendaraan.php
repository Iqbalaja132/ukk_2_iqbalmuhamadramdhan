<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil parameter dari URL
$search = $_GET['search'] ?? '';
$jenis_filter = $_GET['jenis_filter'] ?? '';
$current_page = $_GET['page'] ?? 1;
$limit = 10;

// Memanggil controller
include_once __DIR__ . '/../../controllers/c_kendaraan.php';

// Include header
include '../templates/header.php';
?>

<!-- Main Layout -->
<div class="flex min-h-screen">
  <!-- Include sidebar -->
  <?php include '../templates/sidebar.php'; ?>

  <!-- Main Content Area -->
  <div class="flex-1 md:ml-72 pt-2 transition-all duration-300">
    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-200/50">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center space-x-4">
          <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
            <i class="fas fa-bars text-xl text-gray-700"></i>
          </button>
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Kendaraan</h1>
            <p class="text-sm text-gray-600">Kelola dan pantau semua kendaraan terdaftar</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button onclick="openTambah()" class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center space-x-2 group">
            <i class="fas fa-plus-circle text-lg"></i>
            <span class="hidden sm:inline">Tambah Kendaraan</span>
            <i class="fas fa-chevron-right text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 transform group-hover:translate-x-1"></i>
          </button>
        </div>
      </div>
      
      <!-- Stats Bar -->
      <div class="px-6 pb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Kendaraan</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= !empty($total_data) ? $total_data : '0' ?></p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                <i class="fas fa-car text-blue-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Motor</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                  <?= isset($jumlah_motor) ? $jumlah_motor : '0' ?>
                </p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="fas fa-motorcycle text-green-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Mobil</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                  <?= isset($jumlah_mobil) ? $jumlah_mobil : '0' ?>
                </p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                <i class="fas fa-car text-purple-600 text-xl"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="p-6">
      <!-- Table Container -->
      <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden animate-fade-in">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Kendaraan</h2>
            <p class="text-sm text-gray-600 mt-1">Semua kendaraan terdaftar dalam sistem</p>
          </div>
          
          <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <!-- Search Form -->
            <form method="GET" action="" class="relative">
              <input type="text" name="search" placeholder="Cari kendaraan..." 
                     value="<?= htmlspecialchars($search) ?>"
                     class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition w-full sm:w-64">
              <input type="hidden" name="page" value="1">
              <?php if($jenis_filter): ?>
                <input type="hidden" name="jenis_filter" value="<?= htmlspecialchars($jenis_filter) ?>">
              <?php endif; ?>
              <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
            </form>
            
            <!-- Filter Dropdown -->
            <div class="relative">
              <form method="GET" action="" id="filterForm">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="page" value="1">
                <select name="jenis_filter" onchange="document.getElementById('filterForm').submit()" 
                        class="pl-10 pr-8 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none appearance-none bg-white cursor-pointer">
                  <option value="">Semua Jenis</option>
                  <option value="motor" <?= $jenis_filter == 'motor' ? 'selected' : '' ?>>Motor</option>
                  <option value="mobil" <?= $jenis_filter == 'mobil' ? 'selected' : '' ?>>Mobil</option>
                </select>
                <i class="fas fa-filter absolute left-4 top-4 text-gray-400"></i>
                <i class="fas fa-chevron-down absolute right-3 top-4 text-gray-400"></i>
              </form>
            </div>
            
            <!-- Reset Filter -->
            <?php if($search || $jenis_filter): ?>
            <a href="?page=1" class="p-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-gray-600 hover:text-gray-800">
              <i class="fas fa-redo"></i>
            </a>
            <?php endif; ?>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <span>No</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <span>Plat Nomor</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Jenis</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Warna</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pemilik</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Petugas</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php 
              if (!empty($data_kendaraan)) : 
                  $no = ($current_page - 1) * $limit + 1; 
                  foreach ($data_kendaraan as $row): 
              ?>
              <tr class="hover:bg-gray-50/50 transition-all duration-200 animate-slide-in" style="animation-delay: <?= ($no % 10) * 0.05 ?>s;">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900"><?= $no++ ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center mr-3">
                      <i class="fas fa-hashtag text-blue-600"></i>
                    </div>
                    <div>
                      <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($row->plat_nomor) ?></div>
                      <div class="text-xs text-gray-500">ID: <?= $row->id_kendaraan ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold <?= $row->jenis_kendaraan == 'motor' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' ?>">
                    <?php if($row->jenis_kendaraan == 'motor'): ?>
                      <i class="fas fa-motorcycle mr-1.5"></i>
                    <?php else: ?>
                      <i class="fas fa-car mr-1.5"></i>
                    <?php endif; ?>
                    <?= htmlspecialchars(ucfirst($row->jenis_kendaraan)) ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <span class="text-sm text-gray-900 font-medium"><?= htmlspecialchars(ucfirst($row->warna)) ?></span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 font-medium"><?= htmlspecialchars($row->pemilik) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white text-xs font-bold mr-2">
                      <?= strtoupper(substr($row->nama_lengkap, 0, 1)) ?>
                    </div>
                    <span class="text-sm text-gray-900"><?= htmlspecialchars(ucfirst(strtolower($row->nama_lengkap))) ?></span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button onclick="openEdit(
                        '<?= $row->id_kendaraan ?>',
                        '<?= $row->plat_nomor ?>',
                        '<?= $row->jenis_kendaraan ?>',
                        '<?= $row->warna ?>',
                        '<?= $row->pemilik ?>',
                        '<?= $row->id_user ?>'
                    )" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm">
                      <i class="fas fa-edit mr-1.5"></i> Edit
                    </button>
                    <form action="../../controllers/c_kendaraan.php?aksi=hapus" method="post" class="inline">
                      <input type="hidden" name="id_kendaraan" value="<?= $row->id_kendaraan ?>">
                      <button type="submit" onclick="return confirm('Yakin ingin menghapus data ini?')" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 text-red-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm">
                        <i class="fas fa-trash-alt mr-1.5"></i> Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php 
                  endforeach; 
              else : 
              ?>
              <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-4">
                      <i class="fas fa-car text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">
                      <?php if($search || $jenis_filter): ?>
                        Tidak ditemukan kendaraan dengan kriteria tersebut
                      <?php else: ?>
                        Belum ada data kendaraan
                      <?php endif; ?>
                    </h3>
                    <p class="text-gray-500 mb-4">
                      <?php if($search || $jenis_filter): ?>
                        Coba ubah kata kunci pencarian atau filter
                      <?php else: ?>
                        Mulai dengan menambahkan kendaraan pertama Anda
                      <?php endif; ?>
                    </p>
                    <?php if($search || $jenis_filter): ?>
                      <a href="?page=1" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-redo mr-2"></i> Reset Pencarian
                      </a>
                    <?php else: ?>
                      <button onclick="openTambah()" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Kendaraan Pertama
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Table Footer / Pagination -->
        <?php if (!empty($data_kendaraan) && $total_halaman > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
          <div class="text-sm text-gray-700 mb-4 sm:mb-0">
            <?php
              $start_data = (($current_page - 1) * $limit) + 1;
              $end_data = min($current_page * $limit, $total_data);
            ?>
            Menampilkan <span class="font-semibold"><?= $start_data ?>-<?= $end_data ?></span> dari <span class="font-semibold"><?= $total_data ?></span> hasil
          </div>
          <div class="flex items-center space-x-2">
            <!-- Previous Button -->
            <a href="?page=<?= $current_page > 1 ? $current_page - 1 : 1 ?>&search=<?= urlencode($search) ?>&jenis_filter=<?= urlencode($jenis_filter) ?>"
               class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition <?= $current_page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
              <i class="fas fa-chevron-left text-gray-600"></i>
            </a>
            
            <!-- Page Numbers -->
            <?php 
            // Tampilkan maksimal 5 halaman
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_halaman, $current_page + 2);
            
            // Jika di awal, tampilkan 5 halaman pertama
            if($start_page <= 2) {
              $start_page = 1;
              $end_page = min(5, $total_halaman);
            }
            
            // Jika di akhir, tampilkan 5 halaman terakhir
            if($end_page >= $total_halaman - 1) {
              $start_page = max(1, $total_halaman - 4);
              $end_page = $total_halaman;
            }
            
            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&jenis_filter=<?= urlencode($jenis_filter) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg border <?= $i == $current_page ? 'bg-primary-500 text-white border-primary-500' : 'border-gray-300 hover:bg-gray-50' ?> transition font-medium">
              <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <!-- Next Button -->
            <a href="?page=<?= $current_page < $total_halaman ? $current_page + 1 : $total_halaman ?>&search=<?= urlencode($search) ?>&jenis_filter=<?= urlencode($jenis_filter) ?>"
               class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition <?= $current_page >= $total_halaman ? 'opacity-50 cursor-not-allowed' : '' ?>">
              <i class="fas fa-chevron-right text-gray-600"></i>
            </a>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>

<!-- Modal Tambah -->
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 invisible" id="modalTambah">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform scale-95 transition-all duration-300">
    <div class="relative">
      <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white p-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold flex items-center">
              <i class="fas fa-car mr-3"></i> Tambah Kendaraan Baru
            </h3>
            <p class="text-primary-100 text-sm mt-1">Isi detail kendaraan di bawah ini</p>
          </div>
          <button onclick="closeModal('modalTambah')" class="text-white/80 hover:text-white text-xl transition">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <form action="../../controllers/c_kendaraan.php?aksi=tambah" method="post" class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Plat Nomor</label>
            <div class="relative">
              <input type="text" name="plat_nomor" placeholder="Contoh: B 1234 ABC" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-hashtag"></i>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kendaraan</label>
            <div class="grid grid-cols-2 gap-3">
              <label class="relative">
                <input type="radio" name="jenis_kendaraan" value="motor" class="hidden peer" required>
                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 transition-all duration-200">
                  <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-motorcycle text-xl text-gray-600 peer-checked:text-primary-600"></i>
                    <span class="font-medium peer-checked:text-primary-700">Motor</span>
                  </div>
                </div>
              </label>
              <label class="relative">
                <input type="radio" name="jenis_kendaraan" value="mobil" class="hidden peer">
                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 transition-all duration-200">
                  <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-car text-xl text-gray-600 peer-checked:text-primary-600"></i>
                    <span class="font-medium peer-checked:text-primary-700">Mobil</span>
                  </div>
                </div>
              </label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Warna Kendaraan</label>
            <div class="relative">
              <input type="text" name="warna" placeholder="Contoh: Merah, Hitam, Putih" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-palette"></i>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Pemilik</label>
            <div class="relative">
              <input type="text" name="pemilik" value="-" placeholder="Nama lengkap pemilik" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-user"></i>
              </div>
            </div>
          </div>
        </div>
        
        <input type="hidden" name="id_user" value="<?= $_SESSION['data']['id_user'] ?>">
        
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button type="button" onclick="closeModal('modalTambah')" 
                  class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold hover:shadow-sm">
            <i class="fas fa-times mr-2"></i> Batal
          </button>
          <button type="submit" 
                  class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-save mr-2"></i> Simpan Kendaraan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 invisible" id="modalEdit">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform scale-95 transition-all duration-300">
    <div class="relative">
      <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white p-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold flex items-center">
              <i class="fas fa-edit mr-3"></i> Edit Data Kendaraan
            </h3>
            <p class="text-primary-100 text-sm mt-1">Perbarui detail kendaraan di bawah ini</p>
          </div>
          <button onclick="closeModal('modalEdit')" class="text-white/80 hover:text-white text-xl transition">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <form action="../../controllers/c_kendaraan.php?aksi=update" method="post" class="p-6 space-y-6">
        <input type="hidden" name="id_kendaraan" id="editId">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Plat Nomor</label>
            <div class="relative">
              <input type="text" name="plat_nomor" id="editPlat" placeholder="Contoh: B 1234 ABC" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-hashtag"></i>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kendaraan</label>
            <div class="grid grid-cols-2 gap-3">
              <label class="relative">
                <input type="radio" name="jenis_kendaraan" id="editJenisMotor" value="motor" class="hidden peer">
                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 transition-all duration-200">
                  <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-motorcycle text-xl text-gray-600 peer-checked:text-primary-600"></i>
                    <span class="font-medium peer-checked:text-primary-700">Motor</span>
                  </div>
                </div>
              </label>
              <label class="relative">
                <input type="radio" name="jenis_kendaraan" id="editJenisMobil" value="mobil" class="hidden peer">
                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 transition-all duration-200">
                  <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-car text-xl text-gray-600 peer-checked:text-primary-600"></i>
                    <span class="font-medium peer-checked:text-primary-700">Mobil</span>
                  </div>
                </div>
              </label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Warna Kendaraan</label>
            <div class="relative">
              <input type="text" name="warna" id="editWarna" placeholder="Contoh: Merah, Hitam, Putih" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-palette"></i>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Pemilik</label>
            <div class="relative">
              <input type="text" name="pemilik" id="editPemilik" placeholder="Nama lengkap pemilik" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-user"></i>
              </div>
            </div>
          </div>
        </div>
        
        <input type="hidden" name="id_user" value="<?= $_SESSION['data']['id_user'] ?>">
        
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button type="button" onclick="closeModal('modalEdit')" 
                  class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold hover:shadow-sm">
            <i class="fas fa-times mr-2"></i> Batal
          </button>
          <button type="submit" 
                  class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-sync-alt mr-2"></i> Perbarui Data
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Sidebar functionality
let sidebarVisible = false;

function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  
  if (sidebarVisible) {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.remove('opacity-50', 'visible');
    overlay.classList.add('opacity-0', 'invisible');
  } else {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('opacity-0', 'invisible');
    overlay.classList.add('opacity-50', 'visible');
  }
  
  sidebarVisible = !sidebarVisible;
}

// Modal functionality
function openTambah() { 
  const modal = document.getElementById('modalTambah');
  modal.classList.remove('opacity-0', 'invisible', 'scale-95');
  modal.classList.add('opacity-100', 'visible', 'scale-100');
}

function openEdit(id, plat, jenis, warna, pemilik, userId) {
  document.getElementById('editId').value = id;
  document.getElementById('editPlat').value = plat;
  document.getElementById('editWarna').value = warna;
  document.getElementById('editPemilik').value = pemilik;
  
  if (jenis === 'motor') {
    document.getElementById('editJenisMotor').checked = true;
  } else {
    document.getElementById('editJenisMobil').checked = true;
  }
  
  const modal = document.getElementById('modalEdit');
  modal.classList.remove('opacity-0', 'invisible', 'scale-95');
  modal.classList.add('opacity-100', 'visible', 'scale-100');
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.classList.remove('opacity-100', 'visible', 'scale-100');
  modal.classList.add('opacity-0', 'invisible', 'scale-95');
}

// Auto submit search form when typing (with debounce)
let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
  searchInput.addEventListener('keyup', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      if (e.target.value.length >= 1 || e.target.value.length === 0) {
        this.form.submit();
      }
    }, 500);
  });
}

// Close modals with escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeModal('modalTambah');
    closeModal('modalEdit');
  }
});

// Close sidebar and modals when clicking overlay
document.getElementById('sidebarOverlay').addEventListener('click', toggleSidebar);

// Initialize animations on page load
document.addEventListener('DOMContentLoaded', () => {
  // Add animation classes to table rows
  const tableRows = document.querySelectorAll('tbody tr');
  tableRows.forEach((row, index) => {
    row.style.animationDelay = `${index * 0.05}s`;
  });
  
  // Auto-hide sidebar on mobile after clicking link
  if (window.innerWidth < 768) {
    document.querySelectorAll('.sidebar a').forEach(link => {
      link.addEventListener('click', () => {
        if (sidebarVisible) {
          toggleSidebar();
        }
      });
    });
  }
  
  // Auto-uppercase for plat nomor inputs
  const platInputs = document.querySelectorAll('input[name="plat_nomor"], input[id="editPlat"]');
  platInputs.forEach(input => {
    // Format saat mengetik
    input.addEventListener('input', function(e) {
      this.value = this.value.toUpperCase();
    });
    
    // Format saat paste
    input.addEventListener('paste', function(e) {
      setTimeout(() => {
        this.value = this.value.toUpperCase();
      }, 0);
    });
  });
  
  // Format plat nomor saat modal dibuka
  document.querySelector('input[name="plat_nomor"]')?.addEventListener('focus', function() {
    this.value = this.value.toUpperCase();
  });
  
  document.getElementById('editPlat')?.addEventListener('focus', function() {
    this.value = this.value.toUpperCase();
  });
});

// Fungsi untuk mengubah input ke uppercase
function toUpperCaseInput(input) {
  input.value = input.value.toUpperCase();
}
</script>

<?php include '../templates/footer.php'; ?>