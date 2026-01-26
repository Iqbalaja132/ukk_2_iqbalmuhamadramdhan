<?php
session_start();

// Cek session admin
if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

// Sesuaikan path controller user kamu di sini
include_once __DIR__ . '/../../controllers/c_users.php'; 
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar.php'; ?>
<link rel="stylesheet" href="../../assets/css/main.css">

<main class="main-content">
  <div class="top-header">
    <h2 class="page-title">Tabel User</h2>
    <button class="btn-main btn-blue" onclick="openTambah()">+ Tambah User</button>
  </div>

  <div class="card-box">
    <table class="grid-table">
      <thead>
        <tr>
          <th style="width: 50px; text-align: center;">No</th>
          <th>Nama Lengkap</th>
          <th>Username</th>
          <th style="width: 100px;">Role</th>
          <th style="width: 100px;">Status</th>
          <th style="width: 150px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($data_user)) : ?>
          <?php $no = 1; ?>
          <?php foreach ($data_user as $row) : ?>
            <?php
            $status_text = ($row->status_aktif == 1) ? 'AKTIF' : 'NON-AKTIF';
            $badge_status = ($row->status_aktif == 1) ? 'bg-soft-green' : 'bg-soft-red';
            
            $badge_role = 'bg-soft-blue'; 
            if($row->role == 'owner') $badge_role = 'bg-soft-red';
            if($row->role == 'petugas') $badge_role = 'bg-soft-green';
            ?>
            <tr>
              <td style="text-align: center;"><?= $no++; ?></td>
              <td><strong><?= strtoupper($row->nama_lengkap) ?></strong></td>
              <td><?= $row->username ?></td>
              <td><span class="badge-pill <?= $badge_role ?>"><?= strtoupper($row->role) ?></span></td>
              <td><span class="badge-pill <?= $badge_status ?>"><?= $status_text ?></span></td>
              <td style="text-align: center;">
                <a href="#" class="btn-outline btn-outline-blue"
                  onclick="openEdit('<?= $row->id_user ?>','<?= $row->nama_lengkap ?>','<?= $row->username ?>','<?= $row->role ?>','<?= $row->status_aktif ?>')">
                  Edit
                </a>
                <form action="../../controllers/c_user.php?aksi=hapus" method="post" style="display:inline">
                  <input type="hidden" name="id_user" value="<?= $row->id_user ?>">
                  <button class="btn-outline btn-outline-red" onclick="return confirm('Hapus user ini?')">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 30px; color: #9ca3af;">Data user belum tersedia.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<div class="modal" id="modalTambah">
  <div class="modal-content">
    <h3 style="margin-top:0">Tambah User Baru</h3>
    <br>
    <form action="../../controllers/c_users.php?aksi=tambah" method="post">
      <div class="modal-body">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" required>
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Role</label>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="petugas">Petugas</option>
            <option value="owner">Owner</option>
        </select>
        <label>Status</label>
        <select name="status_aktif">
            <option value="1">Aktif</option>
            <option value="0">Non-Aktif</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
        <button class="btn-main btn-blue">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal" id="modalEdit">
  <div class="modal-content">
    <h3 style="margin-top:0">Edit User</h3>
    <br>
    <form action="../../controllers/c_users.php?aksi=update" method="post">
      <input type="hidden" name="id_user" id="editId">
      <div class="modal-body">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" id="editNama" required>
        <label>Username</label>
        <input type="text" name="username" id="editUsername" required>
        <label>Password (Kosongkan jika tidak diubah)</label>
        <input type="password" name="password" placeholder="********">
        <label>Role</label>
        <select name="role" id="editRole">
            <option value="admin">Admin</option>
            <option value="petugas">Petugas</option>
            <option value="owner">Owner</option>
        </select>
        <!-- <label>Status</label>
        <select name="status_aktif" id="editStatus">
            <option value="1">Aktif</option>
            <option value="0">Non-Aktif</option>
        </select> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
        <button class="btn-main btn-blue">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openTambah() { document.getElementById('modalTambah').style.display = 'flex'; }
  function closeModal() { 
    document.getElementById('modalTambah').style.display = 'none'; 
    document.getElementById('modalEdit').style.display = 'none'; 
  }
  function openEdit(id, nama, username, role, status) {
    document.getElementById('editId').value = id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editUsername').value = username;
    document.getElementById('editRole').value = role;
    // document.getElementById('editStatus').value = status;
    document.getElementById('modalEdit').style.display = 'flex';
  }
</script>

<?php include '../templates/footer.php'; ?>