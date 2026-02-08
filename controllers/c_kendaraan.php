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
      // Konversi plat nomor ke huruf kapital
      $plat_nomor       = strtoupper($_POST['plat_nomor']);
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
    // TAMPIL SEMUA DATA DENGAN PAGINATION, SEARCH, DAN FILTER
    // =========================
    
    // Parameter untuk search dan filter
    $search = $_GET['search'] ?? '';
    $jenis_filter = $_GET['jenis_filter'] ?? '';
    $page = $_GET['page'] ?? 1;
    $limit = 10; // Jumlah data per halaman
    
    // Ambil data kendaraan dengan pagination
    $data_kendaraan = $kendaraan->tampil_data_paginated($search, $jenis_filter, $page, $limit);
    
    // Hitung total data untuk pagination
    $total_data = $kendaraan->hitung_total_data($search, $jenis_filter);
    $total_halaman = ceil($total_data / $limit);
    
    // Hitung jumlah motor dan mobil berdasarkan seluruh data (bukan hanya halaman saat ini)
    $jumlah_motor = $kendaraan->hitung_jenis_kendaraan('motor', $search, $jenis_filter);
    $jumlah_mobil = $kendaraan->hitung_jenis_kendaraan('mobil', $search, $jenis_filter);
    
    // Simpan parameter untuk view
    $current_page = $page;
    $current_search = $search;
    $current_filter = $jenis_filter;
  }

} catch (Exception $e) {
  echo $e->getMessage();
}