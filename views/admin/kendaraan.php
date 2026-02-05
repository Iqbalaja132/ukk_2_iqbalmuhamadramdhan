<?php
session_start();

if (!isset($_SESSION['data']) || $_SESSION['data']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Memanggil controller
include_once __DIR__ . '/../../controllers/c_kendaraan.php';
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar.php'; ?>
<link rel="stylesheet" href="../../assets/css/main.css">

<main class="main-content">

    <div class="top-header">
        <h2 class="page-title">Data Kendaraan</h2>
        <button class="btn-main btn-blue" onclick="openTambah()">Tambah Kendaraan</button>
    </div>

    <div class="card-box">
        <table class="grid-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Plat Nomor</th>
                    <th>Jenis</th>
                    <th>Warna</th>
                    <th>Pemilik</th>
                    <th>Petugas</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                // Cek apakah variabel $data_kendaraan ada dan tidak kosong
                if (!empty($data_kendaraan)) : 
                    foreach ($data_kendaraan as $row): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row->plat_nomor) ?></td>
                    <td><?= htmlspecialchars($row->jenis_kendaraan) ?></td>
                    <td><?= htmlspecialchars($row->warna) ?></td>
                    <td><?= htmlspecialchars($row->pemilik) ?></td>
                    <td><?= htmlspecialchars($row->nama_lengkap) ?></td>
                    <td align="center">
                        <a href="javascript:void(0)" class="btn-edit" onclick="openEdit(
                            '<?= $row->id_kendaraan ?>',
                            '<?= $row->plat_nomor ?>',
                            '<?= $row->jenis_kendaraan ?>',
                            '<?= $row->warna ?>',
                            '<?= $row->pemilik ?>',
                            '<?= $row->id_user ?>'
                        )">Edit</a>

                        <form action="../../controllers/c_kendaraan.php?aksi=hapus" method="post" style="display:inline">
                            <input type="hidden" name="id_kendaraan" value="<?= $row->id_kendaraan ?>">
                            <button type="submit" class="btn-delete" onclick="return confirm('Yakin hapus data ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php 
                    endforeach; 
                else : 
                ?>
                <tr>
                    <td colspan="7" style="text-align:center">Belum ada data kendaraan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<div class="modal" id="modalTambah" style="display:none;">
    <div class="modal-content">
        <h3>Tambah Kendaraan</h3>
        <form action="../../controllers/c_kendaraan.php?aksi=tambah" method="post">
            <input type="text" name="plat_nomor" placeholder="Plat Nomor" required>
            <input type="text" name="warna" placeholder="Warna" required>
            <input type="text" name="pemilik" placeholder="Pemilik" required>
            <select name="jenis_kendaraan" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="motor">Motor</option>
                <option value="mobil">Mobil</option>
            </select>
            <input type="hidden" name="id_user" value="<?= $_SESSION['data']['id_user'] ?>">
            <div class="modal-footer">
                <button type="button" onclick="modalTambah.style.display='none'">Batal</button>
                <button type="submit" class="btn-blue">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="modalEdit" style="display:none;">
    <div class="modal-content">
        <h3>Edit Kendaraan</h3>
        <form action="../../controllers/c_kendaraan.php?aksi=update" method="post">
            <input type="hidden" name="id_kendaraan" id="editId">
            <input type="text" name="plat_nomor" id="editPlat" placeholder="Plat Nomor" required>
            <input type="text" name="warna" id="editWarna" placeholder="Warna" required>
            <input type="text" name="pemilik" id="editPemilik" placeholder="Pemilik" required>
            <select name="jenis_kendaraan" id="editJenis" required>
                <option value="motor">Motor</option>
                <option value="mobil">Mobil</option>
            </select>
            <input type="hidden" name="id_user" value="<?= $_SESSION['data']['id_user'] ?>">
            <div class="modal-footer">
                <button type="button" onclick="modalEdit.style.display='none'">Batal</button>
                <button type="submit" class="btn-blue">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
const modalTambah = document.getElementById('modalTambah');
const modalEdit = document.getElementById('modalEdit');

function openTambah(){ 
    modalTambah.style.display='flex'; 
}

function openEdit(id, p, j, w, pm, u){
    document.getElementById('editId').value = id;
    document.getElementById('editPlat').value = p;
    document.getElementById('editJenis').value = j;
    document.getElementById('editWarna').value = w;
    document.getElementById('editPemilik').value = pm;
    modalEdit.style.display = 'flex';
}

// Menutup modal jika user klik di luar kotak modal
window.onclick = function(event) {
    if (event.target == modalTambah) modalTambah.style.display = "none";
    if (event.target == modalEdit) modalEdit.style.display = "none";
}
</script>

<?php include '../templates/footer.php'; ?>