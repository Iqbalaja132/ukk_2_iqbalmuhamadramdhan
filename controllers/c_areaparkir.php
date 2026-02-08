<?php

include_once __DIR__ . '/../models/m_areaparkir.php';

$area = new area();

try {
  // Cek apakah ada parameter aksi
  if (!empty($_GET['aksi'])) {

    // =========================
    // JIKA AKSI BUKAN HAPUS
    // =========================
    if ($_GET['aksi'] != 'hapus') {

      // Tangkap data dari form
      $id_area   = $_POST['id_area'] ?? null;
      $nama_area = $_POST['nama_area'];
      $kapasitas = $_POST['kapasitas'];
      $terisi    = $_POST['terisi'];

      // Validasi: terisi tidak boleh lebih dari kapasitas
      if ($terisi > $kapasitas) {
        echo "<script>
                alert('Jumlah terisi tidak boleh melebihi kapasitas!');
                history.back();
              </script>";
        exit;
      }

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
    // TAMPIL SEMUA DATA DENGAN PAGINATION, SEARCH, DAN FILTER
    // =========================
    
    // Parameter untuk search dan filter
    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status_filter'] ?? '';
    $page = $_GET['page'] ?? 1;
    $limit = 10; // Jumlah data per halaman
    
    // Ambil data area dengan pagination
    $data_area = $area->tampil_data_paginated($search, $status_filter, $page, $limit);
    
    // Hitung total data untuk pagination
    $total_data = $area->hitung_total_data($search, $status_filter);
    $total_halaman = ceil($total_data / $limit);
    
    // Hitung statistik area parkir
    $total_kapasitas = $area->hitung_total_kapasitas();
    $total_terisi = $area->hitung_total_terisi();
    $persentase_terisi = $total_kapasitas > 0 ? round(($total_terisi / $total_kapasitas) * 100, 1) : 0;
    
    // Hitung jumlah area berdasarkan status
    $area_tersedia = $area->hitung_area_tersedia();
    $area_penuh = $area->hitung_area_penuh();
    
    // Simpan parameter untuk view
    $current_page = $page;
    $current_search = $search;
    $current_filter = $status_filter;
  }

} catch (Exception $e) {
  echo $e->getMessage();
}