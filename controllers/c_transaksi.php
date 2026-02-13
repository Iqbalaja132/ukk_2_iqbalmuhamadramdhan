<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../models/m_transaksi.php';
include_once __DIR__ . '/../models/m_kendaraan.php';
include_once __DIR__ . '/../models/m_areaparkir.php';
include_once __DIR__ . '/../models/m_tarif.php';
include_once __DIR__ . '/../models/m_logaktivitas.php';

$transaksi_obj = new transaksi();
$kendaraan_obj = new kendaraan();
$areaparkir_obj = new area();
$tarif_obj = new tarif();
$log_obj = new logaktivitas();

$data_transaksi_aktif = [];
$data_riwayat = [];
$data_kendaraan_masuk = [];
$data_area = [];
$total_transaksi_hari_ini = 0;
$total_pendapatan_hari_ini = 0;
$kendaraan_aktif = 0;
$ketersediaan_area = [];

$current_page = 1;
$current_search = '';
$limit = 10;
$total_data = 0;
$total_halaman = 0;

try {
    $aksi = $_GET['aksi'] ?? '';
    
    if ($aksi == 'cari_kendaraan') {
        $plat_nomor = strtoupper($_GET['plat_nomor']);
        $kendaraan = $kendaraan_obj->cari_plat($plat_nomor);
        header('Content-Type: application/json');
        echo json_encode($kendaraan ?: null);
        exit;
    }
    
    if ($aksi == 'cari_transaksi_aktif') {
        $plat_nomor = strtoupper($_GET['plat_nomor']);
        $transaksi = $transaksi_obj->cari_transaksi_aktif_by_plat($plat_nomor);
        
        if ($transaksi) {
            $kendaraan = $kendaraan_obj->tampil_data_by_id($transaksi->id_kendaraan);
            $area = $areaparkir_obj->tampil_data_byid($transaksi->id_area);
            $tarif = $tarif_obj->tampil_data_byid($transaksi->id_tarif);
            
            $waktu_masuk = $transaksi->waktu_masuk_unix ?? strtotime($transaksi->waktu_masuk);
            $sekarang = time();
            $selisih = $sekarang - $waktu_masuk;
            
            $jam = floor($selisih / 3600);
            $menit = floor(($selisih % 3600) / 60);
            $detik = $selisih % 60;
            $durasi_format = sprintf("%02d:%02d:%02d", $jam, $menit, $detik);
            
            $durasi_text = '';
            if ($jam > 0) $durasi_text .= $jam . ' jam ';
            if ($menit > 0) $durasi_text .= $menit . ' menit ';
            if ($detik > 0) $durasi_text .= $detik . ' detik';
            if ($jam == 0 && $menit == 0 && $detik == 0) {
                $durasi_text = '0 detik';
            }
            
            $biaya_bulat = $transaksi_obj->hitung_biaya_parkir($selisih, $tarif->tarif_per_jam);
            
            $data = [
                'id_parkir' => $transaksi->id_parkir,
                'plat_nomor' => $kendaraan['plat_nomor'],
                'jenis_kendaraan' => $kendaraan['jenis_kendaraan'],
                'warna' => $kendaraan['warna'],
                'pemilik' => $kendaraan['pemilik'],
                'waktu_masuk' => date('d/m/Y H:i:s', strtotime($transaksi->waktu_masuk)),
                'waktu_masuk_full' => $transaksi->waktu_masuk,
                'waktu_masuk_unix' => $waktu_masuk,
                'area' => $area->nama_area,
                'durasi_format' => $durasi_format,
                'durasi_text' => $durasi_text,
                'durasi_jam' => ceil($selisih / 3600),
                'durasi_detik' => $selisih,
                'tarif_per_jam' => $tarif->tarif_per_jam,
                'biaya' => $biaya_bulat
            ];
            
            echo json_encode($data);
        } else {
            echo json_encode(null);
        }
        exit;
    }
    
    if (!isset($_SESSION['data']['id_user'])) {
        echo "<script>
            alert('Anda harus login terlebih dahulu!');
            window.location.href = '../login.php';
        </script>";
        exit;
    }
    
    if ($aksi == 'masuk') {
        $plat_nomor = strtoupper($_POST['plat_nomor']);
        $jenis_kendaraan = $_POST['jenis_kendaraan'];
        $warna = $_POST['warna'] ?? '';
        $pemilik = strtoupper($_POST['pemilik'] ?? '-');
        $id_area = $_POST['id_area'];
        $id_user = $_SESSION['data']['id_user'];
        
        $kendaraan = $kendaraan_obj->cari_plat($plat_nomor);
        
        if (!$kendaraan) {
            $id_kendaraan = $kendaraan_obj->tambah_data($plat_nomor, $jenis_kendaraan, $warna, $pemilik, $id_user);
        } else {
            $id_kendaraan = $kendaraan->id_kendaraan;
        }
        
        $cek_parkir = $transaksi_obj->cek_parkir_aktif($id_kendaraan);
        if ($cek_parkir) {
            echo "<script>
                alert('Kendaraan dengan plat {$plat_nomor} masih terparkir!');
                window.history.back();
            </script>";
            exit;
        }
        
        $area = $areaparkir_obj->tampil_data_byid($id_area);
        if ($area->terisi >= $area->kapasitas) {
            echo "<script>
                alert('Area parkir {$area->nama_area} sudah penuh!');
                window.history.back();
            </script>";
            exit;
        }
        
        $tarif = $tarif_obj->tampil_data_by_jenis($jenis_kendaraan);
        if (!$tarif) {
            echo "<script>
                alert('Tarif untuk jenis kendaraan {$jenis_kendaraan} tidak ditemukan!');
                window.history.back();
            </script>";
            exit;
        }
        $id_tarif = $tarif->id_tarif;
        
        $id_parkir = $transaksi_obj->tambah_masuk($id_kendaraan, $id_tarif, $id_user, $id_area);
        
        $areaparkir_obj->update_terisi($id_area, 'tambah');
        
        $log_obj->tambah_log($id_user, "Parkir masuk: {$plat_nomor} di {$area->nama_area}");
        
        echo "<script>
            alert('Kendaraan {$plat_nomor} berhasil masuk');
            window.location.href = '../views/petugas/transaksi.php';
        </script>";
        exit;
    }
    
    if ($aksi == 'keluar') {
        $id_parkir = $_POST['id_parkir'];
        $plat_nomor = $_POST['plat_nomor'];
        $id_user = $_SESSION['data']['id_user'];
        
        $transaksi = $transaksi_obj->tampil_data_byid($id_parkir);
        
        if ($transaksi && $transaksi->status == 'masuk') {
            $waktu_masuk = new DateTime($transaksi->waktu_masuk);
            $waktu_keluar = new DateTime();
            
            $selisih = $waktu_keluar->getTimestamp() - $waktu_masuk->getTimestamp();
            $durasi_jam = ceil($selisih / 3600);
            if ($durasi_jam < 1) $durasi_jam = 1;
            
            $tarif = $tarif_obj->tampil_data_byid($transaksi->id_tarif);
            $biaya_real = $durasi_jam * $tarif->tarif_per_jam;
            
            $update = $transaksi_obj->update_keluar(
                $id_parkir, 
                $waktu_keluar->format('Y-m-d H:i:s'), 
                $durasi_jam, 
                $biaya_real
            );
            
            if ($update) {
                $areaparkir_obj->update_terisi($transaksi->id_area, 'kurang');
                
                $transaksi_updated = $transaksi_obj->tampil_data_byid($id_parkir);
                
                $log_obj->tambah_log(
                    $id_user,
                    "Parkir keluar: {$plat_nomor} - Durasi: " . gmdate("H:i:s", $transaksi_updated->durasi_detik) . ", Biaya: Rp " . number_format($transaksi_updated->biaya_total, 0, ',', '.')
                );
                
                echo "<script>
                    window.location.href = '../views/petugas/cetak_struk.php?id_parkir={$id_parkir}';
                </script>";
                exit;
            } else {
                echo "<script>
                    alert('Gagal memproses transaksi keluar!');
                    window.history.back();
                </script>";
                exit;
            }
        } else {
            echo "<script>
                alert('Transaksi tidak ditemukan atau sudah keluar!');
                window.history.back();
            </script>";
            exit;
        }
    }
    
    $current_page = max(1, intval($_GET['page'] ?? 1));
    $current_search = $_GET['search'] ?? '';
    $filter_status = $_GET['status'] ?? 'aktif';
    
    $total_transaksi_hari_ini = $transaksi_obj->hitung_transaksi_hari_ini();
    $total_pendapatan_hari_ini = $transaksi_obj->hitung_pendapatan_hari_ini();
    $kendaraan_aktif = $transaksi_obj->hitung_kendaraan_aktif();
    
    $ketersediaan_area = $areaparkir_obj->tampil_data_ketersediaan();
    $data_transaksi_aktif = $transaksi_obj->tampil_transaksi_aktif_paginated($current_search, $current_page, $limit);
    
    if ($filter_status == 'riwayat') {
        $data_riwayat = $transaksi_obj->tampil_riwayat_paginated($current_search, $current_page, $limit);
        $total_data = $transaksi_obj->hitung_total_riwayat($current_search);
    } else {
        $total_data = $transaksi_obj->hitung_total_aktif($current_search);
    }
    
    $total_halaman = ceil($total_data / $limit);
    $data_area = $areaparkir_obj->tampil_data_tersedia();
    $data_kendaraan_masuk = $kendaraan_obj->tampil_data();
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: {$e->getMessage()}</div>";
}
?>