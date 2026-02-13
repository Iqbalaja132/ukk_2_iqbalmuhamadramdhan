<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../../controllers/c_transaksi.php';

$id_parkir = $_GET['id_parkir'] ?? 0;

if (!$id_parkir) {
    echo "<script>
        alert('ID transaksi tidak ditemukan!');
        window.location.href = 'transaksi.php';
    </script>";
    exit;
}

$transaksi = $transaksi_obj->tampil_data_byid($id_parkir);

if (!$transaksi) {
    echo "<script>
        alert('Data transaksi tidak ditemukan!');
        window.location.href = 'transaksi.php';
    </script>";
    exit;
}

$petugas = $_SESSION['data']['nama_lengkap'] ?? 'Petugas';

$tgl_masuk = date('d/m/Y', strtotime($transaksi->waktu_masuk));
$jam_masuk = date('H:i:s', strtotime($transaksi->waktu_masuk));
$tgl_keluar = $transaksi->waktu_keluar ? date('d/m/Y', strtotime($transaksi->waktu_keluar)) : '-';
$jam_keluar = $transaksi->waktu_keluar ? date('H:i:s', strtotime($transaksi->waktu_keluar)) : '-';

$durasi_detik = $transaksi->durasi_detik ?? 0;
if ($durasi_detik > 0) {
    $jam_durasi = floor($durasi_detik / 3600);
    $menit_durasi = floor(($durasi_detik % 3600) / 60);
    $detik_durasi = $durasi_detik % 60;
    $durasi_format = sprintf("%02d:%02d:%02d", $jam_durasi, $menit_durasi, $detik_durasi);
} else {
    $durasi_format = $transaksi->durasi_jam . ':00:00';
}

$total_menit = floor($durasi_detik / 60);
$sisa_detik = $durasi_detik % 60;
$total_jam = floor($total_menit / 60);
$sisa_menit = $total_menit % 60;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk Parkir - <?= $transaksi->plat_nomor ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-area {
                box-shadow: none !important;
                border: none !important;
            }
            .bg-print-black {
                background-color: #1e293b !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-mono p-4 md:p-8 print:p-0 print:bg-white">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-6 print:shadow-none print:border-0 print-area">
            <div class="text-center border-b-2 border-dashed border-gray-300 pb-4 mb-4">
                <div class="flex justify-center items-center space-x-2 mb-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">P</span>
                    </div>
                    <h1 class="text-3xl font-bold text-blue-600">Park<span class="text-gray-800">Smart</span></h1>
                </div>
                <p class="text-sm text-gray-600">Sistem Parkir Premium</p>
                <p class="text-xs text-gray-500 mt-1">Jl. Parkir No. 1, Jakarta</p>
                <p class="text-xs text-gray-500">Telp: (021) 1234-5678</p>
            </div>
            
            <div class="text-center mb-4">
                <span class="bg-gray-900 text-white px-6 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                    Bukti Transaksi Parkir
                </span>
            </div>
            
            <div class="bg-blue-50 rounded-xl p-4 mb-4 border border-blue-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-semibold text-gray-700">No. Transaksi</span>
                    <span class="text-lg font-bold text-blue-600 font-mono">#<?= str_pad($transaksi->id_parkir, 6, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Tanggal Cetak</span>
                    <span class="text-sm text-gray-800"><?= date('d/m/Y H:i:s') ?></span>
                </div>
            </div>
            
            <div class="bg-gray-900 rounded-xl p-4 mb-4 text-center bg-print-black">
                <span class="text-white text-2xl md:text-3xl font-bold tracking-widest font-mono">
                    <?= htmlspecialchars($transaksi->plat_nomor) ?>
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Kendaraan
                    </h3>
                    <div class="space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">Jenis</span>
                            <span class="text-xs font-semibold text-gray-800"><?= ucfirst($transaksi->jenis_kendaraan) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">Warna</span>
                            <span class="text-xs font-semibold text-gray-800"><?= htmlspecialchars($transaksi->warna ?? '-') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">Pemilik</span>
                            <span class="text-xs font-semibold text-gray-800 truncate max-w-[120px]"><?= htmlspecialchars($transaksi->pemilik ?? '-') ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Waktu
                    </h3>
                    <div class="space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">Masuk</span>
                            <span class="text-xs font-semibold text-gray-800"><?= $jam_masuk ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">Keluar</span>
                            <span class="text-xs font-semibold text-gray-800"><?= $jam_keluar ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">Tanggal</span>
                            <span class="text-xs font-semibold text-gray-800"><?= $tgl_masuk ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 mb-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <span class="text-xs text-gray-600 block">Area Parkir</span>
                        <span class="text-base font-bold text-gray-800"><?= htmlspecialchars($transaksi->nama_area) ?></span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-600 block">Durasi</span>
                        <span class="text-base font-bold text-blue-600 font-mono"><?= $durasi_format ?></span>
                        <span class="text-xs text-gray-500 block">
                            <?php if ($total_jam > 0): ?>
                                <?= $total_jam ?> jam <?= $sisa_menit ?> menit <?= $sisa_detik ?> detik
                            <?php else: ?>
                                <?= $sisa_menit ?> menit <?= $sisa_detik ?> detik
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200 mb-4">
                <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Rincian Biaya
                </h3>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Tarif per Jam</span>
                        <span class="text-sm font-semibold text-gray-800">Rp <?= number_format($transaksi->tarif_per_jam, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Durasi Parkir</span>
                        <span class="text-sm font-semibold text-gray-800">
                            <?php
                            $menit = $durasi_detik / 60;
                            if ($menit < 15) {
                                echo '< 15 menit';
                            } elseif ($menit < 60) {
                                echo floor($menit) . ' menit';
                            } else {
                                echo floor($menit / 60) . ' jam ' . floor($menit % 60) . ' menit';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="border-t border-green-200 my-2 pt-2">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-gray-800">TOTAL BIAYA</span>
                            <span class="text-2xl font-bold text-green-600">Rp <?= number_format($transaksi->biaya_total, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between items-center text-xs text-gray-500 mb-4">
                <span>Petugas: <?= htmlspecialchars($petugas) ?></span>
                <span>#<?= str_pad($transaksi->id_parkir, 6, '0', STR_PAD_LEFT) ?></span>
            </div>
            
            <div class="bg-gray-100 rounded-lg p-3 mb-4 text-center font-mono text-xs text-gray-600 border border-gray-300">
                <div class="flex justify-center items-center space-x-2 mb-1">
                    <span class="text-gray-500">⬛⬛⬛⬛⬛⬛⬛⬛</span>
                </div>
                <div class="flex justify-center items-center space-x-2 mb-1">
                    <span class="text-gray-500">⬛⬜⬜⬜⬜⬜⬜⬛</span>
                </div>
                <div class="flex justify-center items-center space-x-2 mb-1">
                    <span class="text-gray-500">⬛⬜⬛⬛⬛⬜⬜⬛</span>
                </div>
                <div class="flex justify-center items-center space-x-2 mb-1">
                    <span class="text-gray-500">⬛⬜⬛⬜⬛⬜⬛⬛</span>
                </div>
                <div class="text-gray-800 font-bold mt-1"><?= $transaksi->plat_nomor ?></div>
                <div class="text-xs mt-1">ID: <?= str_pad($transaksi->id_parkir, 6, '0', STR_PAD_LEFT) ?></div>
            </div>
            
            <div class="text-center border-t-2 border-dashed border-gray-300 pt-4 mt-2">
                <p class="text-sm font-semibold text-gray-800">Terima kasih telah menggunakan layanan Park Smart</p>
                <p class="text-xs text-gray-500 mt-1">Simpan struk ini sebagai bukti transaksi yang sah</p>
                <p class="text-xs text-gray-400 mt-2">© <?= date('Y') ?> Park Smart - Sistem Parkir Premium</p>
            </div>
            
            <div class="flex flex-col space-y-2 mt-6 no-print">
                <button onclick="window.print()" 
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak Struk</span>
                </button>
                
                <a href="transaksi.php" 
                   class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Transaksi</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>