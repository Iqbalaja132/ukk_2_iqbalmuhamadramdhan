<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'owner') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../../controllers/c_rekap.php';

include '../templates/header.php';

$periode = $_GET['periode'] ?? 'harian';
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-d');
$tanggal_selesai = $_GET['tanggal_selesai'] ?? date('Y-m-d');
$bulan = $_GET['bulan'] ?? date('Y-m');
$tahun = $_GET['tahun'] ?? date('Y');
$current_page = $current_page ?? 1;
$total_halaman = $total_halaman ?? 0;
$total_data = $total_data ?? 0;
$current_search = $current_search ?? '';
?>

<style>
    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .chart-container {
        position: relative;
        height: 300px;
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
                            <i class="fas fa-chart-bar mr-2 text-primary-500"></i>
                            Laporan & Rekap
                        </h1>
                        <p class="text-sm text-gray-600">Lihat laporan pendapatan dan transaksi parkir</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center bg-blue-50 px-4 py-2 rounded-xl">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700"><?= date('d F Y') ?></span>
                    </div>
                    <a href="?periode=<?= $periode ?>&tanggal=<?= $tanggal ?>&tanggal_mulai=<?= $tanggal_mulai ?>&tanggal_selesai=<?= $tanggal_selesai ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&aksi=export_excel"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition flex items-center">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </a>
                </div>
            </div>
        </header>

        <main class="p-6">
            <!-- Period Tabs -->
            <div class="mb-6">
                <div class="flex space-x-2 bg-white p-1 rounded-xl shadow-sm border border-gray-200 w-fit flex-wrap">
                    <a href="?periode=rentang"
                        class="px-6 py-2.5 rounded-lg text-sm font-medium transition <?= $periode == 'rentang' ? 'bg-primary-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="fas fa-calendar-range mr-2"></i> Rentang Tanggal
                    </a>
                    <a href="?periode=harian"
                        class="px-6 py-2.5 rounded-lg text-sm font-medium transition <?= $periode == 'harian' ? 'bg-primary-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="fas fa-calendar-day mr-2"></i> Harian
                    </a>
                    <a href="?periode=bulanan"
                        class="px-6 py-2.5 rounded-lg text-sm font-medium transition <?= $periode == 'bulanan' ? 'bg-primary-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="fas fa-calendar-alt mr-2"></i> Bulanan
                    </a>
                    <a href="?periode=tahunan"
                        class="px-6 py-2.5 rounded-lg text-sm font-medium transition <?= $periode == 'tahunan' ? 'bg-primary-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="fas fa-chart-line mr-2"></i> Tahunan
                    </a>
                </div>
            </div>

            <!-- Period Filter -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6 mb-6">
                <?php if ($periode == 'harian'): ?>
                    <form method="GET" action="" class="flex flex-col sm:flex-row gap-4 items-end">
                        <input type="hidden" name="periode" value="harian">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-1 text-primary-500"></i> Pilih Tanggal
                            </label>
                            <input type="date" name="tanggal" value="<?= $tanggal ?>"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition">
                        </div>
                        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2.5 rounded-xl font-medium transition">
                            <i class="fas fa-search mr-2"></i> Tampilkan
                        </button>
                    </form>

                <?php elseif ($periode == 'rentang'): ?>
                    <form method="GET" action="" class="flex flex-col sm:flex-row gap-4 items-end">
                        <input type="hidden" name="periode" value="rentang">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-start mr-1 text-primary-500"></i> Tanggal Mulai
                            </label>
                            <input type="date" name="tanggal_mulai" value="<?= $tanggal_mulai ?>"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-end mr-1 text-primary-500"></i> Tanggal Selesai
                            </label>
                            <input type="date" name="tanggal_selesai" value="<?= $tanggal_selesai ?>"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition">
                        </div>
                        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2.5 rounded-xl font-medium transition">
                            <i class="fas fa-search mr-2"></i> Tampilkan
                        </button>
                    </form>

                <?php elseif ($periode == 'bulanan'): ?>
                    <form method="GET" action="" class="flex flex-col sm:flex-row gap-4 items-end">
                        <input type="hidden" name="periode" value="bulanan">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1 text-primary-500"></i> Pilih Bulan
                            </label>
                            <input type="month" name="bulan" value="<?= $bulan ?>"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition">
                        </div>
                        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2.5 rounded-xl font-medium transition">
                            <i class="fas fa-search mr-2"></i> Tampilkan
                        </button>
                    </form>

                <?php else: ?>
                    <form method="GET" action="" class="flex flex-col sm:flex-row gap-4 items-end">
                        <input type="hidden" name="periode" value="tahunan">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-year mr-1 text-primary-500"></i> Pilih Tahun
                            </label>
                            <select name="tahun" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition">
                                <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                                    <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2.5 rounded-xl font-medium transition">
                            <i class="fas fa-search mr-2"></i> Tampilkan
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white shadow-lg stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Total Pendapatan</p>
                            <p class="text-2xl font-bold mt-1">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white/30 flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-5 text-white shadow-lg stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Total Transaksi</p>
                            <p class="text-2xl font-bold mt-1"><?= number_format($total_transaksi) ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white/30 flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 text-white shadow-lg stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Rata-rata per Transaksi</p>
                            <p class="text-xl font-bold mt-1">Rp <?= number_format($rata_rata_per_transaksi, 0, ',', '.') ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white/30 flex items-center justify-center">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-5 text-white shadow-lg stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm">Kendaraan Unik</p>
                            <p class="text-2xl font-bold mt-1"><?= number_format($total_kendaraan_unik) ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white/30 flex items-center justify-center">
                            <i class="fas fa-car text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <?php if ($periode == 'harian'): ?>
                <!-- Harian Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-line mr-2 text-primary-500"></i>
                            Pendapatan Per Jam
                        </h2>
                        <div class="chart-container">
                            <canvas id="hourlyChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-pie mr-2 text-primary-500"></i>
                            Statistik Transaksi
                        </h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Transaksi</span>
                                <span class="font-semibold text-gray-800"><?= number_format($total_transaksi) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Transaksi Minimal</span>
                                <span class="font-semibold text-gray-800">Rp <?= number_format($data_rekap_harian->minimal ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Transaksi Maksimal</span>
                                <span class="font-semibold text-gray-800">Rp <?= number_format($data_rekap_harian->maksimal ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Rata-rata</span>
                                <span class="font-semibold text-gray-800">Rp <?= number_format($rata_rata_per_transaksi, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($periode == 'rentang'): ?>
                <!-- Rentang Tanggal Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-line mr-2 text-primary-500"></i>
                            Pendapatan Per Hari
                        </h2>
                        <div class="chart-container">
                            <canvas id="dailyRangeChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-pie mr-2 text-primary-500"></i>
                            Jenis Kendaraan
                        </h2>
                        <div class="chart-container">
                            <canvas id="vehicleTypeRangeChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-bar mr-2 text-primary-500"></i>
                            Area Parkir
                        </h2>
                        <div class="chart-container">
                            <canvas id="areaRangeChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-clock mr-2 text-primary-500"></i>
                            Jam Sibuk
                        </h2>
                        <div class="space-y-3">
                            <?php if (!empty($data_rekap_jam_sibuk)): ?>
                                <?php foreach ($data_rekap_jam_sibuk as $jam): ?>
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">Jam <?= str_pad($jam->jam, 2, '0', STR_PAD_LEFT) ?>:00 - <?= str_pad($jam->jam + 1, 2, '0', STR_PAD_LEFT) ?>:00</span>
                                            <span class="font-semibold text-gray-800"><?= $jam->jumlah_transaksi ?> transaksi</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-primary-500 h-2 rounded-full" style="width: <?= ($jam->jumlah_transaksi / max(array_column($data_rekap_jam_sibuk, 'jumlah_transaksi'))) * 100 ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center text-gray-500 py-8">Belum ada data</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php elseif ($periode == 'bulanan'): ?>
                <!-- Bulanan Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-line mr-2 text-primary-500"></i>
                            Pendapatan Harian
                        </h2>
                        <div class="chart-container">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-pie mr-2 text-primary-500"></i>
                            Jenis Kendaraan
                        </h2>
                        <div class="chart-container">
                            <canvas id="vehicleTypeChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-bar mr-2 text-primary-500"></i>
                            Area Parkir
                        </h2>
                        <div class="chart-container">
                            <canvas id="areaChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-clock mr-2 text-primary-500"></i>
                            Jam Sibuk
                        </h2>
                        <div class="space-y-3">
                            <?php if (!empty($data_rekap_jam_sibuk)): ?>
                                <?php foreach ($data_rekap_jam_sibuk as $jam): ?>
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">Jam <?= str_pad($jam->jam, 2, '0', STR_PAD_LEFT) ?>:00 - <?= str_pad($jam->jam + 1, 2, '0', STR_PAD_LEFT) ?>:00</span>
                                            <span class="font-semibold text-gray-800"><?= $jam->jumlah_transaksi ?> transaksi</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-primary-500 h-2 rounded-full" style="width: <?= ($jam->jumlah_transaksi / max(array_column($data_rekap_jam_sibuk, 'jumlah_transaksi'))) * 100 ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center text-gray-500 py-8">Belum ada data</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Tahunan Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-line mr-2 text-primary-500"></i>
                            Pendapatan Bulanan
                        </h2>
                        <div class="chart-container">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-pie mr-2 text-primary-500"></i>
                            Jenis Kendaraan
                        </h2>
                        <div class="chart-container">
                            <canvas id="vehicleTypeChartYear"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Detail Transactions Table -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-list mr-2 text-primary-500"></i>
                            Detail Transaksi
                        </h2>

                        <form method="GET" action="" class="relative">
                            <input type="hidden" name="periode" value="<?= $periode ?>">
                            <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                            <input type="hidden" name="tanggal_mulai" value="<?= $tanggal_mulai ?>">
                            <input type="hidden" name="tanggal_selesai" value="<?= $tanggal_selesai ?>">
                            <input type="hidden" name="bulan" value="<?= $bulan ?>">
                            <input type="hidden" name="tahun" value="<?= $tahun ?>">
                            <input type="text" name="search" placeholder="Cari plat/pemilik..."
                                value="<?= htmlspecialchars($current_search) ?>"
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition w-full sm:w-64 text-sm">
                            <i class="fas fa-search absolute left-4 top-3 text-gray-400"></i>
                            <?php if (!empty($current_search)): ?>
                                <a href="?periode=<?= $periode ?>&tanggal=<?= $tanggal ?>&tanggal_mulai=<?= $tanggal_mulai ?>&tanggal_selesai=<?= $tanggal_selesai ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">No</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Plat Nomor</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Jenis</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Warna</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Pemilik</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Area</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Masuk</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Keluar</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Durasi</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Biaya</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Petugas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($data_detail)):
                                $no = ($current_page - 1) * $limit + 1;
                                foreach ($data_detail as $row):
                            ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('d/m/Y', strtotime($row->waktu_keluar)) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono font-semibold text-gray-900"><?= htmlspecialchars($row->plat_nomor) ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= ucfirst($row->jenis_kendaraan) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row->warna) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row->pemilik) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row->nama_area) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('H:i:s', strtotime($row->waktu_masuk)) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('H:i:s', strtotime($row->waktu_keluar)) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono"><?= $row->durasi_format ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-semibold text-gray-900">Rp <?= number_format($row->biaya_total, 0, ',', '.') ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($row->petugas ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-receipt text-gray-400 text-4xl mb-3"></i>
                                            <p class="text-gray-500">Belum ada data transaksi</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                                <a href="?page=<?= $current_page - 1 ?>&periode=<?= $periode ?>&tanggal=<?= $tanggal ?>&tanggal_mulai=<?= $tanggal_mulai ?>&tanggal_selesai=<?= $tanggal_selesai ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&search=<?= urlencode($current_search) ?>"
                                    class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                    <i class="fas fa-chevron-left text-gray-600"></i>
                                </a>
                            <?php endif; ?>

                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($start_page + 4, $total_halaman);
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                                <a href="?page=<?= $i ?>&periode=<?= $periode ?>&tanggal=<?= $tanggal ?>&tanggal_mulai=<?= $tanggal_mulai ?>&tanggal_selesai=<?= $tanggal_selesai ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&search=<?= urlencode($current_search) ?>"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border <?= $i == $current_page ? 'bg-primary-500 text-white border-primary-500' : 'border-gray-300 hover:bg-gray-50' ?> transition font-medium">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_halaman): ?>
                                <a href="?page=<?= $current_page + 1 ?>&periode=<?= $periode ?>&tanggal=<?= $tanggal ?>&tanggal_mulai=<?= $tanggal_mulai ?>&tanggal_selesai=<?= $tanggal_selesai ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&search=<?= urlencode($current_search) ?>"
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <?php if ($periode == 'harian'): ?>
        // Hourly Chart
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        const hourlyData = <?= json_encode($data_rekap_harian->per_jam ?? []) ?>;
        const hourlyLabels = hourlyData.map(item => `${String(item.jam).padStart(2, '0')}:00`);
        const hourlyValues = hourlyData.map(item => item.pendapatan);

        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: hourlyValues,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    <?php elseif ($periode == 'rentang'): ?>
        // Daily Range Chart
        const dailyRangeCtx = document.getElementById('dailyRangeChart').getContext('2d');
        const dailyRangeData = <?= json_encode($data_rekap_harian->per_hari ?? []) ?>;
        const dailyRangeLabels = dailyRangeData.map(item => {
            const date = new Date(item.tanggal);
            return date.getDate() + '/' + (date.getMonth() + 1);
        });
        const dailyRangeValues = dailyRangeData.map(item => item.pendapatan);

        new Chart(dailyRangeCtx, {
            type: 'line',
            data: {
                labels: dailyRangeLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: dailyRangeValues,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Vehicle Type Range Chart
        const vehicleRangeCtx = document.getElementById('vehicleTypeRangeChart').getContext('2d');
        const vehicleRangeData = <?= json_encode($data_rekap_jenis_kendaraan) ?>;
        const vehicleRangeLabels = vehicleRangeData.map(item => item.jenis_kendaraan);
        const vehicleRangeValues = vehicleRangeData.map(item => item.jumlah);

        new Chart(vehicleRangeCtx, {
            type: 'pie',
            data: {
                labels: vehicleRangeLabels,
                datasets: [{
                    data: vehicleRangeValues,
                    backgroundColor: ['rgb(59, 130, 246)', 'rgb(16, 185, 129)', 'rgb(245, 158, 11)'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = vehicleRangeValues.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Area Range Chart
        const areaRangeCtx = document.getElementById('areaRangeChart').getContext('2d');
        const areaRangeData = <?= json_encode($data_rekap_area) ?>;
        const areaRangeLabels = areaRangeData.map(item => item.nama_area);
        const areaRangeValues = areaRangeData.map(item => item.jumlah);

        new Chart(areaRangeCtx, {
            type: 'bar',
            data: {
                labels: areaRangeLabels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: areaRangeValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    <?php elseif ($periode == 'bulanan'): ?>
        // Daily Chart
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyData = <?= json_encode($data_rekap_bulanan) ?>;
        const dailyLabels = dailyData.map(item => {
            const date = new Date(item.tanggal);
            return date.getDate() + '/' + (date.getMonth() + 1);
        });
        const dailyValues = dailyData.map(item => item.total_pendapatan);

        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: dailyValues,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Vehicle Type Chart
        const vehicleCtx = document.getElementById('vehicleTypeChart').getContext('2d');
        const vehicleData = <?= json_encode($data_rekap_jenis_kendaraan) ?>;
        const vehicleLabels = vehicleData.map(item => item.jenis_kendaraan);
        const vehicleValues = vehicleData.map(item => item.jumlah);

        new Chart(vehicleCtx, {
            type: 'pie',
            data: {
                labels: vehicleLabels,
                datasets: [{
                    data: vehicleValues,
                    backgroundColor: ['rgb(59, 130, 246)', 'rgb(16, 185, 129)', 'rgb(245, 158, 11)'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = vehicleValues.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Area Chart
        const areaCtx = document.getElementById('areaChart').getContext('2d');
        const areaData = <?= json_encode($data_rekap_area) ?>;
        const areaLabels = areaData.map(item => item.nama_area);
        const areaValues = areaData.map(item => item.jumlah);

        new Chart(areaCtx, {
            type: 'bar',
            data: {
                labels: areaLabels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: areaValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    <?php elseif ($periode == 'tahunan'): ?>
        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = <?= json_encode($data_rekap_tahunan) ?>;
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const monthlyLabels = monthlyData.map(item => monthNames[item.bulan - 1]);
        const monthlyValues = monthlyData.map(item => item.total_pendapatan);

        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: monthlyValues,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Vehicle Type Chart Tahun
        const vehicleYearCtx = document.getElementById('vehicleTypeChartYear').getContext('2d');
        const vehicleYearData = <?= json_encode($data_rekap_jenis_kendaraan) ?>;
        const vehicleYearLabels = vehicleYearData.map(item => item.jenis_kendaraan);
        const vehicleYearValues = vehicleYearData.map(item => item.jumlah);

        new Chart(vehicleYearCtx, {
            type: 'pie',
            data: {
                labels: vehicleYearLabels,
                datasets: [{
                    data: vehicleYearValues,
                    backgroundColor: ['rgb(59, 130, 246)', 'rgb(16, 185, 129)', 'rgb(245, 158, 11)'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = vehicleYearValues.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    <?php endif; ?>

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebarVisible) {
            toggleSidebar();
        }
    });
</script>

<?php include '../templates/footer.php'; ?>