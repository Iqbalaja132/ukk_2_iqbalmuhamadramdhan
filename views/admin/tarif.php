<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

include_once __DIR__ . '/../../controllers/c_tarif.php';
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
          <th style="width: 75px;">No</th>
          <th style="width: 200px;">Jenis Kendaraan</th>
          <th style="width: 200px;">Tarif Per Jam</th>
          <th style="width: 150px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        <?php if (!empty($data_tarif)) : ?>
          <?php foreach ($data_tarif as $row) : ?>
            <tr>
              <td><span class="badge-pill bg-soft-blue"><?= $no++; ?></span></td>
              <td><?= $row->jenis_kendaraan ?></td>
              <td><?= $row->tarif_per_jam ?></td>
              <td>
                <a href="#" class="btn-outline btn-outline-blue"
                  onclick="openEdit('<?= $row->id_tarif ?>','<?= $row->jenis_kendaraan ?>',<?= $row->tarif_per_jam ?>)">
                  Edit
                </a>

                <form action="../../controllers/c_tarif.php?aksi=hapus"
                  method="post" style="display:inline">
                  <input type="hidden" name="id_tarif" value="<?= $row->id_tarif ?>">
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
    <h3 style="margin-top:0">Tambah Tarif</h3>
    <br>
    <form action="../../controllers/c_tarif.php?aksi=tambah" method="post">
      <div class="modal-body">
        <label>Jenis Kendaraan</label>
        <select name="jenis_kendaraan">
          <option value="motor">Motor</option>
          <option value="mobil">Mobil</option>
          <option value="lainnya">Lainnya</option>
        </select>
        <label>Tarif Per Jam</label>
        <input type="number" name="tarif_per_jam" placeholder="5000" required>
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
    <h3 style="margin-top:0">Edit Tarif</h3>
    <br>
    <form action="../../controllers/c_tarif.php?aksi=update" method="post">
      
      <input type="hidden" name="id_tarif" id="editId">

      <div class="modal-body">
        <label>Jenis Kendaraan</label>
        <select name="jenis_kendaraan" id="editJenis">
          <option value="motor">Motor</option>
          <option value="mobil">Mobil</option>
          <option value="lainnya">Lainnya</option>
        </select>

        <label>Tarif Per Jam</label>
        <input type="number" name="tarif_per_jam" id="editTarif" required>
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

  function openEdit(id, jenis, tarif) {
    editId.value = id;
    editJenis.value = jenis;
    editTarif.value = tarif;
    modalEdit.style.display = 'flex';
  }

  function closeModal() {
    modalTambah.style.display = 'none';
    modalEdit.style.display = 'none';
  }
</script>

<?php include '../templates/footer.php'; ?>