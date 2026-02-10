<?php

include_once 'm_koneksi.php';

class tarif
{
    public function tampil_data()
    {
        $conn = new koneksi();
        $sql  = "SELECT * FROM tb_tarif ORDER BY 
                CASE jenis_kendaraan 
                    WHEN 'motor' THEN 1
                    WHEN 'mobil' THEN 2
                    ELSE 3
                END,
                tarif_per_jam ASC";
        $query = mysqli_query($conn->koneksi, $sql);

        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $hasil[] = $data;
            }
        }
        return $hasil;
    }

    public function tampil_data_paginated($search = '', $jenis_filter = '', $page = 1, $limit = 10)
    {
        $conn = new koneksi();
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM tb_tarif WHERE 1=1";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($conn->koneksi, $search);
            $sql .= " AND (
                jenis_kendaraan LIKE '%$search%' OR 
                tarif_per_jam LIKE '%$search%'
            )";
        }
        
        if (!empty($jenis_filter)) {
            $jenis_filter = mysqli_real_escape_string($conn->koneksi, $jenis_filter);
            $sql .= " AND jenis_kendaraan = '$jenis_filter'";
        }
        
        $sql .= " ORDER BY 
                CASE jenis_kendaraan 
                    WHEN 'motor' THEN 1
                    WHEN 'mobil' THEN 2
                    ELSE 3
                END,
                tarif_per_jam ASC
                LIMIT $limit OFFSET $offset";
        
        $query = mysqli_query($conn->koneksi, $sql);

        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $hasil[] = $data;
            }
        }
        return $hasil;
    }

    public function hitung_total_data($search = '', $jenis_filter = '')
    {
        $conn = new koneksi();
        
        $sql = "SELECT COUNT(*) as total FROM tb_tarif WHERE 1=1";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($conn->koneksi, $search);
            $sql .= " AND (
                jenis_kendaraan LIKE '%$search%' OR 
                tarif_per_jam LIKE '%$search%'
            )";
        }
        
        if (!empty($jenis_filter)) {
            $jenis_filter = mysqli_real_escape_string($conn->koneksi, $jenis_filter);
            $sql .= " AND jenis_kendaraan = '$jenis_filter'";
        }
        
        $query = mysqli_query($conn->koneksi, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['total'] ?? 0;
    }

    public function hitung_jenis_kendaraan($jenis, $search = '', $jenis_filter = '')
    {
        $conn = new koneksi();
        
        $sql = "SELECT COUNT(*) as total FROM tb_tarif 
                WHERE jenis_kendaraan = '" . mysqli_real_escape_string($conn->koneksi, $jenis) . "'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($conn->koneksi, $search);
            $sql .= " AND (
                jenis_kendaraan LIKE '%$search%' OR 
                tarif_per_jam LIKE '%$search%'
            )";
        }
        
        if (!empty($jenis_filter)) {
            $jenis_filter = mysqli_real_escape_string($conn->koneksi, $jenis_filter);
            $sql .= " AND jenis_kendaraan = '$jenis_filter'";
        }
        
        $query = mysqli_query($conn->koneksi, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['total'] ?? 0;
    }

    public function cek_jenis_kendaraan_sudah_ada()
    {
        $conn = new koneksi();
        $jenis_sudah_ada = [
            'motor' => false,
            'mobil' => false,
            'lainnya' => false
        ];
        
        $sql = "SELECT jenis_kendaraan FROM tb_tarif";
        $query = mysqli_query($conn->koneksi, $sql);
        
        if ($query) {
            while ($data = mysqli_fetch_assoc($query)) {
                $jenis = $data['jenis_kendaraan'];
                if (array_key_exists($jenis, $jenis_sudah_ada)) {
                    $jenis_sudah_ada[$jenis] = true;
                }
            }
        }
        
        return $jenis_sudah_ada;
    }

    public function get_tarif_tertinggi()
    {
        $conn = new koneksi();
        $sql = "SELECT MAX(tarif_per_jam) as max_tarif FROM tb_tarif";
        $query = mysqli_query($conn->koneksi, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['max_tarif'] ?? 0;
    }

    public function get_tarif_terendah()
    {
        $conn = new koneksi();
        $sql = "SELECT MIN(tarif_per_jam) as min_tarif FROM tb_tarif";
        $query = mysqli_query($conn->koneksi, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['min_tarif'] ?? 0;
    }

    public function get_rata_rata_tarif()
    {
        $conn = new koneksi();
        $sql = "SELECT AVG(tarif_per_jam) as avg_tarif FROM tb_tarif";
        $query = mysqli_query($conn->koneksi, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return number_format($result['avg_tarif'] ?? 0, 0, ',', '.');
    }

    public function tampil_data_byid($id_tarif)
    {
        $conn = new koneksi();
        $id_tarif = mysqli_real_escape_string($conn->koneksi, $id_tarif);
        
        $sql  = "SELECT * FROM tb_tarif WHERE id_tarif = '$id_tarif'";
        $query = mysqli_query($conn->koneksi, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            return mysqli_fetch_object($query);
        }

        return null;
    }

    public function tambah_data($jenis_kendaraan, $tarif_per_jam)
    {
        $conn = new koneksi();
        $jenis_kendaraan = mysqli_real_escape_string($conn->koneksi, $jenis_kendaraan);
        $tarif_per_jam = mysqli_real_escape_string($conn->koneksi, $tarif_per_jam);
        
        $cek_sql = "SELECT * FROM tb_tarif WHERE jenis_kendaraan = '$jenis_kendaraan'";
        $cek_query = mysqli_query($conn->koneksi, $cek_sql);
        
        if (mysqli_num_rows($cek_query) > 0) {
            echo "<script>
                alert('Jenis kendaraan \"' + '$jenis_kendaraan' + '\" sudah ada dalam database!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        }
        
        if ($tarif_per_jam <= 0) {
            echo "<script>
                alert('Tarif harus lebih dari 0!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        }
        
        $sql  = "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam)
                VALUES ('$jenis_kendaraan', '$tarif_per_jam')";
        $query = mysqli_query($conn->koneksi, $sql);

        if ($query) {
            echo "<script>
                alert('Tarif berhasil ditambahkan!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        } else {
            echo "<script>
                alert('Gagal menambahkan tarif!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        }
    }

    public function edit_data($id_tarif, $tarif_per_jam)
    {
        $conn = new koneksi();
        $id_tarif = mysqli_real_escape_string($conn->koneksi, $id_tarif);
        $tarif_per_jam = mysqli_real_escape_string($conn->koneksi, $tarif_per_jam);
        
        if ($tarif_per_jam <= 0) {
            echo "<script>
                alert('Tarif harus lebih dari 0!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        }
        
        $sql  = "UPDATE tb_tarif SET
                tarif_per_jam = '$tarif_per_jam'
                WHERE id_tarif = '$id_tarif'";
        $query = mysqli_query($conn->koneksi, $sql);

        if ($query) {
            echo "<script>
                alert('Tarif berhasil diperbarui!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        } else {
            echo "<script>
                alert('Gagal memperbarui tarif!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        }
    }

    public function hapus_data($id_tarif)
    {
        $conn = new koneksi();
        $id_tarif = mysqli_real_escape_string($conn->koneksi, $id_tarif);
        
        $cek_sql = "SELECT * FROM tb_tarif WHERE id_tarif = '$id_tarif'";
        $cek_query = mysqli_query($conn->koneksi, $cek_sql);
        
        if (mysqli_num_rows($cek_query) == 0) {
            echo "<script>
                alert('Tarif tidak ditemukan!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        }
        
        $delete_query = mysqli_query(
            $conn->koneksi,
            "DELETE FROM tb_tarif WHERE id_tarif='$id_tarif'"
        );

        if ($delete_query) {
            echo "<script>
                alert('Tarif berhasil dihapus!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        } else {
            echo "<script>
                alert('Gagal menghapus tarif!');
                window.location='../views/admin/tarif.php';
            </script>";
            exit;
        }
    }
}
?>