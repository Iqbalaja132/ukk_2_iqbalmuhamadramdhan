<?php

include_once __DIR__ . '/../models/m_users.php';

$user_obj = new user();

try {
    // cek apakah ada parameter aksi
    if (!empty($_GET['aksi'])) {

        // =========================
        // JIKA AKSI BUKAN HAPUS
        // =========================
        if ($_GET['aksi'] != 'hapus') {

            // tangkap data dari form
            $id_user      = $_POST['id_user'] ?? null;
            $nama_lengkap = $_POST['nama_lengkap'];
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
        // TAMPIL SEMUA DATA
        // =========================
        $data_user = $user_obj->tampil_data();
    }

} catch (Exception $e) {
    echo $e->getMessage();
}