<?php

include_once 'm_koneksi.php';

class user
{
  private $conn;
  
  public function __construct() {
    $koneksi = new koneksi();
    $this->conn = $koneksi->koneksi;
  }

  // =========================
  // TAMPIL DATA DENGAN PAGINATION, SEARCH, DAN FILTER (DIPERBAIKI)
  // =========================
  public function tampil_data_paginated($search = '', $role_filter = '', $status_filter = '', $page = 1, $limit = 10)
  {
    // Hitung offset
    $offset = ($page - 1) * $limit;
    
    // Build query dengan search dan filter
    $sql = "SELECT * FROM tb_user WHERE 1=1";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($this->conn, $search);
      $sql .= " AND (
                nama_lengkap LIKE '%$search%' OR 
                username LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter role
    if (!empty($role_filter)) {
      $role_filter = mysqli_real_escape_string($this->conn, $role_filter);
      $sql .= " AND role = '$role_filter'";
    }
    
    // PERBAIKAN: Filter status harus menerima string '0' untuk non-aktif
    if ($status_filter !== '') {
      $status_filter = mysqli_real_escape_string($this->conn, $status_filter);
      $sql .= " AND status_aktif = '$status_filter'";
    }
    
    $sql .= " ORDER BY id_user DESC
              LIMIT $limit OFFSET $offset";
    
    $query = mysqli_query($this->conn, $sql);

    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_object($query)) {
        // Konversi nama lengkap ke uppercase saat menampilkan
        $data->nama_lengkap = strtoupper($data->nama_lengkap);
        $hasil[] = $data;
      }
    }
    return $hasil;
  }

  // =========================
  // HITUNG TOTAL DATA UNTUK PAGINATION (DIPERBAIKI)
  // =========================
  public function hitung_total_data($search = '', $role_filter = '', $status_filter = '')
  {
    $sql = "SELECT COUNT(*) as total
            FROM tb_user
            WHERE 1=1";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($this->conn, $search);
      $sql .= " AND (
                nama_lengkap LIKE '%$search%' OR 
                username LIKE '%$search%'
              )";
    }
    
    // Tambahkan kondisi filter role
    if (!empty($role_filter)) {
      $role_filter = mysqli_real_escape_string($this->conn, $role_filter);
      $sql .= " AND role = '$role_filter'";
    }
    
    // PERBAIKAN: Filter status harus menerima string '0' untuk non-aktif
    if ($status_filter !== '') {
      $status_filter = mysqli_real_escape_string($this->conn, $status_filter);
      $sql .= " AND status_aktif = '$status_filter'";
    }
    
    $query = mysqli_query($this->conn, $sql);
    
    if ($query) {
      $result = mysqli_fetch_assoc($query);
      return $result['total'] ?? 0;
    }
    
    return 0;
  }

  // =========================
  // HITUNG JUMLAH PER ROLE (DIPERBAIKI)
  // =========================
  public function hitung_per_role($role, $search = '', $status_filter = '')
  {
    $sql = "SELECT COUNT(*) as total
            FROM tb_user
            WHERE role = '" . mysqli_real_escape_string($this->conn, $role) . "'";
    
    // Tambahkan kondisi search
    if (!empty($search)) {
      $search = mysqli_real_escape_string($this->conn, $search);
      $sql .= " AND (
                nama_lengkap LIKE '%$search%' OR 
                username LIKE '%$search%'
              )";
    }
    
    // PERBAIKAN: Filter status harus menerima string '0' untuk non-aktif
    if ($status_filter !== '') {
      $status_filter = mysqli_real_escape_string($this->conn, $status_filter);
      $sql .= " AND status_aktif = '$status_filter'";
    }
    
    $query = mysqli_query($this->conn, $sql);
    
    if ($query) {
      $result = mysqli_fetch_assoc($query);
      return $result['total'] ?? 0;
    }
    
    return 0;
  }

  // =========================
  // TAMPIL DATA SEMUA (UNTUK KOMPATIBILITAS)
  // =========================
  public function tampil_data()
  {
    return $this->tampil_data_paginated('', '', '', 1, 1000);
  }

  // =========================
  // TAMBAH DATA USER
  // =========================
  public function tambah_data($nama_lengkap, $username, $password, $role, $status_aktif)
  {
    // Konversi ke uppercase sebelum disimpan
    $nama_lengkap = mysqli_real_escape_string($this->conn, strtoupper($nama_lengkap));
    $username = mysqli_real_escape_string($this->conn, $username);
    $role = mysqli_real_escape_string($this->conn, $role);
    $status_aktif = mysqli_real_escape_string($this->conn, $status_aktif);
    
    // Cek apakah username sudah ada
    $check_sql = "SELECT * FROM tb_user WHERE username = '$username'";
    $check_query = mysqli_query($this->conn, $check_sql);
    
    if (mysqli_num_rows($check_query) > 0) {
        echo "<script>alert('Username sudah digunakan!');history.back()</script>";
        return;
    }
    
    // Enkripsi password sebelum disimpan
    $pass_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql  = "INSERT INTO tb_user (nama_lengkap, username, password, role, status_aktif)
             VALUES ('$nama_lengkap', '$username', '$pass_hash', '$role', '$status_aktif')";
    $query = mysqli_query($this->conn, $sql);

    if ($query) {
      echo "<script>
        alert('User berhasil ditambahkan');
        window.location.href = '../views/admin/user.php';
      </script>";
      exit;
    } else {
      echo "<script>alert('User gagal ditambahkan');history.back()</script>";
    }
  }

  // =========================
  // AMBIL DATA USER BY ID
  // =========================
  public function tampil_data_byid($id_user)
  {
    $id_user = mysqli_real_escape_string($this->conn, $id_user);
    
    $sql  = "SELECT * FROM tb_user WHERE id_user = '$id_user'";
    $query = mysqli_query($this->conn, $sql);

    if ($query && mysqli_num_rows($query) > 0) {
      $data = mysqli_fetch_object($query);
      $data->nama_lengkap = strtoupper($data->nama_lengkap);
      return $data;
    }

    return null;
  }

  // =========================
  // UPDATE DATA USER
  // =========================
  public function edit_data($id_user, $nama_lengkap, $username, $password, $role, $status_aktif)
  {
    $id_user = mysqli_real_escape_string($this->conn, $id_user);
    
    // Konversi ke uppercase sebelum disimpan
    $nama_lengkap = mysqli_real_escape_string($this->conn, strtoupper($nama_lengkap));
    $username = mysqli_real_escape_string($this->conn, $username);
    $role = mysqli_real_escape_string($this->conn, $role);
    
    // Cek apakah username sudah digunakan oleh user lain
    $check_sql = "SELECT * FROM tb_user WHERE username = '$username' AND id_user != '$id_user'";
    $check_query = mysqli_query($this->conn, $check_sql);
    
    if (mysqli_num_rows($check_query) > 0) {
        echo "<script>alert('Username sudah digunakan oleh user lain!');history.back()</script>";
        return;
    }
    
    // Build query dengan password opsional
    $query_pass = "";
    if (!empty($password)) {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $query_pass = ", password = '$pass_hash'";
    }

    // Build query dengan status opsional
    $query_status = "";
    if ($status_aktif !== null) {
        $status_aktif = mysqli_real_escape_string($this->conn, $status_aktif);
        $query_status = ", status_aktif = '$status_aktif'";
    }

    // Gabungkan dalam satu Query
    $sql = "UPDATE tb_user SET 
                nama_lengkap = '$nama_lengkap', 
                username = '$username', 
                role = '$role' 
                $query_pass 
                $query_status 
            WHERE id_user = '$id_user'";

    $query = mysqli_query($this->conn, $sql);

    if ($query) {
      echo "<script>
        alert('Data berhasil diubah');
        window.location.href = '../views/admin/user.php';
      </script>";
      exit;
    } else {
      echo "<script>alert('Gagal mengubah data');history.back()</script>";
    }
  }

  // =========================
  // HAPUS DATA USER
  // =========================
  public function hapus_data($id_user)
  {
    $id_user = mysqli_real_escape_string($this->conn, $id_user);
    
    // Cek apakah session sudah dimulai
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Cek apakah user sedang login
        if (isset($_SESSION['data']['id_user']) && $_SESSION['data']['id_user'] == $id_user) {
            echo "<script>alert('Tidak dapat menghapus akun yang sedang login!');history.back()</script>";
            return false;
        }
    }
    
    // Cek apakah user ada sebelum dihapus
    $check_sql = "SELECT * FROM tb_user WHERE id_user = '$id_user'";
    $check_query = mysqli_query($this->conn, $check_sql);
    
    if (mysqli_num_rows($check_query) == 0) {
        echo "<script>alert('User tidak ditemukan!');history.back()</script>";
        return false;
    }
    
    $sql = "DELETE FROM tb_user WHERE id_user='$id_user'";
    $query = mysqli_query($this->conn, $sql);

    if ($query) {
        echo "<script>
            alert('User berhasil dihapus');
            window.location.href = '../views/admin/user.php';
        </script>";
        exit;
    } else {
        echo "<script>alert('Gagal menghapus user');history.back()</script>";
        return false;
    }
  }
}