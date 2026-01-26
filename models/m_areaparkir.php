<?php

include_once 'm_koneksi.php';

class area
{
  // =========================
  // AMBIL SEMUA DATA
  // =========================
  public function tampil_data()
  {
    $conn = new koneksi();
    $sql  = "SELECT * FROM tb_area_parkir ORDER BY id_area DESC";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query && $query->num_rows > 0) {
      $hasil = [];
      while ($data = mysqli_fetch_object($query)) {
        $hasil[] = $data;
      }
      return $hasil;
    }

    return [];
  }

  // =========================
  // TAMBAH DATA
  // =========================
  public function tambah_data($nama_area, $kapasitas, $terisi)
  {
    $conn = new koneksi();
    $sql  = "INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi)
             VALUES ('$nama_area', '$kapasitas', '$terisi')";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      echo "<script>alert('Data berhasil ditambahkan');window.location='../views/admin/area_parkir.php'</script>";
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
    $sql  = "SELECT * FROM tb_area_parkir WHERE id_area = '$id_area'";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query && $query->num_rows > 0) {
      return mysqli_fetch_object($query);
    }

    return null;
  }

  // =========================
  // UPDATE DATA
  // =========================
  public function edit_data($id_area, $nama_area, $kapasitas, $terisi)
  {
    $conn = new koneksi();
    $sql  = "UPDATE tb_area_parkir SET
              nama_area = '$nama_area',
              kapasitas = '$kapasitas',
              terisi    = '$terisi'
             WHERE id_area = '$id_area'";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      echo "<script>alert('Data berhasil diubah');window.location='../views/admin/area_parkir.php'</script>";
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
    $sql  = "DELETE FROM tb_area_parkir WHERE id_area = '$id_area'";
    mysqli_query($conn->koneksi, $sql);

    header("Location: ../views/admin/area_parkir.php");
    exit;
  }
}
