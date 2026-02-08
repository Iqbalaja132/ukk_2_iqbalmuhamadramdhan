<?php

include_once 'm_koneksi.php';

class tarif
{
  // =========================
  // TAMPIL DATA SEMUA
  // =========================
  public function tampil_data()
  {
    $conn = new koneksi();
    $sql  = "SELECT * FROM tb_tarif ORDER BY jenis_kendaraan ASC";
    $query = mysqli_query($conn->koneksi, $sql);

    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_object($query)) {
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
    $sql = "SELECT * FROM tb_tarif WHERE 1=1";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($conn->koneksi, $search);
      $sql .= " AND (
                jenis_kendaraan LIKE '%$search%' OR 
                tarif_per_jam LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter
    if (!empty($jenis_filter)) {
      $jenis_filter = mysqli_real_escape_string($conn->koneksi, $jenis_filter);
      $sql .= " AND jenis_kendaraan = '$jenis_filter'";
    }
    
    $sql .= " ORDER BY tarif_per_jam DESC
              LIMIT $limit OFFSET $offset";
    
    $query = mysqli_query($conn->koneksi, $sql);

    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_object($query)) {
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
    
    $sql = "SELECT COUNT(*) as total FROM tb_tarif WHERE 1=1";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($conn->koneksi, $search);
      $sql .= " AND (
                jenis_kendaraan LIKE '%$search%' OR 
                tarif_per_jam LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter
    if (!empty($jenis_filter)) {
      $jenis_filter = mysqli_real_escape_string($conn->koneksi, $jenis_filter);
      $sql .= " AND jenis_kendaraan = '$jenis_filter'";
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
    
    $sql = "SELECT COUNT(*) as total FROM tb_tarif 
            WHERE jenis_kendaraan = '" . mysqli_real_escape_string($conn->koneksi, $jenis) . "'";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($conn->koneksi, $search);
      $sql .= " AND (
                jenis_kendaraan LIKE '%$search%' OR 
                tarif_per_jam LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter
    if (!empty($jenis_filter)) {
      $jenis_filter = mysqli_real_escape_string($conn->koneksi, $jenis_filter);
      $sql .= " AND jenis_kendaraan = '$jenis_filter'";
    }
    
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['total'] ?? 0;
  }

  // =========================
  // GET TARIF TERTINGGI
  // =========================
  public function get_tarif_tertinggi()
  {
    $conn = new koneksi();
    $sql = "SELECT MAX(tarif_per_jam) as max_tarif FROM tb_tarif";
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['max_tarif'] ?? 0;
  }

  // =========================
  // GET TARIF TERENDAH
  // =========================
  public function get_tarif_terendah()
  {
    $conn = new koneksi();
    $sql = "SELECT MIN(tarif_per_jam) as min_tarif FROM tb_tarif";
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['min_tarif'] ?? 0;
  }

  // =========================
  // GET RATA-RATA TARIF
  // =========================
  public function get_rata_rata_tarif()
  {
    $conn = new koneksi();
    $sql = "SELECT AVG(tarif_per_jam) as avg_tarif FROM tb_tarif";
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return number_format($result['avg_tarif'] ?? 0, 0, ',', '.');
  }

  // =========================
  // TAMBAH DATA
  // =========================
  public function tambah_data($jenis_kendaraan, $tarif_per_jam)
  {
    $conn = new koneksi();
    $jenis_kendaraan = mysqli_real_escape_string($conn->koneksi, $jenis_kendaraan);
    $tarif_per_jam = mysqli_real_escape_string($conn->koneksi, $tarif_per_jam);
    
    // Cek apakah jenis kendaraan sudah ada
    $cek_sql = "SELECT * FROM tb_tarif WHERE jenis_kendaraan = '$jenis_kendaraan'";
    $cek_query = mysqli_query($conn->koneksi, $cek_sql);
    
    if (mysqli_num_rows($cek_query) > 0) {
      echo "<script>
              alert('Jenis kendaraan sudah ada dalam database!');
              window.location='../views/admin/tarif.php';
            </script>";
      exit;
    }
    
    $sql  = "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam)
             VALUES ('$jenis_kendaraan', '$tarif_per_jam')";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      header("Location: ../views/admin/tarif.php");
      exit;
    } else {
      echo "<script>alert('Data gagal ditambahkan');history.back()</script>";
    }
  }

  // =========================
  // UPDATE DATA
  // =========================
  public function edit_data($id_tarif, $jenis_kendaraan, $tarif_per_jam)
  {
    $conn = new koneksi();
    $id_tarif = mysqli_real_escape_string($conn->koneksi, $id_tarif);
    $jenis_kendaraan = mysqli_real_escape_string($conn->koneksi, $jenis_kendaraan);
    $tarif_per_jam = mysqli_real_escape_string($conn->koneksi, $tarif_per_jam);
    
    // Cek duplikat (kecuali data yang sedang diupdate)
    $cek_sql = "SELECT * FROM tb_tarif 
                WHERE jenis_kendaraan = '$jenis_kendaraan' 
                AND id_tarif != '$id_tarif'";
    $cek_query = mysqli_query($conn->koneksi, $cek_sql);
    
    if (mysqli_num_rows($cek_query) > 0) {
      echo "<script>
              alert('Jenis kendaraan sudah ada dalam database!');
              window.location='../views/admin/tarif.php';
            </script>";
      exit;
    }
    
    $sql  = "UPDATE tb_tarif SET
              jenis_kendaraan = '$jenis_kendaraan',
              tarif_per_jam = '$tarif_per_jam'
             WHERE id_tarif = '$id_tarif'";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      header("Location: ../views/admin/tarif.php");
      exit;
    } else {
      echo "<script>alert('Data gagal diubah');history.back()</script>";
    }
  }

  // =========================
  // HAPUS DATA
  // =========================
  public function hapus_data($id_tarif)
  {
    $conn = new koneksi();
    $id_tarif = mysqli_real_escape_string($conn->koneksi, $id_tarif);
    
    mysqli_query(
      $conn->koneksi,
      "DELETE FROM tb_tarif WHERE id_tarif='$id_tarif'"
    );

    header("Location: ../views/admin/tarif.php");
    exit;
  }
}