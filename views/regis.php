<!DOCTYPE html>
<html>
<head>
    <title>Registrasi User - Sistem Parkir</title>
</head>
<body>
    <h2>Form Registrasi Pengguna Baru</h2>
    <form action="../controllers/c_login.php?aksi=regis" method="POST">
        <div>
            <label>Nama Lengkap:</label><br>
            <input type="text" name="nama_lengkap" required>
            <input type="text" name="id_user" hidden>
        </div><br>

        <div>
            <label>Username:</label><br>
            <input type="text" name="username" required>
        </div><br>

        <div>
            <label>Password:</label><br>
            <input type="password" name="password" required>
        </div><br>

        <div>
            <label>Role:</label><br>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
                <option value="owner">Owner</option>
            </select>
        </div><br>

        <button type="submit" name="regis">Daftar Sekarang</button>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </form>
</body>
</html>