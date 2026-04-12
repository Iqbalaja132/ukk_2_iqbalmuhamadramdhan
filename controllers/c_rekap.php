<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../models/m_rekap.php';
include_once __DIR__ . '/../models/m_logaktivitas.php';

$rekap_obj = new rekap();
$log_obj = new logaktivitas();

$data_rekap_harian = [];
$data_rekap_bulanan = [];
$data_rekap_tahunan = [];
$data_rekap_jenis_kendaraan = [];
$data_rekap_area = [];
$data_rekap_jam_sibuk = [];

$total_pendapatan = 0;
$total_transaksi = 0;
$rata_rata_per_transaksi = 0;
$total_kendaraan_unik = 0;

$current_page = 1;
$current_search = '';
$limit = 10;
$total_data = 0;
$total_halaman = 0;

try {
    // Cek login
    if (!isset($_SESSION['data']['id_user']) || $_SESSION['data']['role'] !== 'owner') {
        echo "<script>
            alert('Anda harus login sebagai owner terlebih dahulu!');
            window.location.href = '../login.php';
        </script>";
        exit;
    }

    $aksi = $_GET['aksi'] ?? '';
    
    // Export Excel
    if ($aksi == 'export_excel') {
        $periode = $_GET['periode'] ?? 'harian';
        $tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-d');
        $tanggal_selesai = $_GET['tanggal_selesai'] ?? date('Y-m-d');
        
        $data_export = [];
        
        if ($periode == 'rentang') {
            $data_export = $rekap_obj->laporan_detail_periode($tanggal_mulai, $tanggal_selesai);
            $filename = "laporan_parkir_{$tanggal_mulai}_{$tanggal_selesai}.xls";
        } elseif ($periode == 'harian') {
            $data_export = $rekap_obj->laporan_detail_periode($tanggal_mulai, $tanggal_mulai);
            $filename = "laporan_parkir_{$tanggal_mulai}.xls";
        } elseif ($periode == 'bulanan') {
            $bulan = $_GET['bulan'] ?? date('Y-m');
            $data_export = $rekap_obj->laporan_detail_bulanan($bulan);
            $filename = "laporan_parkir_bulan_{$bulan}.xls";
        } else {
            $tahun = $_GET['tahun'] ?? date('Y');
            $data_export = $rekap_obj->laporan_detail_tahunan($tahun);
            $filename = "laporan_parkir_tahun_{$tahun}.xls";
        }
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>No</th>";
        echo "<th>Tanggal</th>";
        echo "<th>Plat Nomor</th>";
        echo "<th>Jenis Kendaraan</th>";
        echo "<th>Warna</th>";
        echo "<th>Pemilik</th>";
        echo "<th>Area Parkir</th>";
        echo "<th>Waktu Masuk</th>";
        echo "<th>Waktu Keluar</th>";
        echo "<th>Durasi</th>";
        echo "<th>Biaya</th>";
        echo "<th>Petugas</th>";
        echo "</tr>";
        
        $no = 1;
        foreach ($data_export as $row) {
            echo "<tr>";
            echo "<td>{$no}</td>";
            echo "<td>{$row->tanggal}</td>";
            echo "<td>{$row->plat_nomor}</td>";
            echo "<td>{$row->jenis_kendaraan}</td>";
            echo "<td>{$row->warna}</td>";
            echo "<td>{$row->pemilik}</td>";
            echo "<td>{$row->nama_area}</td>";
            echo "<td>{$row->waktu_masuk}</td>";
            echo "<td>{$row->waktu_keluar}</td>";
            echo "<td>{$row->durasi_format}</td>";
            echo "<td>" . number_format($row->biaya_total, 0, ',', '.') . "</td>";
            echo "<td>{$row->petugas}</td>";
            echo "</tr>";
            $no++;
        }
        
        echo "</table>";
        exit;
    }
    
    // Filter periode
    $periode = $_GET['periode'] ?? 'harian';
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    $tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-d');
    $tanggal_selesai = $_GET['tanggal_selesai'] ?? date('Y-m-d');
    $bulan = $_GET['bulan'] ?? date('Y-m');
    $tahun = $_GET['tahun'] ?? date('Y');
    
    // Ambil data rekap berdasarkan periode
    if ($periode == 'harian') {
        $data_rekap_harian = $rekap_obj->rekap_harian($tanggal);
        $data_rekap_jam_sibuk = $rekap_obj->rekap_jam_sibuk($tanggal, $tanggal);
        $total_pendapatan = $rekap_obj->total_pendapatan_periode($tanggal, $tanggal);
        $total_transaksi = $rekap_obj->total_transaksi_periode($tanggal, $tanggal);
        $rata_rata_per_transaksi = $total_transaksi > 0 ? $total_pendapatan / $total_transaksi : 0;
        $total_kendaraan_unik = $rekap_obj->total_kendaraan_unik_periode($tanggal, $tanggal);
        
        // Data detail untuk tabel
        $current_page = max(1, intval($_GET['page'] ?? 1));
        $current_search = $_GET['search'] ?? '';
        $data_detail = $rekap_obj->laporan_detail_periode_paginated($tanggal, $tanggal, $current_search, $current_page, $limit);
        $total_data = $rekap_obj->hitung_total_detail_periode($tanggal, $tanggal, $current_search);
        $total_halaman = ceil($total_data / $limit);
        
    } elseif ($periode == 'rentang') {
        // Rentang tanggal
        $data_rekap_harian = $rekap_obj->rekap_periode($tanggal_mulai, $tanggal_selesai);
        $data_rekap_jam_sibuk = $rekap_obj->rekap_jam_sibuk($tanggal_mulai, $tanggal_selesai);
        $data_rekap_jenis_kendaraan = $rekap_obj->rekap_jenis_kendaraan_periode($tanggal_mulai, $tanggal_selesai);
        $data_rekap_area = $rekap_obj->rekap_area_periode($tanggal_mulai, $tanggal_selesai);
        
        $total_pendapatan = $rekap_obj->total_pendapatan_periode($tanggal_mulai, $tanggal_selesai);
        $total_transaksi = $rekap_obj->total_transaksi_periode($tanggal_mulai, $tanggal_selesai);
        $rata_rata_per_transaksi = $total_transaksi > 0 ? $total_pendapatan / $total_transaksi : 0;
        $total_kendaraan_unik = $rekap_obj->total_kendaraan_unik_periode($tanggal_mulai, $tanggal_selesai);
        
        // Data detail untuk tabel
        $current_page = max(1, intval($_GET['page'] ?? 1));
        $current_search = $_GET['search'] ?? '';
        $data_detail = $rekap_obj->laporan_detail_periode_paginated($tanggal_mulai, $tanggal_selesai, $current_search, $current_page, $limit);
        $total_data = $rekap_obj->hitung_total_detail_periode($tanggal_mulai, $tanggal_selesai, $current_search);
        $total_halaman = ceil($total_data / $limit);
        
    } elseif ($periode == 'bulanan') {
        $data_rekap_bulanan = $rekap_obj->rekap_bulanan($bulan);
        $data_rekap_jenis_kendaraan = $rekap_obj->rekap_jenis_kendaraan_bulanan($bulan);
        $data_rekap_area = $rekap_obj->rekap_area_bulanan($bulan);
        $data_rekap_jam_sibuk = $rekap_obj->rekap_jam_sibuk(date('Y-m-01', strtotime($bulan)), date('Y-m-t', strtotime($bulan)));
        
        $total_pendapatan = $rekap_obj->total_pendapatan_periode(date('Y-m-01', strtotime($bulan)), date('Y-m-t', strtotime($bulan)));
        $total_transaksi = $rekap_obj->total_transaksi_periode(date('Y-m-01', strtotime($bulan)), date('Y-m-t', strtotime($bulan)));
        $rata_rata_per_transaksi = $total_transaksi > 0 ? $total_pendapatan / $total_transaksi : 0;
        $total_kendaraan_unik = $rekap_obj->total_kendaraan_unik_periode(date('Y-m-01', strtotime($bulan)), date('Y-m-t', strtotime($bulan)));
        
        // Data detail untuk tabel
        $current_page = max(1, intval($_GET['page'] ?? 1));
        $current_search = $_GET['search'] ?? '';
        $data_detail = $rekap_obj->laporan_detail_bulanan_paginated($bulan, $current_search, $current_page, $limit);
        $total_data = $rekap_obj->hitung_total_detail_bulanan($bulan, $current_search);
        $total_halaman = ceil($total_data / $limit);
        
    } elseif ($periode == 'tahunan') {
        $data_rekap_tahunan = $rekap_obj->rekap_tahunan($tahun);
        $data_rekap_jenis_kendaraan = $rekap_obj->rekap_jenis_kendaraan_tahunan($tahun);
        $data_rekap_area = $rekap_obj->rekap_area_tahunan($tahun);
        
        $total_pendapatan = $rekap_obj->total_pendapatan_periode("{$tahun}-01-01", "{$tahun}-12-31");
        $total_transaksi = $rekap_obj->total_transaksi_periode("{$tahun}-01-01", "{$tahun}-12-31");
        $rata_rata_per_transaksi = $total_transaksi > 0 ? $total_pendapatan / $total_transaksi : 0;
        $total_kendaraan_unik = $rekap_obj->total_kendaraan_unik_periode("{$tahun}-01-01", "{$tahun}-12-31");
        
        // Data detail untuk tabel
        $current_page = max(1, intval($_GET['page'] ?? 1));
        $current_search = $_GET['search'] ?? '';
        $data_detail = $rekap_obj->laporan_detail_tahunan_paginated($tahun, $current_search, $current_page, $limit);
        $total_data = $rekap_obj->hitung_total_detail_tahunan($tahun, $current_search);
        $total_halaman = ceil($total_data / $limit);
    }
    
    // Log aktivitas
    $log_obj->tambah_log($_SESSION['data']['id_user'], "Melihat laporan periode: {$periode}");
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: {$e->getMessage()}</div>";
}
?>