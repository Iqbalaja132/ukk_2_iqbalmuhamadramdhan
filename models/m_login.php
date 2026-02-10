<?php

include_once 'm_koneksi.php';

class login
{
  // function registrasi($nama_lengkap, $username, $password, $role, $status_aktif = 1)
  // {
  //   $conn = new koneksi();
  //   $sql = "INSERT INTO tb_user  VALUES (NULL, '$nama_lengkap', '$username', '$password', '$role', '$status_aktif')";
  //   $query = mysqli_query($conn->koneksi, $sql);

  //   if ($query) {
  //     echo "<script>
  //               alert('Registrasi berhasil');
  //               window.location='../views/login.php';
  //             </script>";
  //   } else {
  //     echo "<script>
  //               alert('Registrasi gagal');
  //               window.location='../views/registrasi.php';
  //             </script>";
  //   }
  // }

  function login($username, $password, $log_model = null) {
    $conn = new koneksi();
    $sql = "SELECT * FROM tb_user WHERE username='$username'";
    $query = mysqli_query($conn->koneksi, $sql);
    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
      $_SESSION['data'] = $data;
      
      if ($log_model !== null) {
        $log_model->tambah_log($data['id_user'], 'User login ke sistem');
      }
      
      header("Location: ../views/{$data['role']}/dashboard.php");
      exit;
    } else {
      if ($log_model !== null) {
        $sql_user = "SELECT id_user FROM tb_user WHERE username='$username'";
        $query_user = mysqli_query($conn->koneksi, $sql_user);
        $user_data = mysqli_fetch_assoc($query_user);
        
        if ($user_data) {
          $log_model->tambah_log($user_data['id_user'], 'Percobaan login gagal - password salah');
        } else {
          $log_model->tambah_log(0, "Percobaan login gagal - username '$username' tidak ditemukan");
        }
      }
      
      echo "<script>
        alert('Username atau Password salah');
        window.location='../views/login.php';
      </script>";
    }
  }
}