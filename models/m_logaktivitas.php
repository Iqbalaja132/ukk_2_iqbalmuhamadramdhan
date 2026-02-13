<?php

include_once 'm_koneksi.php';

class logaktivitas
{
  private $conn;
  
  public function __construct() {
    $koneksi = new koneksi();
    $this->conn = $koneksi->koneksi;
  }

  public function tampil_data_paginated($search = '', $date_filter = '', $user_filter = '', $page = 1, $limit = 15)
  {
    $offset = ($page - 1) * $limit;
    $sql = "SELECT l.*, u.nama_lengkap, u.username, u.role 
            FROM tb_log_aktivitas l 
            LEFT JOIN tb_user u ON l.Id_user = u.id_user 
            WHERE 1=1";
    
    if (!empty($search)) {
      $search = mysqli_real_escape_string($this->conn, $search);
      $sql .= " AND (
                l.aktivitas LIKE '%$search%' OR 
                u.nama_lengkap LIKE '%$search%' OR
                u.username LIKE '%$search%'
              )";
    }
    
    if (!empty($date_filter)) {
      $date_filter = mysqli_real_escape_string($this->conn, $date_filter);
      $sql .= " AND DATE(l.waktu_aktivitas) = '$date_filter'";
    }
    
    if (!empty($user_filter)) {
      $user_filter = mysqli_real_escape_string($this->conn, $user_filter);
      $sql .= " AND l.Id_user = '$user_filter'";
    }
    
    $sql .= " ORDER BY l.waktu_aktivitas DESC
              LIMIT $limit OFFSET $offset";
    
    $query = mysqli_query($this->conn, $sql);

    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_object($query)) {
        $data->waktu_format = date('d/m/Y', strtotime($data->waktu_aktivitas));
        $data->jam_format = date('H:i:s', strtotime($data->waktu_aktivitas));
        $hasil[] = $data;
      }
    }
    return $hasil;
  }

  public function hitung_total_data($search = '', $date_filter = '', $user_filter = '')
  {
    $sql = "SELECT COUNT(*) as total
            FROM tb_log_aktivitas l
            LEFT JOIN tb_user u ON l.Id_user = u.id_user
            WHERE 1=1";
    
    if (!empty($search)) {
      $search = mysqli_real_escape_string($this->conn, $search);
      $sql .= " AND (
                l.aktivitas LIKE '%$search%' OR 
                u.nama_lengkap LIKE '%$search%' OR
                u.username LIKE '%$search%'
              )";
    }
    
    if (!empty($date_filter)) {
      $date_filter = mysqli_real_escape_string($this->conn, $date_filter);
      $sql .= " AND DATE(l.waktu_aktivitas) = '$date_filter'";
    }
    
    if (!empty($user_filter)) {
      $user_filter = mysqli_real_escape_string($this->conn, $user_filter);
      $sql .= " AND l.Id_user = '$user_filter'";
    }
    
    $query = mysqli_query($this->conn, $sql);
    
    if ($query) {
      $result = mysqli_fetch_assoc($query);
      return $result['total'] ?? 0;
    }
    
    return 0;
  }

  public function hitung_hari_ini()
  {
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) as total
            FROM tb_log_aktivitas 
            WHERE DATE(waktu_aktivitas) = '$today'";
    
    $query = mysqli_query($this->conn, $sql);
    
    if ($query) {
      $result = mysqli_fetch_assoc($query);
      return $result['total'] ?? 0;
    }
    
    return 0;
  }

  public function hitung_user_aktif_hari_ini()
  {
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(DISTINCT Id_user) as total
            FROM tb_log_aktivitas 
            WHERE DATE(waktu_aktivitas) = '$today'";
    
    $query = mysqli_query($this->conn, $sql);
    
    if ($query) {
      $result = mysqli_fetch_assoc($query);
      return $result['total'] ?? 0;
    }
    
    return 0;
  }

  public function get_daftar_user()
  {
    $sql = "SELECT DISTINCT u.id_user, u.nama_lengkap, u.username 
            FROM tb_log_aktivitas l
            JOIN tb_user u ON l.Id_user = u.id_user
            ORDER BY u.nama_lengkap";
    
    $query = mysqli_query($this->conn, $sql);
    
    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_assoc($query)) {
        $hasil[] = $data;
      }
    }
    return $hasil;
  }

  public function tampil_data()
  {
    return $this->tampil_data_paginated('', '', '', 1, 1000);
  }

  public function tambah_log($id_user, $aktivitas)
  {
    if (empty($id_user) || !is_numeric($id_user)) {
      $id_user = 'NULL';
    } else {
      $id_user = "'" . mysqli_real_escape_string($this->conn, $id_user) . "'";
    }
    
    $aktivitas = mysqli_real_escape_string($this->conn, $aktivitas);
    
    $sql = "INSERT INTO tb_log_aktivitas (Id_user, aktivitas, waktu_aktivitas) 
            VALUES ($id_user, '$aktivitas', NOW())";
    
    return mysqli_query($this->conn, $sql);
  }

  public function export_csv($search = '', $date_filter = '', $user_filter = '')
  {
    $sql = "SELECT l.*, u.nama_lengkap, u.username 
            FROM tb_log_aktivitas l 
            LEFT JOIN tb_user u ON l.Id_user = u.id_user 
            WHERE 1=1";
    
    if (!empty($search)) {
      $search = mysqli_real_escape_string($this->conn, $search);
      $sql .= " AND (
                l.aktivitas LIKE '%$search%' OR 
                u.nama_lengkap LIKE '%$search%' OR
                u.username LIKE '%$search%'
              )";
    }
    
    if (!empty($date_filter)) {
      $date_filter = mysqli_real_escape_string($this->conn, $date_filter);
      $sql .= " AND DATE(l.waktu_aktivitas) = '$date_filter'";
    }
    
    if (!empty($user_filter)) {
      $user_filter = mysqli_real_escape_string($this->conn, $user_filter);
      $sql .= " AND l.Id_user = '$user_filter'";
    }
    
    $sql .= " ORDER BY l.waktu_aktivitas DESC";
    
    $query = mysqli_query($this->conn, $sql);
    
    $hasil = [];
    if ($query) {
      while ($data = mysqli_fetch_assoc($query)) {
        $hasil[] = $data;
      }
    }
    return $hasil;
  }
}
?>