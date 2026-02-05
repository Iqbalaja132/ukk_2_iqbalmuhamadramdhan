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
  <title>Login - Sistem Parkir</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --primary: #2563eb;
      --primary-dark: #1e40af;
    }
  </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-sm">
    <!-- Card -->
    <div class="bg-white rounded-xl shadow-lg p-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg text-white font-bold text-lg mb-4" style="background: var(--primary);">
          <i class="fas fa-parking"></i>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Selamat Datang</h1>
        <p class="text-slate-600 text-sm mt-2">Silakan login untuk melanjutkan</p>
      </div>

      <!-- Form -->
      <form action="../controllers/c_login.php?aksi=login" method="post" class="space-y-4">
        <!-- Username Field -->
        <div>
          <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username</label>
          <input 
            type="text" 
            id="username" 
            name="username" 
            placeholder="Masukkan username" 
            required
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
          />
        </div>

        <!-- Password Field -->
        <div>
          <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="••••••••" 
            required
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
          />
        </div>

        <!-- Login Button -->
        <button 
          type="submit" 
          name="login"
          class="w-full py-2 px-4 rounded-lg font-semibold text-white transition-all duration-200 transform hover:scale-105"
          style="background: var(--primary);"
          onmouseover="this.style.background='var(--primary-dark)'"
          onmouseout="this.style.background='var(--primary)'"
        >
          Login
        </button>
      </form>
    </div>

    <!-- Footer Text -->
    <p class="text-center text-slate-600 text-sm mt-6">
      © 2025 Sistem Parkir. All rights reserved.
    </p>
  </div>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>

</html>
