<?php

include_once 'm_koneksi.php';

class kendaraan
{
  // =========================
  // TAMPIL DATA
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
        $hasil[] = $data;
      }
    }
    return $hasil;
  }

  // =========================
  // TAMBAH DATA
  // =========================
  public function tambah_data($plat, $jenis, $warna, $pemilik, $id_user)
  {
    $conn = new koneksi();
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
    mysqli_query(
      $conn->koneksi,
      "DELETE FROM tb_kendaraan WHERE id_kendaraan='$id'"
    );

    header("Location: ../views/admin/kendaraan.php");
    exit;
  }
}
