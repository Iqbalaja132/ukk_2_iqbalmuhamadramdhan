<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include_once __DIR__ . '/../../controllers/c_areaparkir.php';
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar.php'; ?>
<link rel="stylesheet" href="../../assets/css/main.css">

<main class="main-content">

  <div class="top-header">
    <h2 class="page-title">Tabel Area Parkir</h2>
    <button class="btn-main btn-blue" onclick="openTambah()">Tambah Area</button>
  </div>

  <div class="card-box">
    <table class="grid-table">
      <thead>
        <tr>
          <th style="width: 75px;">ID</th>
          <th>Nama Area</th>
          <th style="width: 200px;">Kapasitas</th>
          <th style="width: 200px;">Terisi</th>
          <th style="width: 100px;">Status</th>
          <th style="width: 150px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>

        <?php if (!empty($data_area)) : ?>
          <?php foreach ($data_area as $row) : ?>
            <?php
            $status = ($row->terisi >= $row->kapasitas) ? 'Penuh' : 'Tersedia';
            $badge  = ($status == 'Penuh') ? 'bg-soft-red' : 'bg-soft-green';
            ?>
            <tr>
              <td><span class="badge-pill bg-soft-blue">AR<?= $row->id_area ?></span></td>
              <td><b><?= $row->nama_area ?></b></td>
              <td><?= $row->kapasitas ?></td>
              <td><?= $row->terisi ?></td>
              <td><span class="badge-pill <?= $badge ?>"><?= $status ?></span></td>
              <td>
                <a href="#" class="btn-outline btn-outline-blue"
                  onclick="openEdit('<?= $row->id_area ?>','<?= $row->nama_area ?>',<?= $row->kapasitas ?>,<?= $row->terisi ?>)">
                  Edit
                </a>

                <form action="../../controllers/c_areaparkir.php?aksi=hapus"
                  method="post" style="display:inline">
                  <input type="hidden" name="id_area" value="<?= $row->id_area ?>">
                  <button
                    class="btn-outline btn-outline-red"
                    onclick="return confirm('Apa anda yakin akan menghapus ini?')">
                    Hapus
                  </button>

                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="6" align="center">Data kosong</td>
          </tr>
        <?php endif; ?>

      </tbody>
    </table>
  </div>
</main>

<!-- MODAL TAMBAH -->
<div class="modal" id="modalTambah">
  <div class="modal-content">
    <h3 style="margin-top:0">Tambah Area Parkir</h3>
    <br>
    <form action="../../controllers/c_areaparkir.php?aksi=tambah" method="post">
      <div class="modal-body">
        <input type="text" name="nama_area" placeholder="Nama Area" required>
        <input type="number" name="kapasitas" placeholder="Kapasitas" required>
        <input type="number" name="terisi" placeholder="Terisi" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
        <button class="btn-main btn-blue">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal" id="modalEdit">
  <div class="modal-content">
    <h3 style="margin-top:0">Edit Area Parkir</h3>
    <br>
    <form action="../../controllers/c_areaparkir.php?aksi=update" method="post">
      <input type="hidden" name="id_area" id="editId">
      <div class="modal-body">
        <input type="text" name="nama_area" id="editNama" required>
        <input type="number" name="kapasitas" id="editKapasitas" required>
        <input type="number" name="terisi" id="editTerisi" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
        <button class="btn-main btn-blue">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openTambah() {
    modalTambah.style.display = 'flex';
  }

  function openEdit(id, nama, kapasitas, terisi) {
    editId.value = id;
    editNama.value = nama;
    editKapasitas.value = kapasitas;
    editTerisi.value = terisi;
    modalEdit.style.display = 'flex';
  }

  function closeModal() {
    modalTambah.style.display = 'none';
    modalEdit.style.display = 'none';
  }
</script>

<?php include '../templates/footer.php'; ?>