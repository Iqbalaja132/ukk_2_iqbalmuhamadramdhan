<?php

include_once 'm_koneksi.php';

class kendaraan
{
  // =========================
  // TAMPIL DATA SEMUA
  // =========================
  public function tampil_data()
  {
    $conn = new koneksi();
    $sql  = "SELECT k.*, u.nama_lengkap
             FROM tb_kendaraan k
             JOIN tb_user u ON k.id_user = u.id_user
             ORDER BY k.id_kendaraan DESC";
    $query = mysqli_query($conn->koneksi, $sql);

    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_object($query)) {
        // Konversi plat nomor ke huruf kapital saat menampilkan
        $data->plat_nomor = strtoupper($data->plat_nomor);
        $hasil[] = $data;
      }
    }
    return $hasil;
  }

  // =========================
  // TAMPIL DATA DENGAN PAGINATION, SEARCH, DAN FILTER
  // =========================
  public function tampil_data_paginated($search = '', $jenis_filter = '', $page = 1, $limit = 10)
  {
    $conn = new koneksi();
    
    // Hitung offset
    $offset = ($page - 1) * $limit;
    
    // Build query dengan search dan filter
    $sql = "SELECT k.*, u.nama_lengkap
            FROM tb_kendaraan k
            JOIN tb_user u ON k.id_user = u.id_user
            WHERE 1=1";
    
    // Tambahkan kondisi search (konversi search ke uppercase untuk pencarian case-insensitive)
    if (!empty($search)) {
      $search = mysqli_real_escape_string($conn->koneksi, strtoupper($search));
      $sql .= " AND (
                UPPER(k.plat_nomor) LIKE '%$search%' OR 
                k.warna LIKE '%$search%' OR 
                k.pemilik LIKE '%$search%' OR
                u.nama_lengkap LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter
    if (!empty($jenis_filter)) {
      $jenis_filter = mysqli_real_escape_string($conn->koneksi, $jenis_filter);
      $sql .= " AND k.jenis_kendaraan = '$jenis_filter'";
    }
    
    $sql .= " ORDER BY k.id_kendaraan DESC
              LIMIT $limit OFFSET $offset";
    
    $query = mysqli_query($conn->koneksi, $sql);

    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_object($query)) {
        // Konversi plat nomor ke huruf kapital
        $data->plat_nomor = strtoupper($data->plat_nomor);
        $hasil[] = $data;
      }
    }
    return $hasil;
  }

  // =========================
  // HITUNG TOTAL DATA UNTUK PAGINATION
  // =========================
  public function hitung_total_data($search = '', $jenis_filter = '')
  {
    $conn = new koneksi();
    
    $sql = "SELECT COUNT(*) as total
            FROM tb_kendaraan k
            JOIN tb_user u ON k.id_user = u.id_user
            WHERE 1=1";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($conn->koneksi, strtoupper($search));
      $sql .= " AND (
                UPPER(k.plat_nomor) LIKE '%$search%' OR 
                k.warna LIKE '%$search%' OR 
                k.pemilik LIKE '%$search%' OR
                u.nama_lengkap LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter
    if (!empty($jenis_filter)) {
      $jenis_filter = mysqli_real_escape_string($conn->koneksi, $jenis_filter);
      $sql .= " AND k.jenis_kendaraan = '$jenis_filter'";
    }
    
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['total'] ?? 0;
  }

  // =========================
  // HITUNG JUMLAH PER JENIS KENDARAAN
  // =========================
  public function hitung_jenis_kendaraan($jenis, $search = '', $jenis_filter = '')
  {
    $conn = new koneksi();
    
    $sql = "SELECT COUNT(*) as total
            FROM tb_kendaraan k
            JOIN tb_user u ON k.id_user = u.id_user
            WHERE k.jenis_kendaraan = '" . mysqli_real_escape_string($conn->koneksi, $jenis) . "'";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($conn->koneksi, strtoupper($search));
      $sql .= " AND (
                UPPER(k.plat_nomor) LIKE '%$search%' OR 
                k.warna LIKE '%$search%' OR 
                k.pemilik LIKE '%$search%' OR
                u.nama_lengkap LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter
    if (!empty($jenis_filter)) {
      $jenis_filter = mysqli_real_escape_string($conn->koneksi, $jenis_filter);
      $sql .= " AND k.jenis_kendaraan = '$jenis_filter'";
    }
    
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['total'] ?? 0;
  }

  // =========================
  // TAMBAH DATA
  // =========================
  public function tambah_data($plat, $jenis, $warna, $pemilik, $id_user)
  {
    $conn = new koneksi();
    // Konversi ke uppercase sebelum disimpan
    $plat = mysqli_real_escape_string($conn->koneksi, strtoupper($plat));
    $jenis = mysqli_real_escape_string($conn->koneksi, $jenis);
    $warna = mysqli_real_escape_string($conn->koneksi, $warna);
    $pemilik = mysqli_real_escape_string($conn->koneksi, $pemilik);
    $id_user = mysqli_real_escape_string($conn->koneksi, $id_user);
    
    $sql  = "INSERT INTO tb_kendaraan 
            (plat_nomor, jenis_kendaraan, warna, pemilik, id_user)
            VALUES 
            ('$plat','$jenis','$warna','$pemilik','$id_user')";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      header("Location: ../views/admin/kendaraan.php");
      exit;
    } else {
      echo "<script>alert('Data gagal ditambahkan');history.back()</script>";
    }
  }

  // =========================
  // UPDATE DATA
  // =========================
  public function edit_data($id, $plat, $jenis, $warna, $pemilik, $id_user)
  {
    $conn = new koneksi();
    $id = mysqli_real_escape_string($conn->koneksi, $id);
    // Konversi ke uppercase sebelum disimpan
    $plat = mysqli_real_escape_string($conn->koneksi, strtoupper($plat));
    $jenis = mysqli_real_escape_string($conn->koneksi, $jenis);
    $warna = mysqli_real_escape_string($conn->koneksi, $warna);
    $pemilik = mysqli_real_escape_string($conn->koneksi, $pemilik);
    $id_user = mysqli_real_escape_string($conn->koneksi, $id_user);
    
    $sql  = "UPDATE tb_kendaraan SET
              plat_nomor='$plat',
              jenis_kendaraan='$jenis',
              warna='$warna',
              pemilik='$pemilik',
              id_user='$id_user'
             WHERE id_kendaraan='$id'";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      header("Location: ../views/admin/kendaraan.php");
      exit;
    } else {
      echo "<script>alert('Data gagal diubah');history.back()</script>";
    }
  }

  // =========================
  // HAPUS DATA
  // =========================
  public function hapus_data($id)
  {
    $conn = new koneksi();
    $id = mysqli_real_escape_string($conn->koneksi, $id);
    
    mysqli_query(
      $conn->koneksi,
      "DELETE FROM tb_kendaraan WHERE id_kendaraan='$id'"
    );

    header("Location: ../views/admin/kendaraan.php");
    exit;
  }
}