<?php

include_once __DIR__ . '/../models/m_tarif.php';

$tarif = new tarif();

try {
  // Cek apakah ada parameter aksi
  if (!empty($_GET['aksi'])) {

    // =========================
    // JIKA AKSI BUKAN HAPUS
    // =========================
    if ($_GET['aksi'] != 'hapus') {

      // Tangkap data dari form
      $id_tarif         = $_POST['id_tarif'] ?? null;
      $jenis_kendaraan  = $_POST['jenis_kendaraan'];
      $tarif_per_jam    = $_POST['tarif_per_jam'];

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
    // TAMPIL SEMUA DATA DENGAN PAGINATION, SEARCH, DAN FILTER
    // =========================
    
    // Parameter untuk search dan filter
    $search = $_GET['search'] ?? '';
    $jenis_filter = $_GET['jenis_filter'] ?? '';
    $page = $_GET['page'] ?? 1;
    $limit = 10; // Jumlah data per halaman
    
    // Ambil data tarif dengan pagination
    $data_tarif = $tarif->tampil_data_paginated($search, $jenis_filter, $page, $limit);
    
    // Hitung total data untuk pagination
    $total_data = $tarif->hitung_total_data($search, $jenis_filter);
    $total_halaman = ceil($total_data / $limit);
    
    // Hitung jumlah per jenis kendaraan
    $jumlah_motor = $tarif->hitung_jenis_kendaraan('motor', $search, $jenis_filter);
    $jumlah_mobil = $tarif->hitung_jenis_kendaraan('mobil', $search, $jenis_filter);
    $jumlah_lainnya = $tarif->hitung_jenis_kendaraan('lainnya', $search, $jenis_filter);
    
    // Hitung statistik tarif
    $tarif_tertinggi = $tarif->get_tarif_tertinggi();
    $tarif_terendah = $tarif->get_tarif_terendah();
    $rata_rata_tarif = $tarif->get_rata_rata_tarif();
    
    // Simpan parameter untuk view
    $current_page = $page;
    $current_search = $search;
    $current_filter = $jenis_filter;
  }

} catch (Exception $e) {
  echo $e->getMessage();
}