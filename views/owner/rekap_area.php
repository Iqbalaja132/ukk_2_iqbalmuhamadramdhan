<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'owner') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../../models/m_rekap.php';
include_once __DIR__ . '/../../models/m_areaparkir.php';

$rekap_obj = new rekap();
$area_obj = new area();

// Ambil koneksi melalui method getter
$conn = $rekap_obj->getConnection();

$area_list = $area_obj->tampil_data();
$area_utilization = [];

$periode = $_GET['periode'] ?? 'bulanan';
$bulan = $_GET['bulan'] ?? date('Y-m');
$tahun = $_GET['tahun'] ?? date('Y');

// Hitung utilisasi area
foreach ($area_list as $area) {
    if ($periode == 'bulanan') {
        $tanggal_mulai = date('Y-m-01', strtotime($bulan));
        $tanggal_akhir = date('Y-m-t', strtotime($bulan));
        
        $sql = "SELECT COUNT(*) as jumlah, SUM(biaya_total) as pendapatan
                FROM tb_transaksi
                WHERE id_area = {$area->id_area}
                AND status = 'keluar'
                AND DATE(waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
        
        // Gunakan $conn yang sudah didapatkan
        $query = mysqli_query($conn, $sql);
        $data = mysqli_fetch_object($query);
        
        $area_utilization[] = (object)[
            'nama_area' => $area->nama_area,
            'kapasitas' => $area->kapasitas,
            'transaksi' => $data->jumlah ?? 0,
            'pendapatan' => $data->pendapatan ?? 0,
            'okupansi' => $area->terisi,
            'persentase_okupansi' => ($area->terisi / $area->kapasitas) * 100
        ];
    } else {
        $sql = "SELECT 
                    MONTH(waktu_keluar) as bulan,
                    COUNT(*) as jumlah,
                    SUM(biaya_total) as pendapatan
                FROM tb_transaksi
                WHERE id_area = {$area->id_area}
                AND status = 'keluar'
                AND YEAR(waktu_keluar) = '$tahun'
                GROUP BY MONTH(waktu_keluar)";
        
        // Gunakan $conn yang sudah didapatkan
        $query = mysqli_query($conn, $sql);
        $monthly_data = [];
        while ($row = mysqli_fetch_object($query)) {
            $monthly_data[$row->bulan] = $row;
        }
        
        $area_utilization[] = (object)[
            'nama_area' => $area->nama_area,
            'kapasitas' => $area->kapasitas,
            'monthly' => $monthly_data
        ];
    }
}

include '../templates/header.php';
?>

<!-- Rest of your HTML code remains the same -->
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
                            <i class="fas fa-map-marked-alt mr-2 text-primary-500"></i>
                            Rekap Area Parkir
                        </h1>
                        <p class="text-sm text-gray-600">Analisis okupansi dan pendapatan per area</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex items-center bg-blue-50 px-4 py-2 rounded-xl">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700"><?= date('d F Y') ?></span>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            <!-- Filter -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-200 p-6 mb-6">
                <form method="GET" action="" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="flex space-x-2">
                        <a href="?periode=bulanan&bulan=<?= $bulan ?>" 
                           class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $periode == 'bulanan' ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                            Bulanan
                        </a>
                        <a href="?periode=tahunan&tahun=<?= $tahun ?>" 
                           class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $periode == 'tahunan' ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                            Tahunan
                        </a>
                    </div>
                    
                    <?php if ($periode == 'bulanan'): ?>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Bulan</label>
                        <input type="month" name="bulan" value="<?= $bulan ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <?php else: ?>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Tahun</label>
                        <select name="tahun" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-search mr-2"></i> Tampilkan
                    </button>
                </form>
            </div>

            <!-- Area Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php foreach ($area_utilization as $area): ?>
                <div class="bg-white rounded-2xl shadow-soft border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-4">
                        <h3 class="text-white font-semibold text-lg">
                            <i class="fas fa-parking mr-2"></i> <?= htmlspecialchars($area->nama_area) ?>
                        </h3>
                        <p class="text-primary-100 text-sm">Kapasitas: <?= $area->kapasitas ?> slot</p>
                    </div>
                    
                    <div class="p-6">
                        <?php if ($periode == 'bulanan'): ?>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <p class="text-sm text-gray-600 mb-1">Total Transaksi</p>
                                <p class="text-2xl font-bold text-blue-600"><?= number_format($area->transaksi) ?></p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-xl">
                                <p class="text-sm text-gray-600 mb-1">Pendapatan</p>
                                <p class="text-xl font-bold text-green-600">Rp <?= number_format($area->pendapatan, 0, ',', '.') ?></p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Okupansi Saat Ini</p>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Terisi: <?= $area->okupansi ?> / <?= $area->kapasitas ?></span>
                                <span><?= number_format($area->persentase_okupansi, 1) ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-primary-500 h-2.5 rounded-full" style="width: <?= $area->persentase_okupansi ?>%"></div>
                            </div>
                        </div>
                        
                        <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-2 text-sm font-semibold text-gray-700">Bulan</th>
                                        <th class="text-right py-2 text-sm font-semibold text-gray-700">Transaksi</th>
                                        <th class="text-right py-2 text-sm font-semibold text-gray-700">Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                    for ($m = 1; $m <= 12; $m++): 
                                        $data = $area->monthly[$m] ?? null;
                                    ?>
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 text-sm text-gray-800"><?= $monthNames[$m-1] ?></td>
                                        <td class="py-2 text-sm text-right text-gray-800"><?= number_format($data->jumlah ?? 0) ?></td>
                                        <td class="py-2 text-sm text-right text-gray-800">Rp <?= number_format($data->pendapatan ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && sidebarVisible) {
        toggleSidebar();
    }
});
</script>

<?php include '../templates/footer.php'; ?>