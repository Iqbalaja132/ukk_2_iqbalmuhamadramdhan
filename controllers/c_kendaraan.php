<?php

include_once __DIR__ . '/../models/m_kendaraan.php';

$kendaraan = new kendaraan();

try {

  if (!empty($_GET['aksi'])) {

    // =========================
    // JIKA AKSI BUKAN HAPUS
    // =========================
    if ($_GET['aksi'] != 'hapus') {

      $id_kendaraan     = $_POST['id_kendaraan'] ?? null;
      $plat_nomor       = $_POST['plat_nomor'];
      $jenis_kendaraan  = $_POST['jenis_kendaraan'];
      $warna            = $_POST['warna'];
      $pemilik          = $_POST['pemilik'];
      $id_user          = $_POST['id_user'];

      // =========================
      // TAMBAH DATA
      // =========================
      if ($_GET['aksi'] == 'tambah') {
        $kendaraan->tambah_data(
          $plat_nomor,
          $jenis_kendaraan,
          $warna,
          $pemilik,
          $id_user
        );
      }

      // =========================
      // UPDATE DATA
      // =========================
      if ($_GET['aksi'] == 'update') {
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

      // =========================
      // HAPUS DATA
      // =========================
      $id_kendaraan = $_POST['id_kendaraan'];
      $kendaraan->hapus_data($id_kendaraan);
    }

  } else {

    // =========================
    // TAMPIL SEMUA DATA
    // =========================
    $data_kendaraan = $kendaraan->tampil_data();
  }

} catch (Exception $e) {
  echo $e->getMessage();
}
