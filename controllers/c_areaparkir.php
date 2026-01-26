<?php

include_once __DIR__ . '/../models/m_areaparkir.php';

$area = new area();

try {
  // cek apakah ada parameter aksi
  if (!empty($_GET['aksi'])) {

    // =========================
    // JIKA AKSI BUKAN HAPUS
    // =========================
    if ($_GET['aksi'] != 'hapus') {

      // tangkap data dari form
      $id_area   = $_POST['id_area'] ?? null;
      $nama_area = $_POST['nama_area'];
      $kapasitas = $_POST['kapasitas'];
      $terisi    = $_POST['terisi'];

      // =========================
      // TAMBAH DATA
      // =========================
      if ($_GET['aksi'] == 'tambah') {
        $area->tambah_data($nama_area, $kapasitas, $terisi);
      }

      // =========================
      // UPDATE DATA
      // =========================
      if ($_GET['aksi'] == 'update') {
        $area->edit_data($id_area, $nama_area, $kapasitas, $terisi);
      }

    } else {
      // =========================
      // HAPUS DATA
      // =========================
      $id_area = $_POST['id_area'];
      $area->hapus_data($id_area);
    }

  } else {
    // =========================
    // TAMPIL SEMUA DATA
    // =========================
    $data_area = $area->tampil_data();
  }

} catch (Exception $e) {
  echo $e->getMessage();
}
