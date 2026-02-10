<?php

include_once __DIR__ . '/../models/m_users.php';
include_once __DIR__ . '/../models/m_logaktivitas.php';

$user_obj = new user();
$log_obj  = new logaktivitas();

$data_user = [];
$total_data = 0;
$total_halaman = 0;
$jumlah_admin = 0;
$jumlah_petugas = 0;
$jumlah_owner = 0;
$current_page = 1;
$current_search = '';
$current_role_filter = '';
$current_status_filter = '';
$limit = 10;

try {
    if (!empty($_GET['aksi'])) {
        if ($_GET['aksi'] != 'hapus') {
            $id_user      = $_POST['id_user'] ?? null;
            $nama_lengkap = strtoupper($_POST['nama_lengkap']);
            $username     = $_POST['username'];
            $password     = $_POST['password'] ?? null;
            $role         = $_POST['role'];
            $status_aktif = isset($_POST['status_aktif']) ? $_POST['status_aktif'] : null;

            if ($_GET['aksi'] == 'tambah') {

                session_start();
                if (isset($_SESSION['data']['id_user'])) {
                    $log_detail = "Menambah user baru: $nama_lengkap ($username) sebagai $role";
                    $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);
                }

                $user_obj->tambah_data($nama_lengkap, $username, $password, $role, $status_aktif);
            }

            if ($_GET['aksi'] == 'update') {

                session_start();
                if (isset($_SESSION['data']['id_user'])) {

                    $user_sebelumnya = $user_obj->tampil_data_byid($id_user);

                    if ($user_sebelumnya) {

                        $log_detail = "Mengedit user: {$user_sebelumnya->nama_lengkap} ({$user_sebelumnya->username}) - ";
                        $detail_perubahan = [];

                        if ($user_sebelumnya->nama_lengkap != $nama_lengkap) {
                            $detail_perubahan[] = "Nama: {$user_sebelumnya->nama_lengkap} → $nama_lengkap";
                        }

                        if ($user_sebelumnya->username != $username) {
                            $detail_perubahan[] = "Username: {$user_sebelumnya->username} → $username";
                        }

                        if ($user_sebelumnya->role != $role) {
                            $detail_perubahan[] = "Role: {$user_sebelumnya->role} → $role";
                        }

                        if ($user_sebelumnya->status_aktif != $status_aktif) {
                            $status_lama = $user_sebelumnya->status_aktif == 1 ? 'Aktif' : 'Nonaktif';
                            $status_baru = $status_aktif == 1 ? 'Aktif' : 'Nonaktif';
                            $detail_perubahan[] = "Status: $status_lama → $status_baru";
                        }

                        if (!empty($password)) {
                            $detail_perubahan[] = "Password: Diubah";
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
                            "Mengedit user ID: $id_user (User tidak ditemukan)"
                        );
                    }
                }

                $user_obj->edit_data($id_user, $nama_lengkap, $username, $password, $role, $status_aktif);
            }

        } else {
            $id_user = $_POST['id_user'];

            $user_dihapus = $user_obj->tampil_data_byid($id_user);

            session_start();
            if (isset($_SESSION['data']['id_user'])) {
                if ($user_dihapus) {
                    $log_detail = "Menghapus user: {$user_dihapus->nama_lengkap} ({$user_dihapus->username}) dengan role {$user_dihapus->role}";
                } else {
                    $log_detail = "Menghapus user ID: $id_user (User tidak ditemukan)";
                }
                $log_obj->tambah_log($_SESSION['data']['id_user'], $log_detail);
            }

            $user_obj->hapus_data($id_user);
        }

    } else {
        $current_search = $_GET['search'] ?? '';
        $current_role_filter = $_GET['role_filter'] ?? '';
        $current_status_filter = $_GET['status_filter'] ?? '';
        $current_page = max(1, intval($_GET['page'] ?? 1));

        $data_user = $user_obj->tampil_data_paginated(
            $current_search,
            $current_role_filter,
            $current_status_filter,
            $current_page,
            $limit
        );

        $total_data = $user_obj->hitung_total_data(
            $current_search,
            $current_role_filter,
            $current_status_filter
        );

        $total_halaman = ceil($total_data / $limit);

        $jumlah_admin   = $user_obj->hitung_per_role('admin', $current_search, '');
        $jumlah_petugas = $user_obj->hitung_per_role('petugas', $current_search, '');
        $jumlah_owner   = $user_obj->hitung_per_role('owner', $current_search, '');
    }

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: {$e->getMessage()}</div>";
}
