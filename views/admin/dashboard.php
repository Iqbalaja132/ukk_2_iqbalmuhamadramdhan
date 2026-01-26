<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar.php'; ?>

<main class="content">
  <h2>Dashboard Admin</h2>
</main>

<?php include '../templates/footer.php'; ?>
