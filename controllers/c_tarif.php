<?php

include_once __DIR__ . '/../models/m_tarif.php';

$tarif = new tarif();

try {
  // cek apakah ada parameter aksi
  if (!empty($_GET['aksi'])) {

    // =========================
    // JIKA AKSI BUKAN HAPUS
    // =========================
    if ($_GET['aksi'] != 'hapus') {

      // tangkap data dari form
      $id_tarif   = $_POST['id_tarif'] ?? null;
      $jenis_kendaraan = $_POST['jenis_kendaraan'];
      $tarif_per_jam = $_POST['tarif_per_jam'];

      // =========================
      // TAMBAH DATA
      // =========================
      if ($_GET['aksi'] == 'tambah') {
        $tarif->tambah_data($jenis_kendaraan, $tarif_per_jam);
      }

      // =========================
      // UPDATE DATA
      // =========================
      if ($_GET['aksi'] == 'update') {
        $tarif->edit_data($id_tarif, $jenis_kendaraan, $tarif_per_jam);
      }

    } else {
      // =========================
      // HAPUS DATA
      // =========================
      $id_tarif = $_POST['id_tarif'];
      $tarif->hapus_data($id_tarif);
    }

  } else {
    // =========================
    // TAMPIL SEMUA DATA
    // =========================
    $data_tarif = $tarif->tampil_data();
  }

} catch (Exception $e) {
  echo $e->getMessage();
}
