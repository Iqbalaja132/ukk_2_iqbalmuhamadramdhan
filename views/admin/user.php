<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../../controllers/c_users.php';

include '../templates/header.php';

if (!isset($limit)) $limit = 10;
if (!isset($current_page)) $current_page = 1;
if (!isset($total_data)) $total_data = 0;
if (!isset($total_halaman)) $total_halaman = 0;
if (!isset($current_search)) $current_search = '';
if (!isset($current_role_filter)) $current_role_filter = '';
if (!isset($current_status_filter)) $current_status_filter = '';
if (!isset($data_user)) $data_user = [];
if (!isset($jumlah_admin)) $jumlah_admin = 0;
if (!isset($jumlah_petugas)) $jumlah_petugas = 0;
if (!isset($jumlah_owner)) $jumlah_owner = 0;

$debug_mode = false;
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
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-600">Kelola dan monitor semua pengguna sistem</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button onclick="openTambah()" class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center space-x-2 group">
            <i class="fas fa-user-plus text-lg"></i>
            <span class="hidden sm:inline">Tambah Pengguna</span>
            <i class="fas fa-chevron-right text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 transform group-hover:translate-x-1"></i>
          </button>
        </div>
      </div>
      
      <div class="px-6 pb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Pengguna</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= $total_data ?></p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Admin</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= $jumlah_admin ?></p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="fas fa-user-shield text-green-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Petugas</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= $jumlah_petugas ?></p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                <i class="fas fa-user-tie text-purple-600 text-xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4 border border-amber-100">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Owner</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= $jumlah_owner ?></p>
              </div>
              <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                <i class="fas fa-crown text-amber-600 text-xl"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="p-6">
      <?php if($debug_mode): ?>
      <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
        <h3 class="font-bold text-yellow-800 mb-2">Debug Info:</h3>
        <div class="text-sm text-yellow-700">
          <p>Search: "<?= htmlspecialchars($current_search) ?>"</p>
          <p>Role Filter: "<?= htmlspecialchars($current_role_filter) ?>"</p>
          <p>Status Filter: "<?= htmlspecialchars($current_status_filter) ?>"</p>
          <p>Current Page: <?= $current_page ?></p>
          <p>Total Data: <?= $total_data ?></p>
          <p>Total Pages: <?= $total_halaman ?></p>
          <p>Data Count: <?= count($data_user) ?></p>
        </div>
      </div>
      <?php endif; ?>
      <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden animate-fade-in">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Pengguna</h2>
            <p class="text-sm text-gray-600 mt-1">Semua pengguna terdaftar dalam sistem</p>
          </div>
          
          <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
            <form method="GET" action="" class="relative">
              <input type="text" name="search" placeholder="Cari pengguna..." 
                     value="<?= htmlspecialchars($current_search) ?>"
                     class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition w-full sm:w-64">
              <input type="hidden" name="page" value="1">
              <?php if($current_role_filter): ?>
                <input type="hidden" name="role_filter" value="<?= htmlspecialchars($current_role_filter) ?>">
              <?php endif; ?>
              <?php if($current_status_filter !== ''): ?>
                <input type="hidden" name="status_filter" value="<?= htmlspecialchars($current_status_filter) ?>">
              <?php endif; ?>
              <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
            </form>
            
            <div class="relative">
              <form method="GET" action="" id="roleFilterForm">
                <input type="hidden" name="search" value="<?= htmlspecialchars($current_search) ?>">
                <input type="hidden" name="page" value="1">
                <?php if($current_status_filter !== ''): ?>
                  <input type="hidden" name="status_filter" value="<?= htmlspecialchars($current_status_filter) ?>">
                <?php endif; ?>
                <select name="role_filter" onchange="document.getElementById('roleFilterForm').submit()" 
                        class="pl-10 pr-8 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none appearance-none bg-white cursor-pointer">
                  <option value="">Semua Role</option>
                  <option value="admin" <?= $current_role_filter == 'admin' ? 'selected' : '' ?>>Admin</option>
                  <option value="petugas" <?= $current_role_filter == 'petugas' ? 'selected' : '' ?>>Petugas</option>
                  <option value="owner" <?= $current_role_filter == 'owner' ? 'selected' : '' ?>>Owner</option>
                </select>
                <i class="fas fa-user-tag absolute left-4 top-4 text-gray-400"></i>
                <i class="fas fa-chevron-down absolute right-3 top-4 text-gray-400"></i>
              </form>
            </div>
            
            <div class="relative">
              <form method="GET" action="" id="statusFilterForm">
                <input type="hidden" name="search" value="<?= htmlspecialchars($current_search) ?>">
                <input type="hidden" name="page" value="1">
                <?php if($current_role_filter): ?>
                  <input type="hidden" name="role_filter" value="<?= htmlspecialchars($current_role_filter) ?>">
                <?php endif; ?>
                <select name="status_filter" onchange="document.getElementById('statusFilterForm').submit()" 
                        class="pl-10 pr-8 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none appearance-none bg-white cursor-pointer">
                  <option value="">Semua Status</option>
                  <option value="1" <?= $current_status_filter === '1' ? 'selected' : '' ?>>Aktif</option>
                  <option value="0" <?= $current_status_filter === '0' ? 'selected' : '' ?>>Non-Aktif</option>
                </select>
                <i class="fas fa-circle absolute left-4 top-4 text-gray-400"></i>
                <i class="fas fa-chevron-down absolute right-3 top-4 text-gray-400"></i>
              </form>
            </div>
            
            <?php if($current_search || $current_role_filter || $current_status_filter !== ''): ?>
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
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Lengkap</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Username</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Role</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php 
              if (!empty($data_user)) : 
                  $no = ($current_page - 1) * $limit + 1; 
                  foreach ($data_user as $row): 
              ?>
              <tr class="hover:bg-gray-50/50 transition-all duration-200 animate-slide-in" style="animation-delay: <?= ($no % 10) * 0.05 ?>s;">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900"><?= $no++ ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center mr-3">
                      <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-500 flex items-center justify-center text-white text-sm font-bold">
                        <?= strtoupper(substr($row->nama_lengkap, 0, 1)) ?>
                      </div>
                    </div>
                    <div>
                      <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($row->nama_lengkap) ?></div>
                      <div class="text-xs text-gray-500">ID: <?= $row->id_user ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 font-medium">@<?= htmlspecialchars($row->username) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php 
                  $roleColors = [
                    'admin' => 'from-red-100 to-red-50 text-red-800 border-red-200',
                    'petugas' => 'from-green-100 to-green-50 text-green-800 border-green-200',
                    'owner' => 'from-purple-100 to-purple-50 text-purple-800 border-purple-200'
                  ];
                  $roleIcons = [
                    'admin' => 'fa-user-shield',
                    'petugas' => 'fa-user-tie',
                    'owner' => 'fa-crown'
                  ];
                  ?>
                  <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r <?= $roleColors[$row->role] ?? 'from-gray-100 to-gray-50 text-gray-800 border-gray-200' ?> border">
                    <i class="fas <?= $roleIcons[$row->role] ?? 'fa-user' ?> mr-1.5"></i>
                    <?= htmlspecialchars(ucfirst($row->role)) ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold <?= $row->status_aktif == 1 ? 'bg-gradient-to-r from-green-100 to-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-gradient-to-r from-red-100 to-rose-50 text-rose-800 border border-rose-200' ?>">
                    <?php if($row->status_aktif == 1): ?>
                      <i class="fas fa-check-circle mr-1.5 text-emerald-600"></i>
                      Aktif
                    <?php else: ?>
                      <i class="fas fa-times-circle mr-1.5 text-rose-600"></i>
                      Non-Aktif
                    <?php endif; ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button onclick="openEdit(
                        '<?= $row->id_user ?>',
                        '<?= htmlspecialchars($row->nama_lengkap) ?>',
                        '<?= htmlspecialchars($row->username) ?>',
                        '<?= $row->role ?>',
                        '<?= $row->status_aktif ?>'
                    )" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm">
                      <i class="fas fa-edit mr-1.5"></i> Edit
                    </button>
                    <form action="../../controllers/c_users.php?aksi=hapus" method="post" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                      <input type="hidden" name="id_user" value="<?= $row->id_user ?>">
                      <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 text-red-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm">
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
                <td colspan="6" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-4">
                      <i class="fas fa-users text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">
                      <?php if($current_search || $current_role_filter || $current_status_filter !== ''): ?>
                        Tidak ditemukan pengguna dengan kriteria tersebut
                      <?php else: ?>
                        Belum ada data pengguna
                      <?php endif; ?>
                    </h3>
                    <p class="text-gray-500 mb-4">
                      <?php if($current_search || $current_role_filter || $current_status_filter !== ''): ?>
                        Coba ubah kata kunci pencarian atau filter
                      <?php else: ?>
                        Mulai dengan menambahkan pengguna pertama Anda
                      <?php endif; ?>
                    </p>
                    <?php if($current_search || $current_role_filter || $current_status_filter !== ''): ?>
                      <a href="?page=1" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-redo mr-2"></i> Reset Pencarian
                      </a>
                    <?php else: ?>
                      <button onclick="openTambah()" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-user-plus mr-2"></i> Tambah Pengguna Pertama
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if (!empty($data_user) && $total_halaman > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
          <div class="text-sm text-gray-700 mb-4 sm:mb-0">
            <?php
              $start_data = (($current_page - 1) * $limit) + 1;
              $end_data = min($current_page * $limit, $total_data);
            ?>
            Menampilkan <span class="font-semibold"><?= $start_data ?>-<?= $end_data ?></span> 
            dari <span class="font-semibold"><?= $total_data ?></span> hasil
          </div>
          <div class="flex items-center space-x-2">
            <?php if($current_page > 1): ?>
            <a href="?page=<?= $current_page - 1 ?>&search=<?= urlencode($current_search) ?>&role_filter=<?= urlencode($current_role_filter) ?>&status_filter=<?= urlencode($current_status_filter) ?>"
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
            <a href="?page=1&search=<?= urlencode($current_search) ?>&role_filter=<?= urlencode($current_role_filter) ?>&status_filter=<?= urlencode($current_status_filter) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium">
              1
            </a>
            <?php if($start_page > 2): ?>
            <span class="w-8 h-8 flex items-center justify-center text-gray-400">...</span>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($current_search) ?>&role_filter=<?= urlencode($current_role_filter) ?>&status_filter=<?= urlencode($current_status_filter) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg border <?= $i == $current_page ? 'bg-primary-500 text-white border-primary-500' : 'border-gray-300 hover:bg-gray-50' ?> transition font-medium">
              <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if($end_page < $total_halaman): ?>
            <?php if($end_page < $total_halaman - 1): ?>
            <span class="w-8 h-8 flex items-center justify-center text-gray-400">...</span>
            <?php endif; ?>
            <a href="?page=<?= $total_halaman ?>&search=<?= urlencode($current_search) ?>&role_filter=<?= urlencode($current_role_filter) ?>&status_filter=<?= urlencode($current_status_filter) ?>"
               class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium">
              <?= $total_halaman ?>
            </a>
            <?php endif; ?>
            
            <?php if($current_page < $total_halaman): ?>
            <a href="?page=<?= $current_page + 1 ?>&search=<?= urlencode($current_search) ?>&role_filter=<?= urlencode($current_role_filter) ?>&status_filter=<?= urlencode($current_status_filter) ?>"
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
        <?php elseif(!empty($data_user) && $total_halaman == 1): ?>
        <div class="px-6 py-4 border-t border-gray-200">
          <div class="text-sm text-gray-700">
            Menampilkan semua <span class="font-semibold"><?= $total_data ?></span> hasil
          </div>
        </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 invisible" id="modalTambah">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform scale-95 transition-all duration-300">
    <div class="relative">
      <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white p-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold flex items-center">
              <i class="fas fa-user-plus mr-3"></i> Tambah Pengguna Baru
            </h3>
            <p class="text-primary-100 text-sm mt-1">Isi detail pengguna di bawah ini</p>
          </div>
          <button onclick="closeModal('modalTambah')" class="text-white/80 hover:text-white text-xl transition">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <form action="../../controllers/c_users.php?aksi=tambah" method="post" class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
            <div class="relative">
              <input type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50" oninput="toUpperCaseInput(this)">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-user"></i>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
            <div class="relative">
              <input type="text" name="username" placeholder="Masukkan username" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-at"></i>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
            <div class="relative">
              <input type="password" name="password" id="passwordTambah" placeholder="Masukkan password" required 
                    class="w-full px-4 py-3.5 pl-11 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-lock"></i>
              </div>
              <button type="button" onclick="togglePassword('passwordTambah')" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
            <div class="relative">
              <select name="role" required 
                      class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50 appearance-none">
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
                <option value="owner">Owner</option>
              </select>
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-user-tag"></i>
              </div>
              <div class="absolute right-3 top-3.5 text-gray-400">
                <i class="fas fa-chevron-down"></i>
              </div>
            </div>
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
            <div class="grid grid-cols-2 gap-3">
              <label class="relative">
                <input type="radio" name="status_aktif" value="1" class="hidden peer" checked>
                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition-all duration-200">
                  <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-check-circle text-xl text-gray-600 peer-checked:text-green-600"></i>
                    <div>
                      <span class="font-medium peer-checked:text-green-700">Aktif</span>
                      <p class="text-xs text-gray-500 peer-checked:text-green-600">Pengguna dapat login</p>
                    </div>
                  </div>
                </div>
              </label>
              <label class="relative">
                <input type="radio" name="status_aktif" value="0" class="hidden peer">
                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 transition-all duration-200">
                  <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-times-circle text-xl text-gray-600 peer-checked:text-red-600"></i>
                    <div>
                      <span class="font-medium peer-checked:text-red-700">Non-Aktif</span>
                      <p class="text-xs text-gray-500 peer-checked:text-red-600">Pengguna tidak dapat login</p>
                    </div>
                  </div>
                </div>
              </label>
            </div>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button type="button" onclick="closeModal('modalTambah')" 
                  class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold hover:shadow-sm">
            <i class="fas fa-times mr-2"></i> Batal
          </button>
          <button type="submit" 
                  class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-save mr-2"></i> Simpan Pengguna
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 invisible" id="modalEdit">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform scale-95 transition-all duration-300">
    <div class="relative">
      <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white p-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold flex items-center">
              <i class="fas fa-user-edit mr-3"></i> Edit Data Pengguna
            </h3>
            <p class="text-primary-100 text-sm mt-1">Perbarui detail pengguna di bawah ini</p>
          </div>
          <button onclick="closeModal('modalEdit')" class="text-white/80 hover:text-white text-xl transition">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <form action="../../controllers/c_users.php?aksi=update" method="post" class="p-6 space-y-6">
        <input type="hidden" name="id_user" id="editId">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
            <div class="relative">
              <input type="text" name="nama_lengkap" id="editNama" placeholder="Masukkan nama lengkap" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50" oninput="toUpperCaseInput(this)">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-user"></i>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
            <div class="relative">
              <input type="text" name="username" id="editUsername" placeholder="Masukkan username" required 
                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-at"></i>
              </div>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
            <div class="relative">
              <input type="password" name="password" id="passwordEdit" placeholder="Kosongkan jika tidak diubah" 
                    class="w-full px-4 py-3.5 pl-11 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50">
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-lock"></i>
              </div>
              <button type="button" onclick="togglePassword('passwordEdit')" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah password. Minimal 6 karakter jika diisi.</p>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
            <div class="relative">
              <select name="role" id="editRole" required 
                      class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50 appearance-none">
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
                <option value="owner">Owner</option>
              </select>
              <div class="absolute left-3 top-3.5 text-gray-400">
                <i class="fas fa-user-tag"></i>
              </div>
              <div class="absolute right-3 top-3.5 text-gray-400">
                <i class="fas fa-chevron-down"></i>
              </div>
            </div>
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
            <div class="grid grid-cols-2 gap-3">
              <label class="relative">
                <input type="radio" name="status_aktif" id="editStatusAktif" value="1" class="hidden peer">
                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition-all duration-200">
                  <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-check-circle text-xl text-gray-600 peer-checked:text-green-600"></i>
                    <div>
                      <span class="font-medium peer-checked:text-green-700">Aktif</span>
                      <p class="text-xs text-gray-500 peer-checked:text-green-600">Pengguna dapat login</p>
                    </div>
                  </div>
                </div>
              </label>
              <label class="relative">
                <input type="radio" name="status_aktif" id="editStatusNonAktif" value="0" class="hidden peer">
                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 transition-all duration-200">
                  <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-times-circle text-xl text-gray-600 peer-checked:text-red-600"></i>
                    <div>
                      <span class="font-medium peer-checked:text-red-700">Non-Aktif</span>
                      <p class="text-xs text-gray-500 peer-checked:text-red-600">Pengguna tidak dapat login</p>
                    </div>
                  </div>
                </div>
              </label>
            </div>
          </div>
        </div>
        
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
}

function openEdit(id, nama, username, role, status) {
  document.getElementById('editId').value = id;
  document.getElementById('editNama').value = nama;
  document.getElementById('editUsername').value = username;
  document.getElementById('editRole').value = role;
  
  document.getElementById('editStatusAktif').checked = false;
  document.getElementById('editStatusNonAktif').checked = false;
  
  if (status == '1') {
    document.getElementById('editStatusAktif').checked = true;
  } else {
    document.getElementById('editStatusNonAktif').checked = true;
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

function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  const button = input.parentElement.querySelector('button');

  if (input.type === 'password') {
    input.type = 'text';
    button.innerHTML = '<i class="fas fa-eye-slash"></i>';
  } else {
    input.type = 'password';
    button.innerHTML = '<i class="fas fa-eye"></i>';
  }
}

function toUpperCaseInput(input) {
  input.value = input.value.toUpperCase();
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
});

document.querySelector('#modalTambah form')?.addEventListener('submit', function(e) {
  const password = document.getElementById('passwordTambah').value;
  if (password.length < 6) {
    e.preventDefault();
    alert('Password minimal 6 karakter!');
    return false;
  }
  return true;
});

document.querySelector('#modalEdit form')?.addEventListener('submit', function(e) {
  const password = document.getElementById('passwordEdit').value;
  if (password && password.length < 6) {
    e.preventDefault();
    alert('Password minimal 6 karakter!');
    return false;
  }
  return true;
});

document.querySelectorAll('form[action*="hapus"]').forEach(form => {
  form.addEventListener('submit', function(e) {
    if (!confirm('Yakin ingin menghapus pengguna ini?')) {
      e.preventDefault();
      return false;
    }
    return true;
  });
});
</script>

<?php include '../templates/footer.php'; ?>