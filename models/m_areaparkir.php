<?php

include_once 'm_koneksi.php';

class area
{
    private $conn;

    public function __construct() {
        $koneksi = new koneksi();
        $this->conn = $koneksi->koneksi;
    }

    public function tampil_data()
    {
        $sql  = "SELECT * FROM tb_area_parkir ORDER BY id_area DESC";
        $query = mysqli_query($this->conn, $sql);

        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $data->status = ($data->terisi >= $data->kapasitas) ? 'Penuh' : 'Tersedia';
                $data->persentase = $data->kapasitas > 0 ? round(($data->terisi / $data->kapasitas) * 100, 1) : 0;
                $data->tersisa = $data->kapasitas - $data->terisi;
                $hasil[] = $data;
            }
        }
        return $hasil;
    }

    public function tampil_data_paginated($search = '', $status_filter = '', $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM tb_area_parkir WHERE 1=1";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (nama_area LIKE '%$search%')";
        }

        if (!empty($status_filter)) {
            if ($status_filter == 'tersedia') {
                $sql .= " AND terisi < kapasitas";
            } elseif ($status_filter == 'penuh') {
                $sql .= " AND terisi >= kapasitas";
            }
        }
        
        $sql .= " ORDER BY id_area DESC LIMIT $limit OFFSET $offset";
        
        $query = mysqli_query($this->conn, $sql);

        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $data->status = ($data->terisi >= $data->kapasitas) ? 'Penuh' : 'Tersedia';
                $data->persentase = $data->kapasitas > 0 ? round(($data->terisi / $data->kapasitas) * 100, 1) : 0;
                $data->tersisa = $data->kapasitas - $data->terisi;
                $hasil[] = $data;
            }
        }
        return $hasil;
    }

    public function hitung_total_data($search = '', $status_filter = '')
    {
        $sql = "SELECT COUNT(*) as total FROM tb_area_parkir WHERE 1=1";

        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (nama_area LIKE '%$search%')";
        }

        if (!empty($status_filter)) {
            if ($status_filter == 'tersedia') {
                $sql .= " AND terisi < kapasitas";
            } elseif ($status_filter == 'penuh') {
                $sql .= " AND terisi >= kapasitas";
            }
        }
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['total'] ?? 0;
    }

    public function tampil_data_ketersediaan()
    {
        $sql = "SELECT *, 
                (kapasitas - terisi) as sisa,
                CASE 
                    WHEN terisi >= kapasitas THEN 'Penuh'
                    ELSE 'Tersedia'
                END as status_area
                FROM tb_area_parkir 
                ORDER BY nama_area ASC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $data->persentase = $data->kapasitas > 0 ? round(($data->terisi / $data->kapasitas) * 100, 1) : 0;
                $hasil[] = $data;
            }
        }
        return $hasil;
    }

    public function tampil_data_tersedia()
    {
        $sql = "SELECT *, 
                (kapasitas - terisi) as sisa 
                FROM tb_area_parkir 
                WHERE terisi < kapasitas 
                ORDER BY nama_area ASC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $data->tersisa = $data->kapasitas - $data->terisi;
                $hasil[] = $data;
            }
        }
        return $hasil;
    }

    public function update_terisi($id_area, $aksi)
    {
        $id_area = mysqli_real_escape_string($this->conn, $id_area);
        
        if ($aksi == 'tambah') {
            $sql = "UPDATE tb_area_parkir SET terisi = terisi + 1 WHERE id_area = '$id_area'";
        } elseif ($aksi == 'kurang') {
            $sql = "UPDATE tb_area_parkir SET terisi = terisi - 1 WHERE id_area = '$id_area' AND terisi > 0";
        } else {
            return false;
        }
        
        return mysqli_query($this->conn, $sql);
    }

    public function hitung_total_kapasitas()
    {
        $sql = "SELECT SUM(kapasitas) as total FROM tb_area_parkir";
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['total'] ?? 0;
    }

    public function hitung_total_terisi()
    {
        $sql = "SELECT SUM(terisi) as total FROM tb_area_parkir";
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['total'] ?? 0;
    }

    public function hitung_area_tersedia()
    {
        $sql = "SELECT COUNT(*) as total FROM tb_area_parkir WHERE terisi < kapasitas";
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['total'] ?? 0;
    }

    public function hitung_area_penuh()
    {
        $sql = "SELECT COUNT(*) as total FROM tb_area_parkir WHERE terisi >= kapasitas";
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['total'] ?? 0;
    }

    public function tambah_data($nama_area, $kapasitas, $terisi)
    {
        $nama_area = mysqli_real_escape_string($this->conn, $nama_area);
        $kapasitas = mysqli_real_escape_string($this->conn, $kapasitas);
        $terisi = mysqli_real_escape_string($this->conn, $terisi);
        
        $cek_sql = "SELECT * FROM tb_area_parkir WHERE nama_area = '$nama_area'";
        $cek_query = mysqli_query($this->conn, $cek_sql);
        
        if (mysqli_num_rows($cek_query) > 0) {
            echo "<script>
                    alert('Nama area parkir sudah ada dalam database!');
                    window.location='../views/admin/area_parkir.php';
                  </script>";
            exit;
        }
        
        $sql  = "INSERT INTO tb_area_parkir (nama_area, kapasitas, terisi)
                 VALUES ('$nama_area', '$kapasitas', '$terisi')";
        $query = mysqli_query($this->conn, $sql);

        if ($query) {
            header("Location: ../views/admin/area_parkir.php");
            exit;
        } else {
            echo "<script>alert('Data gagal ditambahkan');history.back()</script>";
        }
    }

    public function tampil_data_byid($id_area)
    {
        $id_area = mysqli_real_escape_string($this->conn, $id_area);
        $sql  = "SELECT * FROM tb_area_parkir WHERE id_area = '$id_area'";
        $query = mysqli_query($this->conn, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_object($query);
            $data->status = ($data->terisi >= $data->kapasitas) ? 'Penuh' : 'Tersedia';
            $data->persentase = $data->kapasitas > 0 ? round(($data->terisi / $data->kapasitas) * 100, 1) : 0;
            $data->tersisa = $data->kapasitas - $data->terisi;
            return $data;
        }

        return null;
    }

    public function edit_data($id_area, $nama_area, $kapasitas, $terisi)
    {
        $id_area = mysqli_real_escape_string($this->conn, $id_area);
        $nama_area = mysqli_real_escape_string($this->conn, $nama_area);
        $kapasitas = mysqli_real_escape_string($this->conn, $kapasitas);
        $terisi = mysqli_real_escape_string($this->conn, $terisi);

        $cek_sql = "SELECT * FROM tb_area_parkir 
                    WHERE nama_area = '$nama_area' 
                    AND id_area != '$id_area'";
        $cek_query = mysqli_query($this->conn, $cek_sql);
        
        if (mysqli_num_rows($cek_query) > 0) {
            echo "<script>
                    alert('Nama area parkir sudah ada dalam database!');
                    window.location='../views/admin/area_parkir.php';
                  </script>";
            exit;
        }
        
        $sql  = "UPDATE tb_area_parkir SET
                  nama_area = '$nama_area',
                  kapasitas = '$kapasitas',
                  terisi    = '$terisi'
                 WHERE id_area = '$id_area'";
        $query = mysqli_query($this->conn, $sql);

        if ($query) {
            header("Location: ../views/admin/area_parkir.php");
            exit;
        } else {
            echo "<script>alert('Data gagal diubah');history.back()</script>";
        }
    }

    public function hapus_data($id_area)
    {
        $id_area = mysqli_real_escape_string($this->conn, $id_area);
        
        mysqli_query(
            $this->conn,
            "DELETE FROM tb_area_parkir WHERE id_area='$id_area'"
        );

        header("Location: ../views/admin/area_parkir.php");
        exit;
    }
}
?>