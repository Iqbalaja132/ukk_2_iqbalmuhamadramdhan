<?php

// jika sudah login, arahkan ke dashboard
if (isset($_SESSION['data'])) {
    header("Location: views/dashboard.php");
    exit;
}

// jika belum login, tampilkan halaman login
include "views/login.php";
?>