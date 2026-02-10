<?php

include_once __DIR__ . '/../models/m_areaparkir.php';
include_once __DIR__ . '/../models/m_logaktivitas.php';

$area = new area();
$log_obj = new logaktivitas();

try {
    if (!empty($_GET['aksi'])) {
        if ($_GET['aksi'] != 'hapus') {
            $id_area = $_POST['id_area'] ?? null;
            $nama_area = $_POST['nama_area'];
            
            $kapasitas = (int)$_POST['kapasitas'];
            $terisi = (int)$_POST['terisi'];
            
            if ($terisi > $kapasitas) {
                echo "<script>
                        alert('Jumlah terisi tidak boleh melebihi kapasitas!');
                        history.back();
                      </script>";
                exit;
            }

            if ($_GET['aksi'] == 'tambah') {
                session_start();
                if (isset($_SESSION['data']['id_user'])) {
                    $log_detail = "Menambah area parkir baru: $nama_area dengan kapasitas $kapasitas kendaraan";
                    $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);
                }

                $area->tambah_data($nama_area, $kapasitas, $terisi);
            }

            if ($_GET['aksi'] == 'update') {
                session_start();
                if (isset($_SESSION['data']['id_user'])) {

                    $area_sebelumnya = $area->tampil_data_byid($id_area);

                    if ($area_sebelumnya) {
                        $log_detail = "Mengedit area parkir: {$area_sebelumnya->nama_area} - ";
                        $detail_perubahan = [];

                        if ($area_sebelumnya->nama_area != $nama_area) {
                            $detail_perubahan[] = "Nama: {$area_sebelumnya->nama_area} → $nama_area";
                        }

                        if ($area_sebelumnya->kapasitas != $kapasitas) {
                            $detail_perubahan[] = "Kapasitas: {$area_sebelumnya->kapasitas} → $kapasitas";
                        }

                        if ($area_sebelumnya->terisi != $terisi) {
                            $detail_perubahan[] = "Terisi: {$area_sebelumnya->terisi} → $terisi";
                        }

                        $status_lama = $area_sebelumnya->terisi >= $area_sebelumnya->kapasitas ? 'Penuh' : 'Tersedia';
                        $status_baru = $terisi >= $kapasitas ? 'Penuh' : 'Tersedia';
                        if ($status_lama != $status_baru) {
                            $detail_perubahan[] = "Status: $status_lama → $status_baru";
                        }

                        if (!empty($detail_perubahan)) {
                            $log_detail .= implode(', ', $detail_perubahan);
                        } else {
                            $log_detail .= "Tidak ada perubahan";
                        }

                        $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);

                    } else {
                        $log_obj->tambah_log(
                            $_SESSION['data']['id_user'],
                            "Mengedit area parkir ID: $id_area (Area tidak ditemukan)"
                        );
                    }
                }

                $area->edit_data($id_area, $nama_area, $kapasitas, $terisi);
            }

        } else {
            $id_area = $_POST['id_area'];
            $area_dihapus = $area->tampil_data_byid($id_area);

            session_start();
            if (isset($_SESSION['data']['id_user'])) {
                if ($area_dihapus) {
                    $log_detail = "Menghapus area parkir: {$area_dihapus->nama_area} (Kapasitas: {$area_dihapus->kapasitas}, Terisi: {$area_dihapus->terisi})";
                } else {
                    $log_detail = "Menghapus area parkir ID: $id_area (Area tidak ditemukan)";
                }
                $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);
            }

            $area->hapus_data($id_area);
        }

    } else {
        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status_filter'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 10;

        $data_area = $area->tampil_data_paginated($search, $status_filter, $page, $limit);

        $total_data = $area->hitung_total_data($search, $status_filter);
        $total_halaman = ceil($total_data / $limit);

        $total_kapasitas = $area->hitung_total_kapasitas();
        $total_terisi = $area->hitung_total_terisi();
        $persentase_terisi = $total_kapasitas > 0 ? round(($total_terisi / $total_kapasitas) * 100, 1) : 0;

        $area_tersedia = $area->hitung_area_tersedia();
        $area_penuh = $area->hitung_area_penuh();

        $current_page = $page;
        $current_search = $search;
        $current_filter = $status_filter;
    }

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: {$e->getMessage()}</div>";
}