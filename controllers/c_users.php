<?php
// controllers/c_users.php - TANPA session_start()

include_once __DIR__ . '/../models/m_users.php';

$user_obj = new user();

// Inisialisasi variabel untuk view
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
    // cek apakah ada parameter aksi
    if (!empty($_GET['aksi'])) {

        // =========================
        // JIKA AKSI BUKAN HAPUS
        // =========================
        if ($_GET['aksi'] != 'hapus') {

            // tangkap data dari form
            $id_user      = $_POST['id_user'] ?? null;
            $nama_lengkap = strtoupper($_POST['nama_lengkap']); // Konversi ke uppercase
            $username     = $_POST['username'];
            $password     = $_POST['password'] ?? null; // Bisa kosong saat update
            $role         = $_POST['role'];
            $status_aktif = isset($_POST['status_aktif']) ? $_POST['status_aktif'] : null;

            // =========================
            // TAMBAH DATA
            // =========================
            if ($_GET['aksi'] == 'tambah') {
                $user_obj->tambah_data($nama_lengkap, $username, $password, $role, $status_aktif);
            }

            // =========================
            // UPDATE DATA
            // =========================
            if ($_GET['aksi'] == 'update') {
                $user_obj->edit_data($id_user, $nama_lengkap, $username, $password, $role, $status_aktif);
            }

        } else {
            // =========================
            // HAPUS DATA
            // =========================
            $id_user = $_POST['id_user'];
            $user_obj->hapus_data($id_user);
        }

    } else {
        // =========================
        // TAMPIL SEMUA DATA DENGAN PAGINATION, SEARCH, DAN FILTER
        // =========================
        
        // Parameter untuk search dan filter
        $current_search = $_GET['search'] ?? '';
        $current_role_filter = $_GET['role_filter'] ?? '';
        $current_status_filter = $_GET['status_filter'] ?? '';
        $current_page = $_GET['page'] ?? 1;
        
        // Validasi halaman harus angka positif
        $current_page = max(1, intval($current_page));
        
        // Ambil data user dengan pagination
        $data_user = $user_obj->tampil_data_paginated(
            $current_search, 
            $current_role_filter, 
            $current_status_filter, 
            $current_page, 
            $limit
        );
        
        // Hitung total data untuk pagination
        $total_data = $user_obj->hitung_total_data(
            $current_search, 
            $current_role_filter, 
            $current_status_filter
        );
        
        // Hitung total halaman
        $total_halaman = ceil($total_data / $limit);
        
        // Hitung jumlah per role (TANPA filter status agar statistik tetap akurat)
        $jumlah_admin = $user_obj->hitung_per_role('admin', $current_search, '');
        $jumlah_petugas = $user_obj->hitung_per_role('petugas', $current_search, '');
        $jumlah_owner = $user_obj->hitung_per_role('owner', $current_search, '');
    }

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}