<?php

include_once __DIR__ . '/../models/m_kendaraan.php';
include_once __DIR__ . '/../models/m_logaktivitas.php';

$kendaraan = new kendaraan();
$log_obj = new logaktivitas();

try {

    if (!empty($_GET['aksi'])) {

        if ($_GET['aksi'] != 'hapus') {

            $id_kendaraan     = $_POST['id_kendaraan'] ?? null;
            $plat_nomor       = strtoupper($_POST['plat_nomor']);
            $jenis_kendaraan  = $_POST['jenis_kendaraan'];
            $warna            = $_POST['warna'];
            $pemilik          = $_POST['pemilik'];
            $id_user          = $_POST['id_user'];

            if ($_GET['aksi'] == 'tambah') {
                session_start();
                if (isset($_SESSION['data']['id_user'])) {
                    $log_detail = "Menambah kendaraan baru: Plat $plat_nomor ($jenis_kendaraan) milik $pemilik";
                    $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);
                }

                $kendaraan->tambah_data(
                    $plat_nomor,
                    $jenis_kendaraan,
                    $warna,
                    $pemilik,
                    $id_user
                );
            }

            if ($_GET['aksi'] == 'update') {
                session_start();
                if (isset($_SESSION['data']['id_user'])) {

                    $kendaraan_sebelumnya = $kendaraan->tampil_data_by_id($id_kendaraan);

                    if ($kendaraan_sebelumnya) {
                        $log_detail = "Mengedit kendaraan: {$kendaraan_sebelumnya['plat_nomor']} - ";
                        $detail_perubahan = [];

                        if ($kendaraan_sebelumnya['plat_nomor'] != $plat_nomor) {
                            $detail_perubahan[] = "Plat: {$kendaraan_sebelumnya['plat_nomor']} → $plat_nomor";
                        }

                        if ($kendaraan_sebelumnya['jenis_kendaraan'] != $jenis_kendaraan) {
                            $detail_perubahan[] = "Jenis: {$kendaraan_sebelumnya['jenis_kendaraan']} → $jenis_kendaraan";
                        }

                        if ($kendaraan_sebelumnya['warna'] != $warna) {
                            $detail_perubahan[] = "Warna: {$kendaraan_sebelumnya['warna']} → $warna";
                        }

                        if ($kendaraan_sebelumnya['pemilik'] != $pemilik) {
                            $detail_perubahan[] = "Pemilik: {$kendaraan_sebelumnya['pemilik']} → $pemilik";
                        }

                        if ($kendaraan_sebelumnya['id_user'] != $id_user) {
                            $detail_perubahan[] = "Petugas: ID {$kendaraan_sebelumnya['id_user']} → ID $id_user";
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
                            "Mengedit kendaraan ID: $id_kendaraan (Kendaraan tidak ditemukan)"
                        );
                    }
                }

                $kendaraan->edit_data(
                    $id_kendaraan,
                    $plat_nomor,
                    $jenis_kendaraan,
                    $warna,
                    $pemilik,
                    $id_user
                );
            }

        } else {
            $id_kendaraan = $_POST['id_kendaraan'];
            $kendaraan_dihapus = $kendaraan->tampil_data_by_id($id_kendaraan);

            session_start();
            if (isset($_SESSION['data']['id_user'])) {
                if ($kendaraan_dihapus) {
                    $log_detail = "Menghapus kendaraan: Plat {$kendaraan_dihapus['plat_nomor']} ({$kendaraan_dihapus['jenis_kendaraan']}) milik {$kendaraan_dihapus['pemilik']}";
                } else {
                    $log_detail = "Menghapus kendaraan ID: $id_kendaraan (Kendaraan tidak ditemukan)";
                }
                $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);
            }

            $kendaraan->hapus_data($id_kendaraan);
        }

    } else {
        $search = $_GET['search'] ?? '';
        $jenis_filter = $_GET['jenis_filter'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 10;

        $data_kendaraan = $kendaraan->tampil_data_paginated($search, $jenis_filter, $page, $limit);

        $total_data = $kendaraan->hitung_total_data($search, $jenis_filter);
        $total_halaman = ceil($total_data / $limit);

        $jumlah_motor = $kendaraan->hitung_jenis_kendaraan('motor', $search, $jenis_filter);
        $jumlah_mobil = $kendaraan->hitung_jenis_kendaraan('mobil', $search, $jenis_filter);

        $current_page = $page;
        $current_search = $search;
        $current_filter = $jenis_filter;
    }

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: {$e->getMessage()}</div>";
}