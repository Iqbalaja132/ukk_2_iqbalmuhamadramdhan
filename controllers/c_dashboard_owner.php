<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../models/m_rekap.php';
include_once __DIR__ . '/../models/m_logaktivitas.php';

$rekap_obj = new rekap();
$log_obj = new logaktivitas();

// Ambil koneksi melalui method getter
$conn = $rekap_obj->getConnection();

$total_pendapatan_bulan_ini = 0;
$total_transaksi_bulan_ini = 0;
$total_kendaraan_unik = 0;
$rata_rata_transaksi = 0;

$revenue_data = [];
$revenue_labels_7 = [];
$revenue_values_7 = [];
$revenue_labels_30 = [];
$revenue_values_30 = [];

$vehicle_type_labels = [];
$vehicle_type_values = [];

$transaksi_terbaru = [];

try {
    // Cek login
    if (!isset($_SESSION['data']['id_user']) || $_SESSION['data']['role'] !== 'owner') {
        echo "<script>
            alert('Anda harus login sebagai owner terlebih dahulu!');
            window.location.href = '../login.php';
        </script>";
        exit;
    }
    
    // Total pendapatan bulan ini
    $tanggal_mulai_bulan = date('Y-m-01');
    $tanggal_akhir_bulan = date('Y-m-t');
    $total_pendapatan_bulan_ini = $rekap_obj->total_pendapatan_periode($tanggal_mulai_bulan, $tanggal_akhir_bulan);
    $total_transaksi_bulan_ini = $rekap_obj->total_transaksi_periode($tanggal_mulai_bulan, $tanggal_akhir_bulan);
    $rata_rata_transaksi = $total_transaksi_bulan_ini > 0 ? $total_pendapatan_bulan_ini / $total_transaksi_bulan_ini : 0;
    
    // Total kendaraan unik (sepanjang masa)
    $sql = "SELECT COUNT(DISTINCT id_kendaraan) as total FROM tb_transaksi";
    $query = mysqli_query($conn, $sql); // Gunakan $conn yang sudah didapatkan
    $result = mysqli_fetch_assoc($query);
    $total_kendaraan_unik = $result['total'] ?? 0;
    
    // Data revenue 7 hari terakhir
    for ($i = 6; $i >= 0; $i--) {
        $tanggal = date('Y-m-d', strtotime("-$i days"));
        $revenue_labels_7[] = date('d/m', strtotime($tanggal));
        $pendapatan = $rekap_obj->total_pendapatan_periode($tanggal, $tanggal);
        $revenue_values_7[] = $pendapatan;
    }
    
    // Data revenue 30 hari terakhir
    for ($i = 29; $i >= 0; $i--) {
        $tanggal = date('Y-m-d', strtotime("-$i days"));
        $revenue_labels_30[] = date('d/m', strtotime($tanggal));
        $pendapatan = $rekap_obj->total_pendapatan_periode($tanggal, $tanggal);
        $revenue_values_30[] = $pendapatan;
    }
    
    // Jenis kendaraan (bulan ini)
    $sql_jenis = "SELECT 
                        k.jenis_kendaraan,
                        COUNT(*) as jumlah
                    FROM tb_transaksi t
                    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                    WHERE t.status = 'keluar'
                    AND DATE(t.waktu_keluar) BETWEEN '$tanggal_mulai_bulan' AND '$tanggal_akhir_bulan'
                    GROUP BY k.jenis_kendaraan
                    ORDER BY jumlah DESC";
    
    $query_jenis = mysqli_query($conn, $sql_jenis); // Gunakan $conn yang sudah didapatkan
    while ($row = mysqli_fetch_object($query_jenis)) {
        $vehicle_type_labels[] = ucfirst($row->jenis_kendaraan);
        $vehicle_type_values[] = $row->jumlah;
    }
    
    // Transaksi terbaru (10 terakhir)
    $sql_terbaru = "SELECT t.*, 
                           k.plat_nomor, k.jenis_kendaraan,
                           a.nama_area,
                           u.nama_lengkap as petugas
                    FROM tb_transaksi t
                    JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                    JOIN tb_area_parkir a ON t.id_area = a.id_area
                    LEFT JOIN tb_user u ON t.id_user = u.id_user
                    WHERE t.status = 'keluar'
                    ORDER BY t.waktu_keluar DESC
                    LIMIT 10";
    
    $query_terbaru = mysqli_query($conn, $sql_terbaru); // Gunakan $conn yang sudah didapatkan
    while ($row = mysqli_fetch_object($query_terbaru)) {
        if ($row->durasi_detik) {
            $jam = floor($row->durasi_detik / 3600);
            $menit = floor(($row->durasi_detik % 3600) / 60);
            $row->durasi_format = sprintf("%02d:%02d", $jam, $menit);
        } else {
            $row->durasi_format = $row->durasi_jam . ':00';
        }
        $transaksi_terbaru[] = $row;
    }
    
    // Log aktivitas
    $log_obj->tambah_log($_SESSION['data']['id_user'], "Melihat dashboard owner");
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: {$e->getMessage()}</div>";
}
?>