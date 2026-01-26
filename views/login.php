<?php
session_start();

if (isset($_SESSION['data'])) {
  $role = $_SESSION['data']['role'];

  header("Location: $role/dashboard.php");
  exit;
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="../assets/css/login.css">

</head>

<body>
  <div class="login-card">
    <h1>Selamat Datang</h1>
    <p>Silakan login untuk melanjutkan</p>

    <form action="../controllers/c_login.php?aksi=login" method="post">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" placeholder="Username" name="username" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="••••••••" name="password" required />
      </div>

      <button type="submit" name="login">Login</button>
    </form>
  </div>
</body>

</html>