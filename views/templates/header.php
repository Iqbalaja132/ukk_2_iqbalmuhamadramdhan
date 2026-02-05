<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Sistem Parkir</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary: #2563eb;
      --primary-dark: #1e40af;
      --primary-light: #3b82f6;
      --bg-main: #f8fafc;
      --bg-card: #ffffff;
      --bg-sidebar: #1e293b;
      --bg-sidebar-hover: #334155;
      --text-primary: #1e293b;
      --text-light: #f1f5f9;
      --text-muted: #64748b;
      --danger: #ef4444;
      --shadow-sm: 0 2px 4px rgba(0,0,0,.05);
      --radius-sm: 8px;
      --transition: all .3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background: var(--bg-main);
      min-height: 100vh;
      overflow-x: hidden;
    }
  </style>
</head>
<body>

<header class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-40" style="background: var(--bg-card);">
  <div class="flex items-center gap-4">
    <button class="hamburger text-slate-700 text-xl cursor-pointer hover:bg-slate-100 p-2 rounded-lg transition-colors" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>
    <h2 class="text-xl font-bold text-slate-800">Sistem Parkir</h2>
  </div>

  <!-- Profile Dropdown -->
  <div class="relative group">
    <button class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-slate-100 transition-colors" onclick="toggleProfileMenu(event)">
      <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold" style="background: var(--primary);">
        <?php echo strtoupper(substr($_SESSION['data']['username'] ?? 'U', 0, 1)); ?>
      </div>
      <span class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($_SESSION['data']['username'] ?? 'User'); ?></span>
      <i class="fas fa-chevron-down text-xs text-slate-500"></i>
    </button>

    <!-- Dropdown Menu -->
    <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 hidden z-50">
      <a href="#" class="block px-4 py-3 text-slate-700 hover:bg-slate-50 transition-colors text-sm font-medium border-b border-slate-200">
        <i class="fas fa-user text-slate-500 mr-2"></i> Profile
      </a>
      <a href="#" class="block px-4 py-3 text-slate-700 hover:bg-slate-50 transition-colors text-sm font-medium border-b border-slate-200">
        <i class="fas fa-cog text-slate-500 mr-2"></i> Settings
      </a>
      <a href="#" class="block px-4 py-3 text-slate-700 hover:bg-slate-50 transition-colors text-sm font-medium border-b border-slate-200">
        <i class="fas fa-headset text-slate-500 mr-2"></i> Support
      </a>
      <a href="../../controllers/c_login.php?aksi=logout" class="block px-4 py-3 text-red-600 hover:bg-red-50 transition-colors text-sm font-medium" onclick="return confirm('Yakin ingin logout?')">
        <i class="fas fa-sign-out-alt text-red-500 mr-2"></i> Sign out
      </a>
    </div>
  </div>
</header>

<script>
  function toggleProfileMenu(event) {
    event.stopPropagation();
    const menu = document.getElementById('profileMenu');
    menu.classList.toggle('hidden');
  }

  // Close menu when clicking outside
  document.addEventListener('click', function(event) {
    const menu = document.getElementById('profileMenu');
    const button = event.target.closest('button');
    if (!button || !button.closest('[onclick*="toggleProfileMenu"]')) {
      menu.classList.add('hidden');
    }
  });
</script>
