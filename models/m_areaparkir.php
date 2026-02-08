<?php

include_once 'm_koneksi.php';

class area
{
  // =========================
  // TAMPIL DATA SEMUA
  // =========================
  public function tampil_data()
  {
    $conn = new koneksi();
    $sql  = "SELECT * FROM tb_area_parkir ORDER BY id_area DESC";
    $query = mysqli_query($conn->koneksi, $sql);

    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_object($query)) {
        // Hitung status dan persentase terisi
        $data->status = ($data->terisi >= $data->kapasitas) ? 'Penuh' : 'Tersedia';
        $data->persentase = $data->kapasitas > 0 ? round(($data->terisi / $data->kapasitas) * 100, 1) : 0;
        $data->tersisa = $data->kapasitas - $data->terisi;
        $hasil[] = $data;
      }
    }
    return $hasil;
  }

  // =========================
  // TAMPIL DATA DENGAN PAGINATION, SEARCH, DAN FILTER
  // =========================
  public function tampil_data_paginated($search = '', $status_filter = '', $page = 1, $limit = 10)
  {
    $conn = new koneksi();
    
    // Hitung offset
    $offset = ($page - 1) * $limit;
    
    // Build query dengan search dan filter
    $sql = "SELECT * FROM tb_area_parkir WHERE 1=1";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($conn->koneksi, $search);
      $sql .= " AND (
                nama_area LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter
    if (!empty($status_filter)) {
      if ($status_filter == 'tersedia') {
        $sql .= " AND terisi < kapasitas";
      } elseif ($status_filter == 'penuh') {
        $sql .= " AND terisi >= kapasitas";
      }
    }
    
    $sql .= " ORDER BY id_area DESC
              LIMIT $limit OFFSET $offset";
    
    $query = mysqli_query($conn->koneksi, $sql);

    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_object($query)) {
        // Hitung status dan persentase terisi
        $data->status = ($data->terisi >= $data->kapasitas) ? 'Penuh' : 'Tersedia';
        $data->persentase = $data->kapasitas > 0 ? round(($data->terisi / $data->kapasitas) * 100, 1) : 0;
        $data->tersisa = $data->kapasitas - $data->terisi;
        $hasil[] = $data;
      }
    }
    return $hasil;
  }

  // =========================
  // HITUNG TOTAL DATA UNTUK PAGINATION
  // =========================
  public function hitung_total_data($search = '', $status_filter = '')
  {
    $conn = new koneksi();
    
    $sql = "SELECT COUNT(*) as total FROM tb_area_parkir WHERE 1=1";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($conn->koneksi, $search);
      $sql .= " AND (
                nama_area LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter
    if (!empty($status_filter)) {
      if ($status_filter == 'tersedia') {
        $sql .= " AND terisi < kapasitas";
      } elseif ($status_filter == 'penuh') {
        $sql .= " AND terisi >= kapasitas";
      }
    }
    
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['total'] ?? 0;
  }

  // =========================
  // HITUNG TOTAL KAPASITAS
  // =========================
  public function hitung_total_kapasitas()
  {
    $conn = new koneksi();
    $sql = "SELECT SUM(kapasitas) as total FROM tb_area_parkir";
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['total'] ?? 0;
  }

  // =========================
  // HITUNG TOTAL TERISI
  // =========================
  public function hitung_total_terisi()
  {
    $conn = new koneksi();
    $sql = "SELECT SUM(terisi) as total FROM tb_area_parkir";
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['total'] ?? 0;
  }

  // =========================
  // HITUNG AREA TERSEDIA
  // =========================
  public function hitung_area_tersedia()
  {
    $conn = new koneksi();
    $sql = "SELECT COUNT(*) as total FROM tb_area_parkir WHERE terisi < kapasitas";
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['total'] ?? 0;
  }

  // =========================
  // HITUNG AREA PENUH
  // =========================
  public function hitung_area_penuh()
  {
    $conn = new koneksi();
    $sql = "SELECT COUNT(*) as total FROM tb_area_parkir WHERE terisi >= kapasitas";
    $query = mysqli_query($conn->koneksi, $sql);
    $result = mysqli_fetch_assoc($query);
    
    return $result['total'] ?? 0;
  }

  // =========================
  // TAMBAH DATA
  // =========================
  public function tambah_data($nama_area, $kapasitas, $terisi)
  {
    $conn = new koneksi();
    $nama_area = mysqli_real_escape_string($conn->koneksi, $nama_area);
    $kapasitas = mysqli_real_escape_string($conn->koneksi, $kapasitas);
    $terisi = mysqli_real_escape_string($conn->koneksi, $terisi);
    
    // Cek apakah nama area sudah ada
    $cek_sql = "SELECT * FROM tb_area_parkir WHERE nama_area = '$nama_area'";
    $cek_query = mysqli_query($conn->koneksi, $cek_sql);
    
    if (mysqli_num_rows($cek_query) > 0) {
      echo "<script>
              alert('Nama area parkir sudah ada dalam database!');
              window.location='../views/admin/area_parkir.php';
            </script>";
      exit;
    }
    
    $sql  = "INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi)
             VALUES ('$nama_area', '$kapasitas', '$terisi')";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      header("Location: ../views/admin/area_parkir.php");
      exit;
    } else {
      echo "<script>alert('Data gagal ditambahkan');history.back()</script>";
    }
  }

  // =========================
  // AMBIL DATA BY ID
  // =========================
  public function tampil_data_byid($id_area)
  {
    $conn = new koneksi();
    $id_area = mysqli_real_escape_string($conn->koneksi, $id_area);
    $sql  = "SELECT * FROM tb_area_parkir WHERE id_area = '$id_area'";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query && mysqli_num_rows($query) > 0) {
      $data = mysqli_fetch_object($query);
      // Hitung status dan persentase
      $data->status = ($data->terisi >= $data->kapasitas) ? 'Penuh' : 'Tersedia';
      $data->persentase = $data->kapasitas > 0 ? round(($data->terisi / $data->kapasitas) * 100, 1) : 0;
      $data->tersisa = $data->kapasitas - $data->terisi;
      return $data;
    }

    return null;
  }

  // =========================
  // UPDATE DATA
  // =========================
  public function edit_data($id_area, $nama_area, $kapasitas, $terisi)
  {
    $conn = new koneksi();
    $id_area = mysqli_real_escape_string($conn->koneksi, $id_area);
    $nama_area = mysqli_real_escape_string($conn->koneksi, $nama_area);
    $kapasitas = mysqli_real_escape_string($conn->koneksi, $kapasitas);
    $terisi = mysqli_real_escape_string($conn->koneksi, $terisi);
    
    // Cek duplikat nama area (kecuali data yang sedang diupdate)
    $cek_sql = "SELECT * FROM tb_area_parkir 
                WHERE nama_area = '$nama_area' 
                AND id_area != '$id_area'";
    $cek_query = mysqli_query($conn->koneksi, $cek_sql);
    
    if (mysqli_num_rows($cek_query) > 0) {
      echo "<script>
              alert('Nama area parkir sudah ada dalam database!');
              window.location='../views/admin/area_parkir.php';
            </script>";
      exit;
    }
    
    $sql  = "UPDATE tb_area_parkir SET
              nama_area = '$nama_area',
              kapasitas = '$kapasitas',
              terisi    = '$terisi'
             WHERE id_area = '$id_area'";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      header("Location: ../views/admin/area_parkir.php");
      exit;
    } else {
      echo "<script>alert('Data gagal diubah');history.back()</script>";
    }
  }

  // =========================
  // HAPUS DATA
  // =========================
  public function hapus_data($id_area)
  {
    $conn = new koneksi();
    $id_area = mysqli_real_escape_string($conn->koneksi, $id_area);
    
    mysqli_query(
      $conn->koneksi,
      "DELETE FROM tb_area_parkir WHERE id_area='$id_area'"
    );

    header("Location: ../views/admin/area_parkir.php");
    exit;
  }
}