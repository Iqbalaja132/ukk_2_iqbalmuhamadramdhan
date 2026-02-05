<?php

include_once 'm_koneksi.php';

class user
{
  // =========================
  // AMBIL SEMUA DATA USER
  // =========================
  public function tampil_data()
  {
    $conn = new koneksi();
    $sql  = "SELECT * FROM tb_user ORDER BY id_user DESC";
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
  // TAMBAH DATA USER (Dengan Hash)
  // =========================
  public function tambah_data($nama_lengkap, $username, $password, $role, $status_aktif)
  {
    $conn = new koneksi();
    
    // Enkripsi password sebelum disimpan
    $pass_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql  = "INSERT INTO tb_user (nama_lengkap, username, password, role, status_aktif)
             VALUES ('$nama_lengkap', '$username', '$pass_hash', '$role', '$status_aktif')";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
      echo "<script>alert('User berhasil ditambahkan');window.location='../views/admin/user.php'</script>";
    } else {
      echo "<script>alert('User gagal ditambahkan');history.back()</script>";
    }
  }

  // =========================
  // AMBIL DATA USER BY ID
  // =========================
  public function tampil_data_byid($id_user)
  {
    $conn = new koneksi();
    $sql  = "SELECT * FROM tb_user WHERE id_user = '$id_user'";
    $query = mysqli_query($conn->koneksi, $sql);

    if ($query && $query->num_rows > 0) {
      return mysqli_fetch_object($query);
    }

    return null;
  }

  // =========================
  // UPDATE DATA USER
  // =========================
public function edit_data($id_user, $nama_lengkap, $username, $password, $role, $status_aktif)
{
    $conn = new koneksi();
    
    // 1. Logika Password (seperti sebelumnya)
    $query_pass = "";
    if (!empty($password)) {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $query_pass = ", password = '$pass_hash'";
    }

    // 2. Logika Status (Hanya update jika dikirim dari form)
    $query_status = "";
    if ($status_aktif !== null) {
        $query_status = ", status_aktif = '$status_aktif'";
    }

    // 3. Gabungkan dalam satu Query
    $sql = "UPDATE tb_user SET 
                nama_lengkap = '$nama_lengkap', 
                username = '$username', 
                role = '$role' 
                $query_pass 
                $query_status 
            WHERE id_user = '$id_user'";

    $query = mysqli_query($conn->koneksi, $sql);

    if ($query) {
        echo "<script>alert('Data berhasil diubah');window.location='../views/admin/user.php'</script>";
    } else {
        echo "<script>alert('Gagal mengubah data');history.back()</script>";
    }
}

  // =========================
  // HAPUS DATA USER
  // =========================
  public function hapus_data($id_user)
  {
    $conn = new koneksi();
    $sql  = "DELETE FROM tb_user WHERE id_user = '$id_user'";
    mysqli_query($conn->koneksi, $sql);

    header("Location: ../views/admin/user.php");
    exit;
  }
}