<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'owner') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../../controllers/c_dashboard_owner.php';

include '../templates/header.php';
?>

<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
                            <i class="fas fa-chart-line mr-2 text-primary-500"></i>
                            Dashboard Owner
                        </h1>
                        <p class="text-sm text-gray-600">Selamat datang, <?= htmlspecialchars($_SESSION['data']['nama_lengkap']) ?></p>
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
        </header>

        <main class="p-6">
            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm mb-1">Total Pendapatan</p>
                            <p class="text-3xl font-bold">Rp <?= number_format($total_pendapatan_bulan_ini, 0, ',', '.') ?></p>
                            <p class="text-xs text-blue-100 mt-2">Bulan Ini</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm mb-1">Total Transaksi</p>
                            <p class="text-3xl font-bold"><?= number_format($total_transaksi_bulan_ini) ?></p>
                            <p class="text-xs text-green-100 mt-2">Bulan Ini</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm mb-1">Kendaraan Unik</p>
                            <p class="text-3xl font-bold"><?= number_format($total_kendaraan_unik) ?></p>
                            <p class="text-xs text-purple-100 mt-2">Total Keseluruhan</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-car text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-lg stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm mb-1">Rata-rata per Transaksi</p>
                            <p class="text-2xl font-bold">Rp <?= number_format($rata_rata_transaksi, 0, ',', '.') ?></p>
                            <p class="text-xs text-amber-100 mt-2">Bulan Ini</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-chart-bar mr-2 text-primary-500"></i>
                            Pendapatan 7 Hari Terakhir
                        </h2>
                        <select id="chartPeriod" class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                            <option value="7">7 Hari</option>
                            <option value="30">30 Hari</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-chart-pie mr-2 text-primary-500"></i>
                            Jenis Kendaraan
                        </h2>
                    </div>
                    <div class="chart-container">
                        <canvas id="vehicleTypeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-history mr-2 text-primary-500"></i>
                            Transaksi Terbaru
                        </h2>
                        <a href="rekap.php" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Plat Nomor</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Area</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Durasi</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Biaya</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Petugas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($transaksi_terbaru)): ?>
                                <?php foreach ($transaksi_terbaru as $row): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('d/m/Y H:i', strtotime($row->waktu_keluar)) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono font-semibold text-gray-900"><?= htmlspecialchars($row->plat_nomor) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= ucfirst($row->jenis_kendaraan) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($row->nama_area) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                        <?php 
                                        if (!empty($row->durasi_detik)) {
                                            $jam = floor($row->durasi_detik / 3600);
                                            $menit = floor(($row->durasi_detik % 3600) / 60);
                                            echo sprintf("%02d:%02d", $jam, $menit);
                                        } else {
                                            echo $row->durasi_jam . ':00';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-gray-900">Rp <?= number_format($row->biaya_total, 0, ',', '.') ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($row->petugas ?? '-') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-receipt text-gray-400 text-4xl mb-3"></i>
                                            <p class="text-gray-500">Belum ada transaksi</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <a href="rekap.php?periode=harian" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100 hover:shadow-md transition group">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mr-4 group-hover:scale-110 transition">
                            <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Laporan Harian</h3>
                            <p class="text-sm text-gray-600">Lihat laporan per hari</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-blue-500 transition"></i>
                    </div>
                </a>
                
                <a href="rekap.php?periode=bulanan" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100 hover:shadow-md transition group">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mr-4 group-hover:scale-110 transition">
                            <i class="fas fa-calendar-alt text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Laporan Bulanan</h3>
                            <p class="text-sm text-gray-600">Lihat laporan per bulan</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-green-500 transition"></i>
                    </div>
                </a>
                
                <a href="rekap.php?periode=tahunan" class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100 hover:shadow-md transition group">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center mr-4 group-hover:scale-110 transition">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Laporan Tahunan</h3>
                            <p class="text-sm text-gray-600">Lihat laporan per tahun</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-purple-500 transition"></i>
                    </div>
                </a>
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

// Revenue Chart
let revenueChart;
const revenueData = <?= json_encode($revenue_data) ?>;

function updateRevenueChart(days) {
    let labels = [];
    let data = [];
    
    if (days === 7) {
        labels = <?= json_encode($revenue_labels_7) ?>;
        data = <?= json_encode($revenue_values_7) ?>;
    } else {
        labels = <?= json_encode($revenue_labels_30) ?>;
        data = <?= json_encode($revenue_values_30) ?>;
    }
    
    if (revenueChart) {
        revenueChart.destroy();
    }
    
    const ctx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: data,
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
                legend: {
                    position: 'top',
                },
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
}

document.getElementById('chartPeriod').addEventListener('change', function(e) {
    updateRevenueChart(e.target.value);
});

// Vehicle Type Chart
const vehicleTypeCtx = document.getElementById('vehicleTypeChart').getContext('2d');
new Chart(vehicleTypeCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($vehicle_type_labels) ?>,
        datasets: [{
            data: <?= json_encode($vehicle_type_values) ?>,
            backgroundColor: [
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(245, 158, 11)'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && sidebarVisible) {
        toggleSidebar();
    }
});
</script>

<?php include '../templates/footer.php'; ?>