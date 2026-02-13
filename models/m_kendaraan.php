<?php

include_once 'm_koneksi.php';

class kendaraan
{
    private $conn;

    public function __construct() {
        $koneksi = new koneksi();
        $this->conn = $koneksi->koneksi;
    }

    public function cari_plat($plat_nomor)
    {
        $plat_nomor = mysqli_real_escape_string($this->conn, strtoupper($plat_nomor));
        
        $sql = "SELECT * FROM tb_kendaraan 
                WHERE plat_nomor = '$plat_nomor' 
                LIMIT 1";
        
        $query = mysqli_query($this->conn, $sql);
        
        if ($query && mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_object($query);
            $data->plat_nomor = strtoupper($data->plat_nomor);
            return $data;
        }
        
        return null;
    }

    public function tampil_data()
    {
        $sql  = "SELECT k.*, u.nama_lengkap
                 FROM tb_kendaraan k
                 JOIN tb_user u ON k.id_user = u.id_user
                 ORDER BY k.id_kendaraan DESC";
        $query = mysqli_query($this->conn, $sql);

        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $data->plat_nomor = strtoupper($data->plat_nomor);
                $hasil[] = $data;
            }
        }
        return $hasil;
    }

    public function tampil_data_by_id($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        
        $sql = "SELECT k.*, u.nama_lengkap
                FROM tb_kendaraan k
                JOIN tb_user u ON k.id_user = u.id_user
                WHERE k.id_kendaraan = '$id'";
        
        $query = mysqli_query($this->conn, $sql);
        
        if ($query && mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
            $data['plat_nomor'] = strtoupper($data['plat_nomor']);
            return $data;
        }
        
        return null;
    }

    public function tampil_data_paginated($search = '', $jenis_filter = '', $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT k.*, u.nama_lengkap
                FROM tb_kendaraan k
                JOIN tb_user u ON k.id_user = u.id_user
                WHERE 1=1";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, strtoupper($search));
            $sql .= " AND (
                        UPPER(k.plat_nomor) LIKE '%$search%' OR 
                        k.warna LIKE '%$search%' OR 
                        k.pemilik LIKE '%$search%' OR
                        u.nama_lengkap LIKE '%$search%'
                      )";
        }
        
        if (!empty($jenis_filter)) {
            $jenis_filter = mysqli_real_escape_string($this->conn, $jenis_filter);
            $sql .= " AND k.jenis_kendaraan = '$jenis_filter'";
        }
        
        $sql .= " ORDER BY k.id_kendaraan DESC
                  LIMIT $limit OFFSET $offset";
        
        $query = mysqli_query($this->conn, $sql);

        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $data->plat_nomor = strtoupper($data->plat_nomor);
                $hasil[] = $data;
            }
        }
        return $hasil;
    }

    public function hitung_total_data($search = '', $jenis_filter = '')
    {
        $sql = "SELECT COUNT(*) as total
                FROM tb_kendaraan k
                JOIN tb_user u ON k.id_user = u.id_user
                WHERE 1=1";

        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, strtoupper($search));
            $sql .= " AND (
                        UPPER(k.plat_nomor) LIKE '%$search%' OR 
                        k.warna LIKE '%$search%' OR 
                        k.pemilik LIKE '%$search%' OR
                        u.nama_lengkap LIKE '%$search%'
                      )";
        }
        
        if (!empty($jenis_filter)) {
            $jenis_filter = mysqli_real_escape_string($this->conn, $jenis_filter);
            $sql .= " AND k.jenis_kendaraan = '$jenis_filter'";
        }
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['total'] ?? 0;
    }

    public function hitung_jenis_kendaraan($jenis, $search = '', $jenis_filter = '')
    {
        $sql = "SELECT COUNT(*) as total
                FROM tb_kendaraan k
                JOIN tb_user u ON k.id_user = u.id_user
                WHERE k.jenis_kendaraan = '" . mysqli_real_escape_string($this->conn, $jenis) . "'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, strtoupper($search));
            $sql .= " AND (
                        UPPER(k.plat_nomor) LIKE '%$search%' OR 
                        k.warna LIKE '%$search%' OR 
                        k.pemilik LIKE '%$search%' OR
                        u.nama_lengkap LIKE '%$search%'
                      )";
        }
        
        if (!empty($jenis_filter)) {
            $jenis_filter = mysqli_real_escape_string($this->conn, $jenis_filter);
            $sql .= " AND k.jenis_kendaraan = '$jenis_filter'";
        }
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        
        return $result['total'] ?? 0;
    }

    public function tambah_data($plat, $jenis, $warna, $pemilik, $id_user)
    {
        $plat = mysqli_real_escape_string($this->conn, strtoupper($plat));
        $jenis = mysqli_real_escape_string($this->conn, $jenis);
        $warna = mysqli_real_escape_string($this->conn, $warna);
        $pemilik = mysqli_real_escape_string($this->conn, $pemilik);
        $id_user = mysqli_real_escape_string($this->conn, $id_user);
        
        $sql  = "INSERT INTO tb_kendaraan 
                (plat_nomor, jenis_kendaraan, warna, pemilik, id_user)
                VALUES 
                ('$plat','$jenis','$warna','$pemilik','$id_user')";
        $query = mysqli_query($this->conn, $sql);

        if ($query) {
            return mysqli_insert_id($this->conn);
        } else {
            return false;
        }
    }

    public function edit_data($id, $plat, $jenis, $warna, $pemilik, $id_user)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        $plat = mysqli_real_escape_string($this->conn, strtoupper($plat));
        $jenis = mysqli_real_escape_string($this->conn, $jenis);
        $warna = mysqli_real_escape_string($this->conn, $warna);
        $pemilik = mysqli_real_escape_string($this->conn, $pemilik);
        $id_user = mysqli_real_escape_string($this->conn, $id_user);
        
        $sql  = "UPDATE tb_kendaraan SET
                  plat_nomor='$plat',
                  jenis_kendaraan='$jenis',
                  warna='$warna',
                  pemilik='$pemilik',
                  id_user='$id_user'
                 WHERE id_kendaraan='$id'";
        $query = mysqli_query($this->conn, $sql);

        if ($query) {
            header("Location: ../views/admin/kendaraan.php");
            exit;
        } else {
            echo "<script>alert('Data gagal diubah');history.back()</script>";
        }
    }

    public function hapus_data($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        
        mysqli_query(
            $this->conn,
            "DELETE FROM tb_kendaraan WHERE id_kendaraan='$id'"
        );

        header("Location: ../views/admin/kendaraan.php");
        exit;
    }
}
?>