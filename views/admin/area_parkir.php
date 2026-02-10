<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';
$current_page = $_GET['page'] ?? 1;
$limit = 10;

include_once __DIR__ . '/../../controllers/c_areaparkir.php';

include '../templates/header.php';
?>

<div class="flex min-h-screen">
  <?php include '../templates/sidebar.php'; ?>
  <div class="flex-1 md:ml-72 pt-2 transition-all duration-300">
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-200/50">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center space-x-4">
          <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
            <i class="fas fa-bars text-xl text-gray-700"></i>
          </button>
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Area Parkir</h1>
            <p class="text-sm text-gray-600">Kelola dan pantau ketersediaan area parkir</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button onclick="openTambah()" class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center space-x-2 group">
            <i class="fas fa-plus-circle text-lg"></i>
            <span class="hidden sm:inline">Tambah Area</span>
            <i class="fas fa-chevron-right text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 transform group-hover:translate-x-1"></i>
          </button>
        </div>
      </div>
      
      <div class="px-6 pb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Area</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= !empty($total_data) ? $total_data : '0' ?></p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                <i class="fas fa-parking text-blue-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Kapasitas</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                  <?= !empty($total_kapasitas) ? number_format($total_kapasitas, 0, ',', '.') : '0' ?>
                </p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="fas fa-car text-green-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-4 border border-yellow-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Terisi</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                  <?= !empty($total_terisi) ? number_format($total_terisi, 0, ',', '.') : '0' ?>
                </p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                <i class="fas fa-motorcycle text-yellow-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Tersedia</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                  <?= !empty($total_kapasitas) && !empty($total_terisi) ? 
                        number_format($total_kapasitas - $total_terisi, 0, ',', '.') : '0' ?>
                </p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                <i class="fas fa-check-circle text-purple-600 text-xl"></i>
              </div>
            </div>
          </div>
        </div>
        
        <?php if (!empty($total_kapasitas)): ?>
        <div class="mt-4 bg-white rounded-xl p-4 border border-gray-200">
          <div class="flex items-center justify-between mb-2">
            <div class="text-sm font-medium text-gray-700">Tingkat Pengisian Parkir</div>
            <div class="text-sm font-semibold <?= $persentase_terisi >= 80 ? 'text-red-600' : 'text-green-600' ?>">
              <?= $persentase_terisi ?>%
            </div>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="h-2.5 rounded-full <?= $persentase_terisi >= 80 ? 'bg-red-500' : ($persentase_terisi >= 60 ? 'bg-yellow-500' : 'bg-green-500') ?>" 
                 style="width: <?= min($persentase_terisi, 100) ?>%"></div>
          </div>
          <div class="flex justify-between text-xs text-gray-500 mt-1">
            <span><?= !empty($total_terisi) ? number_format($total_terisi, 0, ',', '.') : '0' ?> terisi</span>
            <span><?= !empty($total_kapasitas) ? number_format($total_kapasitas, 0, ',', '.') : '0' ?> kapasitas</span>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </header>

    <main class="p-6">
      <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden animate-fade-in">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Area Parkir</h2>
            <p class="text-sm text-gray-600 mt-1">Semua area parkir dalam sistem</p>
          </div>
          
          <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <form method="GET" action="" class="relative">
              <input type="text" name="search" placeholder="Cari area parkir..." 
                     value="<?= htmlspecialchars($search) ?>"
                     class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition w-full sm:w-64">
              <input type="hidden" name="page" value="1">
              <?php if($status_filter): ?>
                <input type="hidden" name="status_filter" value="<?= htmlspecialchars($status_filter) ?>">
              <?php endif; ?>
              <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
            </form>
            
            <div class="relative">
              <form method="GET" action="" id="filterForm">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="page" value="1">
                <select name="status_filter" onchange="document.getElementById('filterForm').submit()" 
                        class="pl-10 pr-8 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none appearance-none bg-white cursor-pointer">
                  <option value="">Semua Status</option>
                  <option value="tersedia" <?= $status_filter == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                  <option value="penuh" <?= $status_filter == 'penuh' ? 'selected' : '' ?>>Penuh</option>
                </select>
                <i class="fas fa-filter absolute left-4 top-4 text-gray-400"></i>
                <i class="fas fa-chevron-down absolute right-3 top-4 text-gray-400"></i>
              </form>
            </div>
            
            <?php if($search || $status_filter): ?>
            <a href="?page=1" class="p-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-gray-600 hover:text-gray-800">
              <i class="fas fa-redo"></i>
            </a>
            <?php endif; ?>
          </div>
        </div>

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
                  Nama Area
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Kapasitas & Terisi
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php 
              if (!empty($data_area)) : 
                  $no = ($current_page - 1) * $limit + 1; 
                  foreach ($data_area as $row): 
              ?>
              <tr class="hover:bg-gray-50/50 transition-all duration-200 animate-slide-in" style="animation-delay: <?= ($no % 10) * 0.05 ?>s;">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900"><?= $no++ ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center mr-3">
                      <i class="fas fa-parking text-blue-600"></i>
                    </div>
                    <div>
                      <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($row->nama_area) ?></div>
                      <div class="text-xs text-gray-500">ID: AREA-<?= str_pad($row->id_area, 3, '0', STR_PAD_LEFT) ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Kapasitas:</span>
                      <span class="font-semibold text-gray-900"><?= number_format($row->kapasitas, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Terisi:</span>
                      <span class="font-semibold text-gray-900"><?= number_format($row->terisi, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Tersisa:</span>
                      <span class="font-semibold <?= $row->tersisa > 0 ? 'text-green-600' : 'text-red-600' ?>">
                        <?= number_format($row->tersisa, 0, ',', '.') ?>
                      </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                      <div class="h-1.5 rounded-full 
                        <?= $row->persentase >= 80 ? 'bg-red-500' : ($row->persentase >= 60 ? 'bg-yellow-500' : 'bg-green-500') ?>" 
                           style="width: <?= min($row->persentase, 100) ?>%">
                      </div>
                    </div>
                    <div class="text-xs text-gray-500 text-right"><?= $row->persentase ?>% terisi</div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold 
                    <?= $row->status == 'Penuh' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
                    <?php if($row->status == 'Penuh'): ?>
                      <i class="fas fa-times-circle mr-1.5"></i>
                    <?php else: ?>
                      <i class="fas fa-check-circle mr-1.5"></i>
                    <?php endif; ?>
                    <?= $row->status ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button onclick="openEdit(
                        '<?= $row->id_area ?>',
                        '<?= htmlspecialchars($row->nama_area) ?>',
                        '<?= $row->kapasitas ?>',
                        '<?= $row->terisi ?>'
                    )" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm">
                      <i class="fas fa-edit mr-1.5"></i> Edit
                    </button>
                    <form action="../../controllers/c_areaparkir.php?aksi=hapus" method="post" class="inline">
                      <input type="hidden" name="id_area" value="<?= $row->id_area ?>">
                      <button type="submit" onclick="return confirm('Yakin ingin menghapus area parkir ini?')" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 text-red-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm">
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
                <td colspan="5" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-4">
                      <i class="fas fa-parking text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">
                      <?php if($search || $status_filter): ?>
                        Tidak ditemukan area parkir dengan kriteria tersebut
                      <?php else: ?>
                        Belum ada data area parkir
                      <?php endif; ?>
                    </h3>
                    <p class="text-gray-500 mb-4">
                      <?php if($search || $status_filter): ?>
                        Coba ubah kata kunci pencarian atau filter
                      <?php else: ?>
                        Mulai dengan menambahkan area parkir pertama Anda
                      <?php endif; ?>
                    </p>
                    <?php if($search || $status_filter): ?>
                      <a href="?page=1" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-redo mr-2"></i> Reset Pencarian
                      </a>
                    <?php else: ?>
                      <button onclick="openTambah()" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Area Parkir Pertama
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if (!empty($data_area) && $total_halaman > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
          <div class="text-sm text-gray-700 mb-4 sm:mb-0">
            <?php
              $start_data = (($current_page - 1) * $limit) + 1;
              $end_data = min($current_page * $limit, $total_data);
            ?>
            Menampilkan <span class="font-semibold"><?= $start_data ?>-<?= $end_data ?></span> dari <span class="font-semibold"><?= $total_data ?></span> hasil
          </div>
          <div class="flex items-center space-x-2">
            <a href="?page=<?= $current_page > 1 ? $current_page - 1 : 1 ?>&search=<?= urlencode($search) ?>&status_filter=<?= urlencode($status_filter) ?>"
               class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition <?= $current_page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
              <i class="fas fa-chevron-left text-gray-600"></i>
            </a>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_halaman, $current_page + 2);
            if($start_page <= 2) {
              $start_page = 1;
              $end_page = min(5, $total_halaman);
            }
            if($end_page >= $total_halaman - 1) {
              $start_page = max(1, $total_halaman - 4);
              $end_page = $total_halaman;
            }
            
            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status_filter=<?= urlencode($status_filter) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg border <?= $i == $current_page ? 'bg-primary-500 text-white border-primary-500' : 'border-gray-300 hover:bg-gray-50' ?> transition font-medium">
              <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <a href="?page=<?= $current_page < $total_halaman ? $current_page + 1 : $total_halaman ?>&search=<?= urlencode($search) ?>&status_filter=<?= urlencode($status_filter) ?>"
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

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 invisible" id="modalTambah">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300">
    <div class="relative">
      <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white p-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold flex items-center">
              <i class="fas fa-parking mr-3"></i> Tambah Area Parkir Baru
            </h3>
            <p class="text-primary-100 text-sm mt-1">Isi detail area parkir di bawah ini</p>
          </div>
          <button onclick="closeModal('modalTambah')" class="text-white/80 hover:text-white text-xl transition">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <form action="../../controllers/c_areaparkir.php?aksi=tambah" method="post" class="p-6 space-y-6" onsubmit="cleanNumberInputs(this)">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Area Parkir</label>
          <div class="relative">
            <input type="text" name="nama_area" placeholder="Contoh: Gedung A23, Area B, Blok C" required 
                  class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
            <div class="absolute left-3 top-3.5 text-gray-400">
              <i class="fas fa-signature"></i>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Kapasitas Maksimal</label>
          <div class="relative">
            <input type="number" name="kapasitas" id="kapasitasInput" placeholder="Contoh: 1500" required min="1" 
                  class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50" 
                  oninput="updateProgress()">
            <div class="absolute left-3 top-3.5 text-gray-400">
              <i class="fas fa-car"></i>
            </div>
            <div class="absolute right-3 top-3.5 text-gray-500 text-sm">
              slot
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Terisi Saat Ini</label>
          <div class="relative">
            <input type="number" name="terisi" id="terisiInput" placeholder="Contoh: 500" required min="0" 
                  class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50" 
                  oninput="updateProgress()">
            <div class="absolute left-3 top-3.5 text-gray-400">
              <i class="fas fa-motorcycle"></i>
            </div>
            <div class="absolute right-3 top-3.5 text-gray-500 text-sm">
              slot
            </div>
          </div>
          <div class="mt-2">
            <div class="flex justify-between text-xs text-gray-600 mb-1">
              <span>Tersisa:</span>
              <span id="tersisaText" class="font-semibold text-green-600">0 slot</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div id="progressBar" class="h-2 rounded-full bg-green-500" style="width: 0%"></div>
            </div>
            <div id="errorText" class="text-xs text-red-600 mt-1 hidden">
              ⚠️ Jumlah terisi tidak boleh melebihi kapasitas!
            </div>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button type="button" onclick="closeModal('modalTambah')" 
                  class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold hover:shadow-sm">
            <i class="fas fa-times mr-2"></i> Batal
          </button>
          <button type="submit" id="submitBtn"
                  class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-save mr-2"></i> Simpan Area
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 invisible" id="modalEdit">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300">
    <div class="relative">
      <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white p-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold flex items-center">
              <i class="fas fa-edit mr-3"></i> Edit Area Parkir
            </h3>
            <p class="text-primary-100 text-sm mt-1">Perbarui detail area parkir di bawah ini</p>
          </div>
          <button onclick="closeModal('modalEdit')" class="text-white/80 hover:text-white text-xl transition">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <form action="../../controllers/c_areaparkir.php?aksi=update" method="post" class="p-6 space-y-6" onsubmit="cleanNumberInputs(this)">
        <input type="hidden" name="id_area" id="editId">
        
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Area Parkir</label>
          <div class="relative">
            <input type="text" name="nama_area" id="editNama" placeholder="Contoh: Gedung A23, Area B, Blok C" required 
                  class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
            <div class="absolute left-3 top-3.5 text-gray-400">
              <i class="fas fa-signature"></i>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Kapasitas Maksimal</label>
          <div class="relative">
            <input type="number" name="kapasitas" id="editKapasitas" placeholder="Contoh: 1500" required min="1" 
                  class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50" 
                  oninput="updateEditProgress()">
            <div class="absolute left-3 top-3.5 text-gray-400">
              <i class="fas fa-car"></i>
            </div>
            <div class="absolute right-3 top-3.5 text-gray-500 text-sm">
              slot
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Terisi Saat Ini</label>
          <div class="relative">
            <input type="number" name="terisi" id="editTerisi" placeholder="Contoh: 500" required min="0" 
                  class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50" 
                  oninput="updateEditProgress()">
            <div class="absolute left-3 top-3.5 text-gray-400">
              <i class="fas fa-motorcycle"></i>
            </div>
            <div class="absolute right-3 top-3.5 text-gray-500 text-sm">
              slot
            </div>
          </div>
          <div class="mt-2">
            <div class="flex justify-between text-xs text-gray-600 mb-1">
              <span>Tersisa:</span>
              <span id="editTersisaText" class="font-semibold text-green-600">0 slot</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div id="editProgressBar" class="h-2 rounded-full bg-green-500" style="width: 0%"></div>
            </div>
            <div id="editErrorText" class="text-xs text-red-600 mt-1 hidden">
              ⚠️ Jumlah terisi tidak boleh melebihi kapasitas!
            </div>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button type="button" onclick="closeModal('modalEdit')" 
                  class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold hover:shadow-sm">
            <i class="fas fa-times mr-2"></i> Batal
          </button>
          <button type="submit" id="editSubmitBtn"
                  class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-sync-alt mr-2"></i> Perbarui Area
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
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

function openTambah() { 
  const modal = document.getElementById('modalTambah');
  modal.classList.remove('opacity-0', 'invisible', 'scale-95');
  modal.classList.add('opacity-100', 'visible', 'scale-100');
  
  document.querySelector('#modalTambah form').reset();
  updateProgress();
}

function openEdit(id, nama, kapasitas, terisi) {
  document.getElementById('editId').value = id;
  document.getElementById('editNama').value = nama;
  document.getElementById('editKapasitas').value = kapasitas;
  document.getElementById('editTerisi').value = terisi;
  
  updateEditProgress();
  
  const modal = document.getElementById('modalEdit');
  modal.classList.remove('opacity-0', 'invisible', 'scale-95');
  modal.classList.add('opacity-100', 'visible', 'scale-100');
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.classList.remove('opacity-100', 'visible', 'scale-100');
  modal.classList.add('opacity-0', 'invisible', 'scale-95');
}

function updateProgress() {
  const kapasitas = parseInt(document.getElementById('kapasitasInput').value) || 0;
  const terisi = parseInt(document.getElementById('terisiInput').value) || 0;
  const tersisa = kapasitas - terisi;
  
  document.getElementById('tersisaText').textContent = tersisa + ' slot';
  document.getElementById('tersisaText').className = `font-semibold ${tersisa >= 0 ? 'text-green-600' : 'text-red-600'}`;
  
  const persentase = kapasitas > 0 ? Math.min((terisi / kapasitas) * 100, 100) : 0;
  const progressBar = document.getElementById('progressBar');
  progressBar.style.width = persentase + '%';
  progressBar.className = `h-2 rounded-full ${
    persentase >= 80 ? 'bg-red-500' : 
    persentase >= 60 ? 'bg-yellow-500' : 
    'bg-green-500'
  }`;
  
  const errorText = document.getElementById('errorText');
  const submitBtn = document.getElementById('submitBtn');
  
  if (terisi > kapasitas) {
    errorText.classList.remove('hidden');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
  } else {
    errorText.classList.add('hidden');
    submitBtn.disabled = false;
    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
  }
}

function updateEditProgress() {
  const kapasitas = parseInt(document.getElementById('editKapasitas').value) || 0;
  const terisi = parseInt(document.getElementById('editTerisi').value) || 0;
  const tersisa = kapasitas - terisi;
  
  document.getElementById('editTersisaText').textContent = tersisa + ' slot';
  document.getElementById('editTersisaText').className = `font-semibold ${tersisa >= 0 ? 'text-green-600' : 'text-red-600'}`;
  
  const persentase = kapasitas > 0 ? Math.min((terisi / kapasitas) * 100, 100) : 0;
  const progressBar = document.getElementById('editProgressBar');
  progressBar.style.width = persentase + '%';
  progressBar.className = `h-2 rounded-full ${
    persentase >= 80 ? 'bg-red-500' : 
    persentase >= 60 ? 'bg-yellow-500' : 
    'bg-green-500'
  }`;
  
  const errorText = document.getElementById('editErrorText');
  const submitBtn = document.getElementById('editSubmitBtn');
  
  if (terisi > kapasitas) {
    errorText.classList.remove('hidden');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
  } else {
    errorText.classList.add('hidden');
    submitBtn.disabled = false;
    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
  }
}

function cleanNumberInputs(form) {
  const numberInputs = form.querySelectorAll('input[type="number"]');
  numberInputs.forEach(input => {
    input.value = input.value.replace(/[^\d]/g, '');
  });
  return true;
}

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

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeModal('modalTambah');
    closeModal('modalEdit');
  }
});

document.getElementById('sidebarOverlay').addEventListener('click', toggleSidebar);

document.addEventListener('DOMContentLoaded', () => {
  const tableRows = document.querySelectorAll('tbody tr');
  tableRows.forEach((row, index) => {
    row.style.animationDelay = `${index * 0.05}s`;
  });
  
  if (window.innerWidth < 768) {
    document.querySelectorAll('.sidebar a').forEach(link => {
      link.addEventListener('click', () => {
        if (sidebarVisible) {
          toggleSidebar();
        }
      });
    });
  }
  
  updateProgress();
  updateEditProgress();
});
</script>

<?php include '../templates/footer.php'; ?>