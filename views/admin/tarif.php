<?php

session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$search = $_GET['search'] ?? '';
$jenis_filter = $_GET['jenis_filter'] ?? '';
$current_page = $_GET['page'] ?? 1;
$limit = 10;

include_once __DIR__ . '/../../controllers/c_tarif.php';

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
                        <h1 class="text-2xl font-bold text-gray-800">Data Tarif Parkir</h1>
                        <p class="text-sm text-gray-600">Kelola tarif parkir per jenis kendaraan</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button onclick="openTambah()" class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center space-x-2 group">
                        <i class="fas fa-plus-circle text-lg"></i>
                        <span class="hidden sm:inline">Tambah Tarif</span>
                        <i class="fas fa-chevron-right text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 transform group-hover:translate-x-1"></i>
                    </button>
                </div>
            </div>
            
            <div class="px-6 pb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Tarif</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1"><?= !empty($total_data) ? $total_data : '0' ?></p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Tarif Tertinggi</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1">
                                    Rp <?= !empty($tarif_tertinggi) ? number_format($tarif_tertinggi, 0, ',', '.') : '0' ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                                <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-4 border border-yellow-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Tarif Terendah</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1">
                                    Rp <?= !empty($tarif_terendah) ? number_format($tarif_terendah, 0, ',', '.') : '0' ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                                <i class="fas fa-arrow-down text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Rata-rata Tarif</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1">
                                    Rp <?= !empty($rata_rata_tarif) ? $rata_rata_tarif : '0' ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden animate-fade-in">
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Daftar Tarif Parkir</h2>
                        <p class="text-sm text-gray-600 mt-1">Maksimal 3 jenis kendaraan: Motor, Mobil, Lainnya</p>
                        <p class="text-xs text-gray-500 mt-1">Maksimal tarif per jam: Rp 99.000</p>
                    </div>
                    
                    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                        <form method="GET" action="" class="relative">
                            <input type="text" name="search" placeholder="Cari tarif..." 
                                   value="<?= htmlspecialchars($search) ?>"
                                   class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition w-full sm:w-64">
                            <input type="hidden" name="page" value="1">
                            <?php if($jenis_filter): ?>
                                <input type="hidden" name="jenis_filter" value="<?= htmlspecialchars($jenis_filter) ?>">
                            <?php endif; ?>
                            <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                        </form>
                        
                        <div class="relative">
                            <form method="GET" action="" id="filterForm">
                                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                <input type="hidden" name="page" value="1">
                                <select name="jenis_filter" onchange="document.getElementById('filterForm').submit()" 
                                        class="pl-10 pr-8 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none appearance-none bg-white cursor-pointer">
                                    <option value="">Semua Jenis</option>
                                    <option value="motor" <?= $jenis_filter == 'motor' ? 'selected' : '' ?>>Motor</option>
                                    <option value="mobil" <?= $jenis_filter == 'mobil' ? 'selected' : '' ?>>Mobil</option>
                                    <option value="lainnya" <?= $jenis_filter == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                                <i class="fas fa-filter absolute left-4 top-4 text-gray-400"></i>
                                <i class="fas fa-chevron-down absolute right-3 top-4 text-gray-400"></i>
                            </form>
                        </div>
                        
                        <?php if($search || $jenis_filter): ?>
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
                                    Jenis Kendaraan
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Tarif Per Jam
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
                            if (!empty($data_tarif)) : 
                                $no = ($current_page - 1) * $limit + 1; 
                                foreach ($data_tarif as $row): 
                                    $tarif_formatted = 'Rp ' . number_format($row->tarif_per_jam, 0, ',', '.');
                                    $is_max = $row->tarif_per_jam >= 99000;
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-all duration-200 animate-slide-in" style="animation-delay: <?= ($no % 10) * 0.05 ?>s;">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= $no++ ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg 
                                            <?= $row->jenis_kendaraan == 'motor' ? 'bg-gradient-to-br from-yellow-100 to-yellow-50' : 
                                               ($row->jenis_kendaraan == 'mobil' ? 'bg-gradient-to-br from-blue-100 to-blue-50' : 
                                               'bg-gradient-to-br from-gray-100 to-gray-50') ?> 
                                            flex items-center justify-center mr-3">
                                            <i class="
                                                <?= $row->jenis_kendaraan == 'motor' ? 'fas fa-motorcycle text-yellow-600' : 
                                                   ($row->jenis_kendaraan == 'mobil' ? 'fas fa-car text-blue-600' : 
                                                   'fas fa-truck text-gray-600') ?>
                                            "></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                <?= htmlspecialchars(ucfirst($row->jenis_kendaraan)) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">ID: <?= $row->id_tarif ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold text-gray-900 <?= $is_max ? 'text-red-600' : '' ?>">
                                            <?= $tarif_formatted ?>
                                            <?php if($is_max): ?>
                                                <i class="fas fa-exclamation-circle text-xs ml-1" title="Sudah mencapai batas maksimum"></i>
                                            <?php endif; ?>
                                        </span>
                                        <span class="text-sm text-gray-500 ml-1">/jam</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?= 'Rp ' . number_format($row->tarif_per_jam * 24, 0, ',', '.') ?> /hari
                                        <?php if($is_max): ?>
                                            <span class="text-red-500 font-medium ml-2">
                                                <i class="fas fa-info-circle"></i> Maksimum
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1.5"></i> Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="openEdit(
                                            '<?= $row->id_tarif ?>',
                                            '<?= htmlspecialchars($row->jenis_kendaraan, ENT_QUOTES) ?>',
                                            '<?= $row->tarif_per_jam ?>'
                                        )" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm <?= $is_max ? 'opacity-75 cursor-not-allowed' : '' ?>">
                                            <i class="fas fa-edit mr-1.5"></i> Edit Tarif
                                        </button>
                                        <button onclick="hapusTarif('<?= $row->id_tarif ?>', '<?= htmlspecialchars(ucfirst($row->jenis_kendaraan), ENT_QUOTES) ?>')" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 text-red-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm">
                                            <i class="fas fa-trash-alt mr-1.5"></i> Hapus
                                        </button>
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
                                            <i class="fas fa-money-bill-wave text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-700 mb-2">
                                            <?php if($search || $jenis_filter): ?>
                                                Tidak ditemukan tarif dengan kriteria tersebut
                                            <?php else: ?>
                                                Belum ada data tarif
                                            <?php endif; ?>
                                        </h3>
                                        <p class="text-gray-500 mb-4">
                                            <?php if($search || $jenis_filter): ?>
                                                Coba ubah kata kunci pencarian atau filter
                                            <?php else: ?>
                                                Mulai dengan menambahkan tarif pertama Anda
                                            <?php endif; ?>
                                        </p>
                                        <?php if($search || $jenis_filter): ?>
                                            <a href="?page=1" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all duration-200">
                                                <i class="fas fa-redo mr-2"></i> Reset Pencarian
                                            </a>
                                        <?php else: ?>
                                            <button onclick="openTambah()" class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all duration-200">
                                                <i class="fas fa-plus-circle mr-2"></i> Tambah Tarif Pertama
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($data_tarif) && $total_halaman > 1): ?>
                <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        <?php
                            $start_data = (($current_page - 1) * $limit) + 1;
                            $end_data = min($current_page * $limit, $total_data);
                        ?>
                        Menampilkan <span class="font-semibold"><?= $start_data ?>-<?= $end_data ?></span> dari <span class="font-semibold"><?= $total_data ?></span> hasil
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="?page=<?= $current_page > 1 ? $current_page - 1 : 1 ?>&search=<?= urlencode($search) ?>&jenis_filter=<?= urlencode($jenis_filter) ?>"
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
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&jenis_filter=<?= urlencode($jenis_filter) ?>"
                           class="w-8 h-8 flex items-center justify-center rounded-lg border <?= $i == $current_page ? 'bg-primary-500 text-white border-primary-500' : 'border-gray-300 hover:bg-gray-50' ?> transition font-medium">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>
                        
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

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 invisible" id="modalTambah">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300">
        <div class="relative">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold flex items-center">
                            <i class="fas fa-money-bill-wave mr-3"></i> Tambah Tarif Baru
                        </h3>
                        <p class="text-primary-100 text-sm mt-1">Pilih jenis kendaraan dan tentukan tarifnya</p>
                        <p class="text-primary-100 text-xs mt-1">Maksimal tarif: Rp 99.000 per jam</p>
                    </div>
                    <button onclick="closeModal('modalTambah')" class="text-white/80 hover:text-white text-xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form action="../../controllers/c_tarif.php?aksi=tambah" method="post" class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Jenis Kendaraan</label>
                    <div class="grid grid-cols-3 gap-3">
                        <?php foreach (['motor', 'mobil', 'lainnya'] as $jenis): ?>
                        <label class="relative <?= $jenis_sudah_ada[$jenis] ? 'opacity-50 cursor-not-allowed' : '' ?>">
                            <input type="radio" name="jenis_kendaraan" value="<?= $jenis ?>" 
                                   class="hidden peer" required
                                   <?= $jenis_sudah_ada[$jenis] ? 'disabled' : '' ?>>
                            <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-<?= 
                                $jenis == 'motor' ? 'yellow' : ($jenis == 'mobil' ? 'blue' : 'gray') ?>-500 
                                peer-checked:bg-<?= $jenis == 'motor' ? 'yellow' : ($jenis == 'mobil' ? 'blue' : 'gray') ?>-50 
                                transition-all duration-200 <?= $jenis_sudah_ada[$jenis] ? 'bg-gray-100' : '' ?>">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i class="fas fa-<?= $jenis == 'motor' ? 'motorcycle' : ($jenis == 'mobil' ? 'car' : 'truck') ?> 
                                        text-xl text-gray-600 peer-checked:text-<?= 
                                        $jenis == 'motor' ? 'yellow' : ($jenis == 'mobil' ? 'blue' : 'gray') ?>-600 
                                        <?= $jenis_sudah_ada[$jenis] ? 'text-gray-400' : '' ?>"></i>
                                    <span class="font-medium peer-checked:text-<?= 
                                        $jenis == 'motor' ? 'yellow' : ($jenis == 'mobil' ? 'blue' : 'gray') ?>-700
                                        <?= $jenis_sudah_ada[$jenis] ? 'text-gray-500' : '' ?>">
                                        <?= ucfirst($jenis) ?>
                                    </span>
                                    <?php if($jenis_sudah_ada[$jenis]): ?>
                                    <span class="text-xs text-red-500 font-medium mt-1">
                                        <i class="fas fa-exclamation-circle"></i> Sudah ada
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if($jenis_sudah_ada['motor'] && $jenis_sudah_ada['mobil'] && $jenis_sudah_ada['lainnya']): ?>
                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Semua jenis kendaraan sudah memiliki tarif. Tidak dapat menambahkan tarif baru.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tarif Per Jam (Rp)</label>
                    <div class="relative">
                        <input type="text" name="tarif_per_jam" placeholder="Contoh: 5.000" required 
                               pattern="[0-9.,]*" 
                               onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 44 || event.charCode === 46"
                               maxlength="6"
                               class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50 tarif-input"
                               oninput="validateTarifInput(this)">
                        <div class="absolute left-3 top-3.5 text-gray-400">
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <div class="absolute right-3 top-3.5 text-gray-500 text-sm">
                            /jam
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <span class="tarif-preview">Rp 0</span> per jam · 
                        <span class="tarif-harian">Rp 0</span> per hari
                        <span id="maxLimitInfo" class="text-red-500 font-medium ml-2 hidden">
                            <i class="fas fa-exclamation-triangle"></i> Maksimal Rp 99.000
                        </span>
                    </p>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('modalTambah')" 
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold hover:shadow-sm">
                        <i class="fas fa-times mr-2"></i> Batal
                    </button>
                    <button type="submit" id="submitTambahBtn"
                            class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200
                            <?= ($jenis_sudah_ada['motor'] && $jenis_sudah_ada['mobil'] && $jenis_sudah_ada['lainnya']) ? 'opacity-50 cursor-not-allowed' : '' ?>"
                            <?= ($jenis_sudah_ada['motor'] && $jenis_sudah_ada['mobil'] && $jenis_sudah_ada['lainnya']) ? 'disabled' : '' ?>>
                        <i class="fas fa-save mr-2"></i> Simpan Tarif
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
                            <i class="fas fa-edit mr-3"></i> Edit Tarif
                        </h3>
                        <p class="text-primary-100 text-sm mt-1" id="editJenisInfo">Perbarui tarif per jam</p>
                        <p class="text-primary-100 text-xs mt-1">Maksimal tarif: Rp 99.000 per jam</p>
                    </div>
                    <button onclick="closeModal('modalEdit')" class="text-white/80 hover:text-white text-xl transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form action="../../controllers/c_tarif.php?aksi=update" method="post" class="p-6 space-y-6" onsubmit="return validateEditForm()">
                <input type="hidden" name="id_tarif" id="editId">
                <input type="hidden" name="jenis_kendaraan" id="editJenis">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kendaraan</label>
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg mr-3 flex items-center justify-center" id="editIconContainer">
                                <i class="fas fa-motorcycle text-xl"></i>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900" id="editJenisText">Motor</div>
                                <div class="text-xs text-gray-500">Jenis kendaraan tidak dapat diubah</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tarif Per Jam (Rp)</label>
                    <div class="relative">
                        <input type="text" name="tarif_per_jam" id="editTarif" required 
                               pattern="[0-9.,]*" 
                               onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 44 || event.charCode === 46"
                               maxlength="6"
                               class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition bg-gray-50/50 tarif-input"
                               oninput="validateTarifInput(this)">
                        <div class="absolute left-3 top-3.5 text-gray-400">
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <div class="absolute right-3 top-3.5 text-gray-500 text-sm">
                            /jam
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <span class="tarif-preview">Rp 0</span> per jam · 
                        <span class="tarif-harian">Rp 0</span> per hari
                        <span id="editMaxLimitInfo" class="text-red-500 font-medium ml-2 hidden">
                            <i class="fas fa-exclamation-triangle"></i> Maksimal Rp 99.000
                        </span>
                    </p>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('modalEdit')" 
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold hover:shadow-sm">
                        <i class="fas fa-times mr-2"></i> Batal
                    </button>
                    <button type="submit" id="submitEditBtn"
                            class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                        <i class="fas fa-sync-alt mr-2"></i> Perbarui Tarif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<form action="../../controllers/c_tarif.php?aksi=hapus" method="post" id="formHapus" class="hidden">
    <input type="hidden" name="id_tarif" id="hapusId">
</form>

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
    
    document.querySelector('#modalTambah .tarif-input').value = '';
    updateTarifPreview(document.querySelector('#modalTambah .tarif-input'));
    document.getElementById('maxLimitInfo').classList.add('hidden');
    document.getElementById('submitTambahBtn').disabled = false;
    document.getElementById('submitTambahBtn').classList.remove('opacity-50', 'cursor-not-allowed');
}

function openEdit(id, jenis, tarif) {
    document.getElementById('editId').value = id;
    document.getElementById('editJenis').value = jenis;
    document.getElementById('editJenisText').textContent = jenis.charAt(0).toUpperCase() + jenis.slice(1);
    document.getElementById('editJenisInfo').textContent = `Perbarui tarif ${jenis} per jam`;
    
    document.getElementById('editTarif').value = formatNumber(tarif);
    
    const iconContainer = document.getElementById('editIconContainer');
    iconContainer.className = 'w-10 h-10 rounded-lg mr-3 flex items-center justify-center';
    
    if (jenis === 'motor') {
        iconContainer.classList.add('bg-gradient-to-br', 'from-yellow-100', 'to-yellow-50');
        iconContainer.innerHTML = '<i class="fas fa-motorcycle text-xl text-yellow-600"></i>';
    } else if (jenis === 'mobil') {
        iconContainer.classList.add('bg-gradient-to-br', 'from-blue-100', 'to-blue-50');
        iconContainer.innerHTML = '<i class="fas fa-car text-xl text-blue-600"></i>';
    } else {
        iconContainer.classList.add('bg-gradient-to-br', 'from-gray-100', 'to-gray-50');
        iconContainer.innerHTML = '<i class="fas fa-truck text-xl text-gray-600"></i>';
    }
    
    updateTarifPreview(document.getElementById('editTarif'), tarif);
    
    if (tarif >= 99000) {
        document.getElementById('editMaxLimitInfo').classList.remove('hidden');
        document.getElementById('submitEditBtn').disabled = true;
        document.getElementById('submitEditBtn').classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        document.getElementById('editMaxLimitInfo').classList.add('hidden');
        document.getElementById('submitEditBtn').disabled = false;
        document.getElementById('submitEditBtn').classList.remove('opacity-50', 'cursor-not-allowed');
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

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function validateTarifInput(input) {
    const numericValue = parseInt(input.value.replace(/[^\d]/g, '')) || 0;
    
    if (numericValue > 99000) {
        input.value = '99.000';
        
        const maxLimitInfo = input.id === 'editTarif' ? 
            document.getElementById('editMaxLimitInfo') : 
            document.getElementById('maxLimitInfo');
        maxLimitInfo.classList.remove('hidden');
        
        if (input.id === 'editTarif') {
            document.getElementById('submitEditBtn').disabled = true;
            document.getElementById('submitEditBtn').classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            document.getElementById('submitTambahBtn').disabled = true;
            document.getElementById('submitTambahBtn').classList.add('opacity-50', 'cursor-not-allowed');
        }
    } else {
        const maxLimitInfo = input.id === 'editTarif' ? 
            document.getElementById('editMaxLimitInfo') : 
            document.getElementById('maxLimitInfo');
        maxLimitInfo.classList.add('hidden');
        
        if (input.id === 'editTarif') {
            document.getElementById('submitEditBtn').disabled = false;
            document.getElementById('submitEditBtn').classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            document.getElementById('submitTambahBtn').disabled = false;
            document.getElementById('submitTambahBtn').classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    updateTarifPreview(input);
}

function updateTarifPreview(input, initialValue = null) {
    let value;
    
    if (initialValue !== null) {
        value = initialValue;
    } else {
        value = parseFloat(input.value.replace(/[^\d]/g, '')) || 0;
    }
    
    const preview = input.closest('div').nextElementSibling.querySelector('.tarif-preview');
    const harian = input.closest('div').nextElementSibling.querySelector('.tarif-harian');
    
    if (preview && harian) {
        preview.textContent = 'Rp ' + value.toLocaleString('id-ID');
        harian.textContent = 'Rp ' + (value * 24).toLocaleString('id-ID');
    }
}

function hapusTarif(id, jenis) {
    if (confirm(`Apakah Anda yakin ingin menghapus tarif untuk ${jenis}?\n\nAksi ini tidak dapat dibatalkan!`)) {
        document.getElementById('hapusId').value = id;
        document.getElementById('formHapus').submit();
    }
}

function validateEditForm() {
    const input = document.getElementById('editTarif');
    const numericValue = parseInt(input.value.replace(/[^\d]/g, '')) || 0;
    
    if (numericValue > 99000) {
        alert('Tarif tidak boleh melebihi Rp 99.000 per jam!');
        input.focus();
        return false;
    }
    
    return true;
}

document.addEventListener('DOMContentLoaded', () => {
    const tarifInputs = document.querySelectorAll('.tarif-input');
    
    tarifInputs.forEach(input => {
        function handleTarifInput() {
            let cursorPosition = this.selectionStart;
            let originalValue = this.value;
            
            let value = originalValue.replace(/[^\d]/g, '');
            
            if (value && parseInt(value) > 99000) {
                value = '99000';
            }
            
            if (value) {
                const numericValue = parseInt(value);
                const formattedValue = numericValue.toLocaleString('id-ID');
                
                this.value = formattedValue;
                
                validateTarifInput(this);
                
                let newCursorPosition = cursorPosition;
                if (originalValue.length !== formattedValue.length) {
                    const diff = formattedValue.length - originalValue.length;
                    newCursorPosition = Math.max(0, cursorPosition + diff);
                }
                
                this.setSelectionRange(newCursorPosition, newCursorPosition);
            } else {
                this.value = '';
                const preview = this.closest('div').nextElementSibling?.querySelector('.tarif-preview');
                const harian = this.closest('div').nextElementSibling?.querySelector('.tarif-harian');
                
                if (preview && harian) {
                    preview.textContent = 'Rp 0';
                    harian.textContent = 'Rp 0';
                }
                
                const maxLimitInfo = this.id === 'editTarif' ? 
                    document.getElementById('editMaxLimitInfo') : 
                    document.getElementById('maxLimitInfo');
                maxLimitInfo.classList.add('hidden');
            }
        }
        
        input.addEventListener('input', handleTarifInput);
        
        input.addEventListener('focus', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = value;
                this.setSelectionRange(this.value.length, this.value.length);
            }
        });
        
        input.addEventListener('blur', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                const numericValue = parseInt(value);
                this.value = numericValue.toLocaleString('id-ID');
                
                validateTarifInput(this);
            }
        });
        
        if (input.value && input.id === 'editTarif') {
            const value = input.value.replace(/[^\d]/g, '');
            if (value) {
                const numericValue = parseInt(value);
                const preview = input.closest('div').nextElementSibling?.querySelector('.tarif-preview');
                const harian = input.closest('div').nextElementSibling?.querySelector('.tarif-harian');
                
                if (preview && harian) {
                    preview.textContent = 'Rp ' + numericValue.toLocaleString('id-ID');
                    harian.textContent = 'Rp ' + (numericValue * 24).toLocaleString('id-ID');
                }
            }
        }
    });
    
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const tarifInputs = this.querySelectorAll('.tarif-input');
            tarifInputs.forEach(input => {
                const numericValue = parseInt(input.value.replace(/[^\d]/g, '')) || 0;
                
                if (numericValue > 99000) {
                    alert('Tarif tidak boleh melebihi Rp 99.000 per jam!');
                    event.preventDefault();
                    return;
                }
                
                input.value = numericValue;
            });
        });
    });
    
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
    
    const tambahButton = document.querySelector('button[onclick="openTambah()"]');
    const semuaJenisAda = <?= json_encode($jenis_sudah_ada['motor'] && $jenis_sudah_ada['mobil'] && $jenis_sudah_ada['lainnya']) ?>;
    
    if (semuaJenisAda && tambahButton) {
        tambahButton.classList.add('opacity-50', 'cursor-not-allowed');
        tambahButton.setAttribute('onclick', 'alert("Semua jenis kendaraan sudah memiliki tarif. Tidak dapat menambahkan tarif baru.")');
    }
});

function alertSemuaJenisAda() {
    alert("Semua jenis kendaraan (Motor, Mobil, Lainnya) sudah memiliki tarif.\n\nUntuk menambah tarif baru, Anda harus menghapus salah satu tarif yang ada terlebih dahulu.");
}
</script>

<?php include '../templates/footer.php'; ?>