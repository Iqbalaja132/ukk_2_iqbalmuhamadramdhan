<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../../controllers/c_transaksi.php';

include '../templates/header.php';

$current_page = $current_page ?? 1;
$total_halaman = $total_halaman ?? 0;
$total_data = $total_data ?? 0;
$current_search = $current_search ?? '';
$filter_status = $_GET['status'] ?? 'aktif';
$total_transaksi_hari_ini = $total_transaksi_hari_ini ?? 0;
$total_pendapatan_hari_ini = $total_pendapatan_hari_ini ?? 0;
$kendaraan_aktif = $kendaraan_aktif ?? 0;
$ketersediaan_area = $ketersediaan_area ?? [];
$data_transaksi_aktif = $data_transaksi_aktif ?? [];
$data_riwayat = $data_riwayat ?? [];
$data_area = $data_area ?? [];
?>

<style>
    .animate-pulse-slow {
        animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    .slot-tersedia {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>

<div class="flex min-h-screen bg-gray-50">
    <?php include '../templates/sidebar.php'; ?>

    <div class="flex-1 md:ml-72 pt-2 transition-all duration-300">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-200/50">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center space-x-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-bars text-xl text-gray-700"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-parking mr-2 text-primary-500"></i>
                            Transaksi Parkir
                        </h1>
                        <p class="text-sm text-gray-600">Kelola parkir masuk dan keluar kendaraan</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex items-center bg-blue-50 px-4 py-2 rounded-xl">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700"><?= date('d F Y') ?></span>
                    </div>
                    <div class="h-8 w-px bg-gray-200"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center text-white text-sm font-bold">
                            <?= strtoupper(substr($_SESSION['data']['nama_lengkap'], 0, 1)) ?>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-700 hidden sm:block">
                            <?= $_SESSION['data']['nama_lengkap'] ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="px-6 pb-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm">Transaksi Hari Ini</p>
                                <p class="text-3xl font-bold mt-1"><?= $total_transaksi_hari_ini ?></p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white/30 flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-exchange-alt text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm">Pendapatan Hari Ini</p>
                                <p class="text-2xl font-bold mt-1">Rp <?= number_format($total_pendapatan_hari_ini, 0, ',', '.') ?></p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white/30 flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-money-bill-wave text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm">Kendaraan Aktif</p>
                                <p class="text-3xl font-bold mt-1"><?= $kendaraan_aktif ?></p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white/30 flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-car text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-amber-100 text-sm">Total Area</p>
                                <p class="text-3xl font-bold mt-1"><?= count($ketersediaan_area) ?></p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-white/30 flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-map-marked-alt text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-parking mr-2 text-primary-500"></i>
                        Ketersediaan Area Parkir
                    </h2>
                    <span class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded-full">
                        <i class="fas fa-info-circle mr-1"></i>
                        Update Real-time
                    </span>
                </div>
                
                <?php if (empty($ketersediaan_area)): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-parking text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">Belum Ada Area Parkir</h3>
                    <p class="text-sm text-gray-600">Silakan hubungi admin untuk menambahkan area parkir</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php foreach ($ketersediaan_area as $area): ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-2">
                                    <i class="fas fa-parking text-blue-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($area->nama_area) ?></h3>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full <?= $area->sisa > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $area->sisa ?> Slot
                            </span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Terisi</span>
                                <span class="font-medium text-gray-800"><?= $area->terisi ?> / <?= $area->kapasitas ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full <?= $area->persentase > 90 ? 'bg-red-500' : ($area->persentase > 70 ? 'bg-yellow-500' : 'bg-green-500') ?>" 
                                     style="width: <?= $area->persentase ?>%"></div>
                            </div>
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                <?= $area->sisa > 0 ? 'Tersedia' : 'Penuh' ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-2xl shadow-soft border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-sign-in-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Parkir Masuk</h2>
                                <p class="text-sm text-gray-600">Input kendaraan yang akan parkir</p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="../../controllers/c_transaksi.php?aksi=masuk" method="post" class="p-6" id="formMasuk">
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-id-card mr-1 text-blue-500"></i>
                                    Plat Nomor <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="plat_nomor" id="plat_nomor_masuk" 
                                           placeholder="Contoh: B 1234 ABC" required
                                           class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-gray-50/50 uppercase"
                                           oninput="cariKendaraan(this.value)" autocomplete="off"
                                           value="<?= isset($_GET['plat']) ? htmlspecialchars($_GET['plat']) : '' ?>">
                                    <div class="absolute left-3 top-3.5 text-gray-400">
                                        <i class="fas fa-car"></i>
                                    </div>
                                    <div id="suggestKendaraan" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg hidden max-h-60 overflow-y-auto"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle"></i>
                                    Ketik plat nomor, data akan otomatis terisi jika sudah terdaftar
                                </p>
                            </div>
                            
                            <div id="dataKendaraanTerdaftar" style="display: none;">
                                <div class="bg-blue-50/50 rounded-xl p-4 border border-blue-200">
                                    <div class="flex items-center mb-3">
                                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                            <i class="fas fa-check-circle text-blue-600 text-xs"></i>
                                        </div>
                                        <h3 class="text-sm font-semibold text-blue-700">Kendaraan Terdaftar</h3>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-xs text-gray-500">Jenis</p>
                                            <p class="text-sm font-medium text-gray-800" id="display_jenis"></p>
                                            <input type="hidden" name="jenis_kendaraan" id="jenis_kendaraan_terdaftar">
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Warna</p>
                                            <p class="text-sm font-medium text-gray-800" id="display_warna"></p>
                                            <input type="hidden" name="warna" id="warna_terdaftar">
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="text-xs text-gray-500">Pemilik</p>
                                            <p class="text-sm font-medium text-gray-800" id="display_pemilik"></p>
                                            <input type="hidden" name="pemilik" id="pemilik_terdaftar">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="formKendaraanBaru">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-motorcycle mr-1 text-gray-500"></i>
                                            Jenis Kendaraan <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <select name="jenis_kendaraan" id="jenis_kendaraan_select" required
                                                    class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-gray-50/50 appearance-none">
                                                <option value="">Pilih Jenis</option>
                                                <option value="motor">Motor</option>
                                                <option value="mobil">Mobil</option>
                                                <option value="lainnya">Lainnya</option>
                                            </select>
                                            <div class="absolute left-3 top-3.5 text-gray-400">
                                                <i class="fas fa-tag"></i>
                                            </div>
                                            <div class="absolute right-3 top-3.5 text-gray-400">
                                                <i class="fas fa-chevron-down"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-palette mr-1 text-gray-500"></i>
                                            Warna
                                        </label>
                                        <div class="relative">
                                            <input type="text" name="warna" id="warna_input" 
                                                   placeholder="Contoh: Merah, Hitam"
                                                   class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-gray-50/50">
                                            <div class="absolute left-3 top-3.5 text-gray-400">
                                                <i class="fas fa-paint-brush"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-user mr-1 text-gray-500"></i>
                                            Pemilik
                                        </label>
                                        <div class="relative">
                                            <input type="text" name="pemilik" id="pemilik_input" 
                                                   placeholder="Nama pemilik kendaraan (Isi '-' jika tidak diketahui)"
                                                   value="-"
                                                   class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-gray-50/50 uppercase">
                                            <div class="absolute left-3 top-3.5 text-gray-400">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                                    Area Parkir <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="id_area" id="id_area" required
                                            class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-gray-50/50 appearance-none">
                                        <option value="">Pilih Area Parkir</option>
                                        <?php if (!empty($data_area)): ?>
                                            <?php foreach ($data_area as $area): ?>
                                            <option value="<?= $area->id_area ?>">
                                                <?= htmlspecialchars($area->nama_area) ?> (Tersedia: <?= $area->sisa ?> slot)
                                            </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>Tidak ada area tersedia</option>
                                        <?php endif; ?>
                                    </select>
                                    <div class="absolute left-3 top-3.5 text-gray-400">
                                        <i class="fas fa-parking"></i>
                                    </div>
                                    <div class="absolute right-3 top-3.5 text-gray-400">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                                <?php if (empty($data_area)): ?>
                                <p class="text-xs text-red-500 mt-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Semua area parkir penuh!
                                </p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex justify-end pt-4">
                                <button type="submit" 
                                        <?= empty($data_area) ? 'disabled' : '' ?>
                                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center space-x-2 <?= empty($data_area) ? 'opacity-50 cursor-not-allowed' : '' ?>">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Proses Parkir Masuk</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="bg-white rounded-2xl shadow-soft border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i class="fas fa-sign-out-alt text-green-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Parkir Keluar</h2>
                                <p class="text-sm text-gray-600">Proses kendaraan keluar dan hitung biaya</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-search mr-1 text-green-500"></i>
                                    Cari Plat Nomor
                                </label>
                                <div class="relative">
                                    <input type="text" id="plat_nomor_keluar" 
                                           placeholder="Masukkan plat nomor kendaraan"
                                           class="w-full px-4 py-3.5 pl-11 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition bg-gray-50/50 uppercase"
                                           oninput="cariTransaksiAktif(this.value)" autocomplete="off">
                                    <div class="absolute left-3 top-3.5 text-gray-400">
                                        <i class="fas fa-car"></i>
                                    </div>
                                    <div id="suggestTransaksi" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg hidden max-h-60 overflow-y-auto"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle"></i>
                                    Cari kendaraan yang masih parkir
                                </p>
                            </div>
                            
                            <div id="loadingTransaksi" class="hidden">
                                <div class="flex items-center justify-center py-6">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                                    <span class="ml-3 text-gray-600">Mencari data...</span>
                                </div>
                            </div>
                            
                            <div id="detailTransaksi" class="hidden">
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-2">
                                                <i class="fas fa-check-circle text-green-600"></i>
                                            </div>
                                            <h3 class="font-semibold text-gray-800">Detail Transaksi Aktif</h3>
                                        </div>
                                        <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full">
                                            <i class="fas fa-clock mr-1"></i>
                                            Parkir
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                            <span class="text-gray-600">Plat Nomor</span>
                                            <span class="font-mono font-bold text-gray-900" id="detail_plat">-</span>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <p class="text-xs text-gray-500">Jenis</p>
                                                <p class="font-medium text-gray-800" id="detail_jenis">-</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Warna</p>
                                                <p class="font-medium text-gray-800" id="detail_warna">-</p>
                                            </div>
                                            <div class="col-span-2">
                                                <p class="text-xs text-gray-500">Pemilik</p>
                                                <p class="font-medium text-gray-800" id="detail_pemilik">-</p>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-200">
                                            <div>
                                                <p class="text-xs text-gray-500">Waktu Masuk</p>
                                                <p class="text-sm font-medium text-gray-800" id="detail_waktu">-</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Area</p>
                                                <p class="text-sm font-medium text-gray-800" id="detail_area">-</p>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white rounded-lg p-3 mt-2">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm text-gray-600">Durasi Parkir:</span>
                                                <span class="font-semibold text-gray-800 font-mono" id="detail_durasi">00:00:00</span>
                                            </div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm text-gray-600">Tarif / Jam:</span>
                                                <span class="font-semibold text-gray-800" id="detail_tarif">Rp 0</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                                                <span class="font-bold text-gray-700">Total Biaya:</span>
                                                <span class="font-bold text-lg text-green-600" id="detail_biaya">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <form action="../../controllers/c_transaksi.php?aksi=keluar" method="post" id="formKeluar" class="mt-4">
                                    <input type="hidden" name="id_parkir" id="id_parkir_keluar">
                                    <input type="hidden" name="plat_nomor" id="plat_nomor_keluar_hidden">
                                    
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center space-x-2">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Proses Keluar & Cetak Struk</span>
                                    </button>
                                </form>
                            </div>
                            
                            <div id="noTransaksi" class="hidden">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                                    <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-car text-yellow-600 text-2xl"></i>
                                    </div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Kendaraan Tidak Ditemukan</h3>
                                    <p class="text-sm text-gray-600">Tidak ada kendaraan dengan plat tersebut yang sedang parkir</p>
                                </div>
                            </div>
                            
                            <div id="initialTransaksi" class="text-center py-8">
                                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500">Masukkan plat nomor untuk mencari kendaraan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-soft border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-list mr-2 text-primary-500"></i>
                                Daftar Transaksi
                            </h2>
                        </div>
                        
                        <div class="flex items-center space-x-4 mt-3 sm:mt-0">
                            <div class="flex space-x-2 bg-gray-100 p-1 rounded-xl">
                                <a href="?status=aktif" 
                                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $filter_status == 'aktif' ? 'bg-white shadow text-primary-600' : 'text-gray-600 hover:text-gray-800' ?>">
                                    <i class="fas fa-clock mr-1"></i> Aktif 
                                    <span class="ml-1 px-1.5 py-0.5 bg-white text-xs rounded-full <?= $filter_status == 'aktif' ? 'bg-primary-100 text-primary-700' : 'bg-gray-200' ?>">
                                        <?= $kendaraan_aktif ?>
                                    </span>
                                </a>
                                <a href="?status=riwayat" 
                                   class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $filter_status == 'riwayat' ? 'bg-white shadow text-primary-600' : 'text-gray-600 hover:text-gray-800' ?>">
                                    <i class="fas fa-history mr-1"></i> Riwayat
                                </a>
                            </div>
                            
                            <form method="GET" action="" class="relative">
                                <input type="hidden" name="status" value="<?= $filter_status ?>">
                                <input type="text" name="search" placeholder="Cari plat/pemilik..." 
                                       value="<?= htmlspecialchars($current_search) ?>"
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition w-full sm:w-64 text-sm">
                                <i class="fas fa-search absolute left-4 top-3 text-gray-400"></i>
                                <?php if (!empty($current_search)): ?>
                                <a href="?status=<?= $filter_status ?>" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <?php if ($filter_status == 'aktif'): ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Plat Nomor</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kendaraan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pemilik</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Waktu Masuk</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Area</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Durasi</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Estimasi</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($data_transaksi_aktif)): 
                                $no = ($current_page - 1) * $limit + 1;
                                foreach ($data_transaksi_aktif as $row): 
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-all duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono font-semibold text-gray-900"><?= htmlspecialchars($row->plat_nomor) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-900"><?= ucfirst($row->jenis_kendaraan) ?></span>
                                        <span class="text-gray-500 block text-xs"><?= htmlspecialchars($row->warna) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row->pemilik) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($row->waktu_masuk)) ?></div>
                                    <div class="text-xs text-gray-500"><?= date('H:i:s', strtotime($row->waktu_masuk)) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row->nama_area) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 font-mono" 
                                          id="durasi_<?= $row->id_parkir ?>" 
                                          data-waktu="<?= $row->waktu_masuk_unix ?? strtotime($row->waktu_masuk) ?>">
                                        <?= $row->durasi_format ?? '00:00:00' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-green-600">Rp <?= number_format($row->estimasi_biaya, 0, ',', '.') ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="prosesKeluar('<?= $row->plat_nomor ?>')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 text-green-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm">
                                        <i class="fas fa-sign-out-alt mr-1.5"></i> Keluar
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-car text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Tidak Ada Transaksi Aktif</h3>
                                        <p class="text-gray-500">Belum ada kendaraan yang parkir saat ini</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <?php else: ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Plat Nomor</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kendaraan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pemilik</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Masuk</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Keluar</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Durasi</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Biaya</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Petugas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Struk</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($data_riwayat)): 
                                $no = ($current_page - 1) * $limit + 1;
                                foreach ($data_riwayat as $row): 
                            ?>
                            <tr class="hover:bg-gray-50/50 transition-all duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono font-semibold text-gray-900"><?= htmlspecialchars($row->plat_nomor) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-900"><?= ucfirst($row->jenis_kendaraan) ?></span>
                                        <span class="text-gray-500 block text-xs"><?= htmlspecialchars($row->warna) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row->pemilik) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($row->waktu_masuk)) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($row->waktu_keluar)) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                    <?php 
                                    if (!empty($row->durasi_detik)) {
                                        $jam = floor($row->durasi_detik / 3600);
                                        $menit = floor(($row->durasi_detik % 3600) / 60);
                                        $detik = $row->durasi_detik % 60;
                                        echo sprintf("%02d:%02d:%02d", $jam, $menit, $detik);
                                    } else {
                                        echo $row->durasi_jam . ':00:00';
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900">Rp <?= number_format($row->biaya_total, 0, ',', '.') ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row->petugas ?? '-') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="cetak_struk.php?id_parkir=<?= $row->id_parkir ?>" target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-all duration-200 hover:shadow-sm">
                                        <i class="fas fa-print mr-1.5"></i> Cetak
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-history text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Riwayat Transaksi</h3>
                                        <p class="text-gray-500">Riwayat transaksi akan muncul setelah ada kendaraan keluar</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <?php if ($total_halaman > 1): ?>
                <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Menampilkan 
                        <span class="font-semibold"><?= (($current_page - 1) * $limit) + 1 ?></span>
                        -
                        <span class="font-semibold"><?= min($current_page * $limit, $total_data) ?></span>
                        dari 
                        <span class="font-semibold"><?= $total_data ?></span> 
                        hasil
                    </div>
                    <div class="flex items-center space-x-2">
                        <?php if ($current_page > 1): ?>
                        <a href="?page=<?= $current_page - 1 ?>&status=<?= $filter_status ?>&search=<?= urlencode($current_search) ?>"
                           class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                            <i class="fas fa-chevron-left text-gray-600"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php 
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($start_page + 4, $total_halaman);
                        for ($i = $start_page; $i <= $end_page; $i++): 
                        ?>
                        <a href="?page=<?= $i ?>&status=<?= $filter_status ?>&search=<?= urlencode($current_search) ?>"
                           class="w-8 h-8 flex items-center justify-center rounded-lg border <?= $i == $current_page ? 'bg-primary-500 text-white border-primary-500' : 'border-gray-300 hover:bg-gray-50' ?> transition font-medium">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $total_halaman): ?>
                        <a href="?page=<?= $current_page + 1 ?>&status=<?= $filter_status ?>&search=<?= urlencode($current_search) ?>"
                           class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                            <i class="fas fa-chevron-right text-gray-600"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<div id="sidebarOverlay" class="fixed inset-0 bg-black opacity-0 invisible transition-opacity duration-300 z-20" onclick="toggleSidebar()"></div>

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

function updateDurasiRealtime() {
    const durasiElements = document.querySelectorAll('[id^="durasi_"]');
    durasiElements.forEach(el => {
        const waktuMasuk = el.dataset.waktu;
        if (waktuMasuk) {
            const sekarang = Math.floor(Date.now() / 1000);
            const selisih = sekarang - parseInt(waktuMasuk);
            
            if (selisih > 0) {
                const jam = Math.floor(selisih / 3600);
                const menit = Math.floor((selisih % 3600) / 60);
                const detik = selisih % 60;
                
                el.innerHTML = `${jam.toString().padStart(2, '0')}:${menit.toString().padStart(2, '0')}:${detik.toString().padStart(2, '0')}`;
            }
        }
    });
}

setInterval(updateDurasiRealtime, 1000);

let searchTimeout;
let searchTransaksiTimeout;

function cariKendaraan(plat) {
    clearTimeout(searchTimeout);
    
    const suggestBox = document.getElementById('suggestKendaraan');
    const dataTerdaftar = document.getElementById('dataKendaraanTerdaftar');
    const formBaru = document.getElementById('formKendaraanBaru');
    
    if (plat.length < 2) {
        suggestBox.classList.add('hidden');
        dataTerdaftar.style.display = 'none';
        formBaru.style.display = 'block';
        
        document.getElementById('jenis_kendaraan_select').disabled = false;
        document.getElementById('warna_input').disabled = false;
        document.getElementById('pemilik_input').disabled = false;
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`../../controllers/c_transaksi.php?aksi=cari_kendaraan&plat_nomor=${encodeURIComponent(plat)}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('display_jenis').innerHTML = ucfirst(data.jenis_kendaraan);
                    document.getElementById('jenis_kendaraan_terdaftar').value = data.jenis_kendaraan;
                    document.getElementById('display_warna').innerHTML = data.warna;
                    document.getElementById('warna_terdaftar').value = data.warna;
                    document.getElementById('display_pemilik').innerHTML = data.pemilik;
                    document.getElementById('pemilik_terdaftar').value = data.pemilik;
                    
                    dataTerdaftar.style.display = 'block';
                    formBaru.style.display = 'none';
                    
                    document.getElementById('jenis_kendaraan_select').disabled = true;
                    document.getElementById('warna_input').disabled = true;
                    document.getElementById('pemilik_input').disabled = true;
                    
                    suggestBox.classList.add('hidden');
                } else {
                    dataTerdaftar.style.display = 'none';
                    formBaru.style.display = 'block';
                    
                    document.getElementById('jenis_kendaraan_select').disabled = false;
                    document.getElementById('warna_input').disabled = false;
                    document.getElementById('pemilik_input').disabled = false;
                }
            })
            .catch(error => console.error('Error:', error));
    }, 500);
}

function cariTransaksiAktif(plat) {
    clearTimeout(searchTransaksiTimeout);
    
    const loading = document.getElementById('loadingTransaksi');
    const detail = document.getElementById('detailTransaksi');
    const noTransaksi = document.getElementById('noTransaksi');
    const initial = document.getElementById('initialTransaksi');
    const suggestBox = document.getElementById('suggestTransaksi');
    
    if (plat.length < 2) {
        if (loading) loading.classList.add('hidden');
        if (detail) detail.classList.add('hidden');
        if (noTransaksi) noTransaksi.classList.add('hidden');
        if (initial) initial.classList.remove('hidden');
        if (suggestBox) suggestBox.classList.add('hidden');
        return;
    }
    
    if (loading) loading.classList.remove('hidden');
    if (detail) detail.classList.add('hidden');
    if (noTransaksi) noTransaksi.classList.add('hidden');
    if (initial) initial.classList.add('hidden');
    if (suggestBox) suggestBox.classList.add('hidden');
    
    searchTransaksiTimeout = setTimeout(() => {
        fetch(`../../controllers/c_transaksi.php?aksi=cari_transaksi_aktif&plat_nomor=${encodeURIComponent(plat)}`)
            .then(response => response.json())
            .then(data => {
                if (loading) loading.classList.add('hidden');
                
                if (data) {
                    document.getElementById('detail_plat').innerHTML = data.plat_nomor;
                    document.getElementById('detail_jenis').innerHTML = ucfirst(data.jenis_kendaraan);
                    document.getElementById('detail_warna').innerHTML = data.warna || '-';
                    document.getElementById('detail_pemilik').innerHTML = data.pemilik || '-';
                    document.getElementById('detail_waktu').innerHTML = data.waktu_masuk;
                    document.getElementById('detail_area').innerHTML = data.area;
                    document.getElementById('detail_durasi').innerHTML = data.durasi_format;
                    document.getElementById('detail_tarif').innerHTML = 'Rp ' + formatRupiah(data.tarif_per_jam);
                    document.getElementById('detail_biaya').innerHTML = 'Rp ' + formatRupiah(data.biaya);
                    
                    document.getElementById('id_parkir_keluar').value = data.id_parkir;
                    document.getElementById('plat_nomor_keluar_hidden').value = data.plat_nomor;
                    
                    if (detail) detail.classList.remove('hidden');
                    if (noTransaksi) noTransaksi.classList.add('hidden');
                    if (initial) initial.classList.add('hidden');
                } else {
                    if (detail) detail.classList.add('hidden');
                    if (noTransaksi) noTransaksi.classList.remove('hidden');
                    if (initial) initial.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (loading) loading.classList.add('hidden');
                if (detail) detail.classList.add('hidden');
                if (noTransaksi) noTransaksi.classList.remove('hidden');
                if (initial) initial.classList.add('hidden');
            });
    }, 500);
}

function prosesKeluar(plat) {
    document.getElementById('plat_nomor_keluar').value = plat;
    cariTransaksiAktif(plat);
    
    document.getElementById('plat_nomor_keluar').scrollIntoView({ 
        behavior: 'smooth',
        block: 'center'
    });
}

function ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatRupiah(angka) {
    if (!angka) return '0';
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

document.addEventListener('DOMContentLoaded', function() {
    const platMasuk = document.getElementById('plat_nomor_masuk');
    if (platMasuk) {
        platMasuk.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    const platKeluar = document.getElementById('plat_nomor_keluar');
    if (platKeluar) {
        platKeluar.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(window.searchSubmitTimeout);
            window.searchSubmitTimeout = setTimeout(() => {
                if (this.value.length >= 0) {
                    this.form.submit();
                }
            }, 800);
        });
    }
});

document.getElementById('formKeluar')?.addEventListener('submit', function(e) {
    if (!confirm('Proses keluar kendaraan dan cetak struk?')) {
        e.preventDefault();
    }
});

window.addEventListener('pageshow', function() {
    const formMasuk = document.getElementById('formMasuk');
    if (formMasuk) {
        formMasuk.reset();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && sidebarVisible) {
        toggleSidebar();
    }
});
</script>

<?php include '../templates/footer.php'; ?>