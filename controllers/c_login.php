<?php
session_start();
include_once '../models/m_login.php';
include_once '../models/m_logaktivitas.php';

if (isset($_GET['aksi'])) { 

  if ($_GET['aksi'] == 'login') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $log_model = new logaktivitas();
    
    $login = new login();
    $login->login($username, $password, $log_model);
    
  } elseif ($_GET['aksi'] == 'logout') {

    if (isset($_SESSION['data'])) {
      include_once '../models/m_logaktivitas.php';
      $log_model = new logaktivitas();
      $user_id = $_SESSION['data']['id_user'];
      $log_model->tambah_log($user_id, 'User logout dari sistem');
    }
    
    session_unset();
    session_destroy();
    header("Location: ../views/login.php");
    exit;
  }
}
?>