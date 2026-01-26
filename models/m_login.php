<?php

include_once 'm_koneksi.php';

class login
{

  // function registrasi($nama_lengkap, $username, $password, $role, $status_aktif = 1)
  // {
  //   $conn = new koneksi();
  //   $sql = "INSERT INTO tb_users  VALUES (NULL, '$nama_lengkap', '$username', '$password', '$role', '$status_aktif')";
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

 function login($username, $password) {
    $conn = new koneksi();
    $sql = "SELECT * FROM tb_users WHERE username='$username'";
    $query = mysqli_query($conn->koneksi, $sql);
    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
      $_SESSION['data'] = $data;
      header("Location: ../views/{$data['role']}/dashboard.php");
      exit;
    } else {
      echo "<script>
        alert('Username atau Password salah');
        window.location='../views/login.php';
      </script>";
    }
  }
}
