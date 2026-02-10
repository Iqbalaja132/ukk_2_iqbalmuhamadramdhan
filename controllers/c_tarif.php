<?php

include_once __DIR__ . '/../models/m_tarif.php';
include_once __DIR__ . '/../models/m_logaktivitas.php';

$tarif = new tarif();
$log_obj = new logaktivitas();

try {
    if (!empty($_GET['aksi'])) {
        if ($_GET['aksi'] != 'hapus') {

            $id_tarif         = $_POST['id_tarif'] ?? null;
            $jenis_kendaraan  = $_POST['jenis_kendaraan'];
            $tarif_per_jam    = $_POST['tarif_per_jam'];

            if ($_GET['aksi'] == 'tambah') {
                $valid_jenis = ['motor', 'mobil', 'lainnya'];
                if (!in_array($jenis_kendaraan, $valid_jenis)) {
                    echo "<script>
                        alert('Jenis kendaraan tidak valid!');
                        window.location='../views/admin/tarif.php';
                    </script>";
                    exit;
                }

                session_start();
                if (isset($_SESSION['data']['id_user'])) {
                    $formatted_tarif = 'Rp ' . number_format($tarif_per_jam, 0, ',', '.');
                    $log_detail = "Menambah tarif baru: " . ucfirst($jenis_kendaraan) . " - $formatted_tarif per jam";
                    $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);
                }
                
                $tarif->tambah_data($jenis_kendaraan, $tarif_per_jam);
            }

            if ($_GET['aksi'] == 'update') {
                session_start();
                if (isset($_SESSION['data']['id_user'])) {
                    $tarif_sebelumnya = $tarif->tampil_data_byid($id_tarif);
                    
                    if ($tarif_sebelumnya) {
                        $jenis_kendaraan = $tarif_sebelumnya->jenis_kendaraan;
                        $formatted_tarif_lama = 'Rp ' . number_format($tarif_sebelumnya->tarif_per_jam, 0, ',', '.');
                        $formatted_tarif_baru = 'Rp ' . number_format($tarif_per_jam, 0, ',', '.');

                        $log_detail = "Mengedit tarif " . ucfirst($jenis_kendaraan) . ": ";
                        
                        if ($tarif_sebelumnya->tarif_per_jam != $tarif_per_jam) {
                            $log_detail .= "Tarif berubah dari $formatted_tarif_lama menjadi $formatted_tarif_baru per jam";
                        } else {
                            $log_detail .= "Tidak ada perubahan tarif";
                        }
                        
                        $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);
                    }
                }
                
                $tarif->edit_data($id_tarif, $tarif_per_jam);
            }

        } else {
            $id_tarif = $_POST['id_tarif'];

            session_start();
            if (isset($_SESSION['data']['id_user'])) {

                $tarif_dihapus = $tarif->tampil_data_byid($id_tarif);
                
                if ($tarif_dihapus) {
                    $formatted_tarif = 'Rp ' . number_format($tarif_dihapus->tarif_per_jam, 0, ',', '.');
                    $log_detail = "Menghapus tarif: " . ucfirst($tarif_dihapus->jenis_kendaraan) . " - $formatted_tarif per jam";
                } else {
                    $log_detail = "Menghapus tarif ID: $id_tarif";
                }
                
                $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);
            }
            
            $tarif->hapus_data($id_tarif);
        }

    } else {
        $search = $_GET['search'] ?? '';
        $jenis_filter = $_GET['jenis_filter'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 10;

        $data_tarif = $tarif->tampil_data_paginated($search, $jenis_filter, $page, $limit);

        $total_data = $tarif->hitung_total_data($search, $jenis_filter);
        $total_halaman = ceil($total_data / $limit);

        $jumlah_motor = $tarif->hitung_jenis_kendaraan('motor', $search, $jenis_filter);
        $jumlah_mobil = $tarif->hitung_jenis_kendaraan('mobil', $search, $jenis_filter);
        $jumlah_lainnya = $tarif->hitung_jenis_kendaraan('lainnya', $search, $jenis_filter);

        $tarif_tertinggi = $tarif->get_tarif_tertinggi();
        $tarif_terendah = $tarif->get_tarif_terendah();
        $rata_rata_tarif = $tarif->get_rata_rata_tarif();

        $jenis_sudah_ada = $tarif->cek_jenis_kendaraan_sudah_ada();

        $current_page = $page;
        $current_search = $search;
        $current_filter = $jenis_filter;
    }

} catch (Exception $e) {
    echo $e->getMessage();
}
?>