<?php

include_once 'm_koneksi.php';

class tarif
{
  // =========================
  // AMBIL SEMUA DATA
  // =========================
  public function tampil_data()
  {
    $conn = new koneksi();
    $sql  = "SELECT * FROM tb_tarif ORDER BY id_tarif DESC";
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
  public function tambah_data($jenis_kendaraan, $tarif_per_jam)
  {
    $conn = new koneksi();
    $sql  = "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam)
             VALUES ('$jenis_kendaraan', '$tarif_per_jam')";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      echo "<script>alert('Data berhasil ditambahkan');window.location='../views/admin/tarif.php'</script>";
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
  public function edit_data($id_tarif, $jenis_kendaraan, $tarif_per_jam)
  {
    $conn = new koneksi();
    $sql  = "UPDATE tb_tarif SET
              jenis_kendaraan = '$jenis_kendaraan',
              tarif_per_jam = '$tarif_per_jam'
             WHERE id_tarif = '$id_tarif'";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      echo "<script>alert('Data berhasil diubah');window.location='../views/admin/tarif.php'</script>";
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
    $sql  = "DELETE FROM tb_tarif WHERE id_tarif = '$id_tarif'";
    mysqli_query($conn->koneksi, $sql);

    header("Location: ../views/admin/tarif.php");
    exit;
  }
}
