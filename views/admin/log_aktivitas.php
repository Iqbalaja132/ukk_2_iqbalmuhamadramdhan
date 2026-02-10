<?php

session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../../controllers/c_logaktivitas.php';

include '../templates/header.php';

if (!isset($limit)) $limit = 15;
if (!isset($current_page)) $current_page = 1;
if (!isset($total_data)) $total_data = 0;
if (!isset($total_halaman)) $total_halaman = 0;
if (!isset($current_search)) $current_search = '';
if (!isset($current_date_filter)) $current_date_filter = '';
if (!isset($current_user_filter)) $current_user_filter = '';
if (!isset($data_log)) $data_log = [];
if (!isset($jumlah_hari_ini)) $jumlah_hari_ini = 0;
if (!isset($daftar_user)) $daftar_user = [];

$jumlah_user_aktif = $log_obj->hitung_user_aktif_hari_ini();
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
            <h1 class="text-2xl font-bold text-gray-800">📜 Log Aktivitas Sistem</h1>
            <p class="text-sm text-gray-600">Rekaman semua aktivitas yang dilakukan di sistem</p>
          </div>
        </div>
      </div>
      
      <div class="px-6 pb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Log</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($total_data, 0, ',', '.') ?></p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                <i class="fas fa-history text-blue-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($jumlah_hari_ini, 0, ',', '.') ?></p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="fas fa-calendar-day text-green-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">User Aktif</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($jumlah_user_aktif, 0, ',', '.') ?></p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                <i class="fas fa-user-clock text-purple-600 text-xl"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="p-6">
      <div class="mb-6 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-100 rounded-xl p-4">
        <div class="flex items-start">
          <div class="mr-3 mt-1">
            <i class="fas fa-info-circle text-indigo-600 text-xl"></i>
          </div>
          <div>
            <h4 class="font-semibold text-indigo-800">Catatan Log Aktivitas</h4>
            <p class="text-sm text-indigo-700 mt-1">
              Halaman ini menampilkan rekaman semua aktivitas yang dilakukan oleh pengguna sistem. 
              <span class="font-semibold">Log aktivitas bersifat read-only dan tidak dapat diubah atau dihapus</span> untuk menjaga integritas audit.
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden animate-fade-in">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Log Aktivitas</h2>
            <p class="text-sm text-gray-600 mt-1">Rekaman aktivitas sistem parkir</p>
          </div>
          
          <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
            <form method="GET" action="" class="relative">
              <input type="text" name="search" placeholder="Cari aktivitas atau user..." 
                     value="<?= htmlspecialchars($current_search) ?>"
                     class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition w-full sm:w-64">
              <input type="hidden" name="page" value="1">
              <?php if($current_date_filter): ?>
                <input type="hidden" name="date_filter" value="<?= htmlspecialchars($current_date_filter) ?>">
              <?php endif; ?>
              <?php if($current_user_filter): ?>
                <input type="hidden" name="user_filter" value="<?= htmlspecialchars($current_user_filter) ?>">
              <?php endif; ?>
              <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
            </form>
            
            <div class="relative">
              <form method="GET" action="" id="dateFilterForm">
                <input type="hidden" name="search" value="<?= htmlspecialchars($current_search) ?>">
                <input type="hidden" name="page" value="1">
                <?php if($current_user_filter): ?>
                  <input type="hidden" name="user_filter" value="<?= htmlspecialchars($current_user_filter) ?>">
                <?php endif; ?>
                <input type="date" name="date_filter" 
                       value="<?= htmlspecialchars($current_date_filter) ?>"
                       onchange="document.getElementById('dateFilterForm').submit()"
                       class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none appearance-none bg-white cursor-pointer w-full sm:w-auto">
                <i class="fas fa-calendar absolute left-4 top-4 text-gray-400"></i>
              </form>
            </div>
            
            <div class="relative">
              <form method="GET" action="" id="userFilterForm">
                <input type="hidden" name="search" value="<?= htmlspecialchars($current_search) ?>">
                <input type="hidden" name="page" value="1">
                <?php if($current_date_filter): ?>
                  <input type="hidden" name="date_filter" value="<?= htmlspecialchars($current_date_filter) ?>">
                <?php endif; ?>
                <select name="user_filter" onchange="document.getElementById('userFilterForm').submit()" 
                        class="pl-10 pr-8 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none appearance-none bg-white cursor-pointer">
                  <option value="">Semua User</option>
                  <?php foreach($daftar_user as $user): ?>
                    <option value="<?= $user['id_user'] ?>" <?= $current_user_filter == $user['id_user'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars(strtoupper($user['nama_lengkap'])) ?> (<?= htmlspecialchars($user['username']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
                <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                <i class="fas fa-chevron-down absolute right-3 top-4 text-gray-400"></i>
              </form>
            </div>
            
            <?php if($current_search || $current_date_filter || $current_user_filter): ?>
            <a href="?page=1" class="p-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-gray-600 hover:text-gray-800 flex items-center justify-center">
              <i class="fas fa-redo"></i>
            </a>
            <?php endif; ?>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Waktu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">User</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aktivitas</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tipe</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php 
              if (!empty($data_log)) : 
                  $no = ($current_page - 1) * $limit + 1; 
                  foreach ($data_log as $row): 
              ?>
              <tr class="hover:bg-gray-50/50 transition-all duration-200 animate-slide-in" style="animation-delay: <?= ($no % 10) * 0.05 ?>s;">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900"><?= $no++ ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 font-semibold">
                    <?= htmlspecialchars($row->waktu_format) ?>
                  </div>
                  <div class="text-xs text-gray-500">
                    <?= htmlspecialchars($row->jam_format) ?>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php if($row->nama_lengkap): ?>
                    <div class="flex items-center">
                      <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center mr-3">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-primary-400 to-primary-500 flex items-center justify-center text-white text-xs font-bold">
                          <?= strtoupper(substr($row->nama_lengkap, 0, 1)) ?>
                        </div>
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars(strtoupper($row->nama_lengkap)) ?></div>
                        <div class="text-xs text-gray-500">
                          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <?= htmlspecialchars($row->username) ?>
                          </span>
                        </div>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="text-sm text-gray-500 italic">
                      <i class="fas fa-user-slash mr-1"></i> User tidak ditemukan
                    </div>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">
                    <?= htmlspecialchars($row->aktivitas) ?>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php
                  $icon = 'fa-info-circle';
                  $color = 'from-gray-100 to-gray-50 text-gray-800 border-gray-200';
                  $text = 'Info';
                  
                  if (strpos(strtolower($row->aktivitas), 'login') !== false) {
                    $icon = 'fa-sign-in-alt';
                    $color = 'from-green-100 to-green-50 text-green-800 border-green-200';
                    $text = 'Login';
                  } elseif (strpos(strtolower($row->aktivitas), 'logout') !== false) {
                    $icon = 'fa-sign-out-alt';
                    $color = 'from-red-100 to-red-50 text-red-800 border-red-200';
                    $text = 'Logout';
                  } elseif (strpos(strtolower($row->aktivitas), 'tambah') !== false || strpos(strtolower($row->aktivitas), 'menambah') !== false) {
                    $icon = 'fa-plus-circle';
                    $color = 'from-blue-100 to-blue-50 text-blue-800 border-blue-200';
                    $text = 'Tambah';
                  } elseif (strpos(strtolower($row->aktivitas), 'edit') !== false || strpos(strtolower($row->aktivitas), 'mengedit') !== false) {
                    $icon = 'fa-edit';
                    $color = 'from-yellow-100 to-yellow-50 text-yellow-800 border-yellow-200';
                    $text = 'Edit';
                  } elseif (strpos(strtolower($row->aktivitas), 'hapus') !== false || strpos(strtolower($row->aktivitas), 'menghapus') !== false) {
                    $icon = 'fa-trash-alt';
                    $color = 'from-red-100 to-red-50 text-red-800 border-red-200';
                    $text = 'Hapus';
                  }
                  ?>
                  <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r <?= $color ?> border">
                    <i class="fas <?= $icon ?> mr-1.5"></i>
                    <?= $text ?>
                  </span>
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
                      <i class="fas fa-history text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">
                      <?php if($current_search || $current_date_filter || $current_user_filter): ?>
                        Tidak ditemukan log aktivitas dengan kriteria tersebut
                      <?php else: ?>
                        Belum ada log aktivitas
                      <?php endif; ?>
                    </h3>
                    <p class="text-gray-500 mb-4">
                      <?php if($current_search || $current_date_filter || $current_user_filter): ?>
                        Coba ubah filter atau kata kunci pencarian
                      <?php else: ?>
                        Aktivitas akan tercatat otomatis ketika ada kegiatan di sistem
                      <?php endif; ?>
                    </p>
                    <?php if($current_search || $current_date_filter || $current_user_filter): ?>
                      <a href="?page=1" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-redo mr-2"></i> Reset Pencarian
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if (!empty($data_log) && $total_halaman > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
          <div class="text-sm text-gray-700 mb-4 sm:mb-0">
            <?php
              $start_data = (($current_page - 1) * $limit) + 1;
              $end_data = min($current_page * $limit, $total_data);
            ?>
            Menampilkan <span class="font-semibold"><?= $start_data ?>-<?= $end_data ?></span> 
            dari <span class="font-semibold"><?= $total_data ?></span> log aktivitas
          </div>
          <div class="flex items-center space-x-2">
            <?php if($current_page > 1): ?>
            <a href="?page=<?= $current_page - 1 ?>&search=<?= urlencode($current_search) ?>&date_filter=<?= urlencode($current_date_filter) ?>&user_filter=<?= urlencode($current_user_filter) ?>"
               class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
              <i class="fas fa-chevron-left text-gray-600"></i>
            </a>
            <?php else: ?>
            <span class="p-2 rounded-lg border border-gray-300 opacity-50 cursor-not-allowed">
              <i class="fas fa-chevron-left text-gray-600"></i>
            </span>
            <?php endif; ?>
            
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
            
            if($start_page > 1): ?>
            <a href="?page=1&search=<?= urlencode($current_search) ?>&date_filter=<?= urlencode($current_date_filter) ?>&user_filter=<?= urlencode($current_user_filter) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium">
              1
            </a>
            <?php if($start_page > 2): ?>
            <span class="w-8 h-8 flex items-center justify-center text-gray-400">...</span>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($current_search) ?>&date_filter=<?= urlencode($current_date_filter) ?>&user_filter=<?= urlencode($current_user_filter) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg border <?= $i == $current_page ? 'bg-primary-500 text-white border-primary-500' : 'border-gray-300 hover:bg-gray-50' ?> transition font-medium">
              <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if($end_page < $total_halaman): ?>
            <?php if($end_page < $total_halaman - 1): ?>
            <span class="w-8 h-8 flex items-center justify-center text-gray-400">...</span>
            <?php endif; ?>
            <a href="?page=<?= $total_halaman ?>&search=<?= urlencode($current_search) ?>&date_filter=<?= urlencode($current_date_filter) ?>&user_filter=<?= urlencode($current_user_filter) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium">
              <?= $total_halaman ?>
            </a>
            <?php endif; ?>
            
            <?php if($current_page < $total_halaman): ?>
            <a href="?page=<?= $current_page + 1 ?>&search=<?= urlencode($current_search) ?>&date_filter=<?= urlencode($current_date_filter) ?>&user_filter=<?= urlencode($current_user_filter) ?>"
               class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
              <i class="fas fa-chevron-right text-gray-600"></i>
            </a>
            <?php else: ?>
            <span class="p-2 rounded-lg border border-gray-300 opacity-50 cursor-not-allowed">
              <i class="fas fa-chevron-right text-gray-600"></i>
            </span>
            <?php endif; ?>
          </div>
        </div>
        <?php elseif(!empty($data_log) && $total_halaman == 1): ?>
        <div class="px-6 py-4 border-t border-gray-200">
          <div class="text-sm text-gray-700">
            Menampilkan semua <span class="font-semibold"><?= $total_data ?></span> log aktivitas
          </div>
        </div>
        <?php endif; ?>
      </div>
    </main>
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

let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
  searchInput.addEventListener('keyup', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      if (e.target.value.length >= 2 || e.target.value.length === 0) {
        this.form.submit();
      }
    }, 800);
  });
}

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
});

document.getElementById('sidebarOverlay').addEventListener('click', toggleSidebar);
</script>

<?php include '../templates/footer.php'; ?>