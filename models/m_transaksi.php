<?php
include_once 'm_koneksi.php';

class transaksi
{
    private $conn;
    
    public function __construct() {
        $koneksi = new koneksi();
        $this->conn = $koneksi->koneksi;
    }
    
    public function tambah_masuk($id_kendaraan, $id_tarif, $id_user, $id_area)
    {
        $id_kendaraan = mysqli_real_escape_string($this->conn, $id_kendaraan);
        $id_tarif = mysqli_real_escape_string($this->conn, $id_tarif);
        $id_user = mysqli_real_escape_string($this->conn, $id_user);
        $id_area = mysqli_real_escape_string($this->conn, $id_area);
        $waktu_masuk = date('Y-m-d H:i:s');
        $waktu_masuk_unix = time();
        
        $sql = "INSERT INTO tb_transaksi 
                (id_kendaraan, waktu_masuk, waktu_masuk_unix, id_tarif, status, id_user, id_area, waktu_keluar, durasi_jam, durasi_detik, biaya_total)
                VALUES 
                ('$id_kendaraan', '$waktu_masuk', '$waktu_masuk_unix', '$id_tarif', 'masuk', '$id_user', '$id_area', NULL, NULL, NULL, NULL)";
        
        $query = mysqli_query($this->conn, $sql);
        
        if ($query) {
            return mysqli_insert_id($this->conn);
        }
        return false;
    }
    
    public function update_keluar($id_parkir, $waktu_keluar, $durasi_jam, $biaya_total)
    {
        $id_parkir = mysqli_real_escape_string($this->conn, $id_parkir);
        $waktu_keluar = mysqli_real_escape_string($this->conn, $waktu_keluar);
        
        $sql_durasi = "SELECT waktu_masuk_unix FROM tb_transaksi WHERE id_parkir = '$id_parkir'";
        $query_durasi = mysqli_query($this->conn, $sql_durasi);
        $data_durasi = mysqli_fetch_object($query_durasi);
        
        $waktu_keluar_unix = time();
        $durasi_detik = $waktu_keluar_unix - ($data_durasi->waktu_masuk_unix ?? $waktu_keluar_unix);
        $durasi_detik = mysqli_real_escape_string($this->conn, $durasi_detik);
        
        $durasi_jam_decimal = $durasi_detik / 3600;
        $durasi_jam_decimal = mysqli_real_escape_string($this->conn, $durasi_jam_decimal);
        
        $sql_tarif = "SELECT tr.tarif_per_jam 
                      FROM tb_transaksi t
                      JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif
                      WHERE t.id_parkir = '$id_parkir'";
        $query_tarif = mysqli_query($this->conn, $sql_tarif);
        $data_tarif = mysqli_fetch_object($query_tarif);
        
        $biaya_bulat = $this->hitung_biaya_parkir($durasi_detik, $data_tarif->tarif_per_jam);
        $biaya_total = mysqli_real_escape_string($this->conn, $biaya_bulat);
        
        $sql = "UPDATE tb_transaksi 
                SET waktu_keluar = '$waktu_keluar', 
                    durasi_jam = '$durasi_jam_decimal',
                    durasi_detik = '$durasi_detik',
                    biaya_total = '$biaya_total',
                    status = 'keluar'
                WHERE id_parkir = '$id_parkir' 
                AND status = 'masuk'";
        
        return mysqli_query($this->conn, $sql);
    }
    
    public function cek_parkir_aktif($id_kendaraan)
    {
        $id_kendaraan = mysqli_real_escape_string($this->conn, $id_kendaraan);
        
        $sql = "SELECT * FROM tb_transaksi 
                WHERE id_kendaraan = '$id_kendaraan' 
                AND status = 'masuk'
                LIMIT 1";
        
        $query = mysqli_query($this->conn, $sql);
        
        if ($query && mysqli_num_rows($query) > 0) {
            return mysqli_fetch_object($query);
        }
        return null;
    }
    
    public function cari_transaksi_aktif_by_plat($plat_nomor)
    {
        $plat_nomor = mysqli_real_escape_string($this->conn, $plat_nomor);
        
        $sql = "SELECT t.* FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                WHERE k.plat_nomor = '$plat_nomor' 
                AND t.status = 'masuk'
                ORDER BY t.waktu_masuk DESC
                LIMIT 1";
        
        $query = mysqli_query($this->conn, $sql);
        
        if ($query && mysqli_num_rows($query) > 0) {
            return mysqli_fetch_object($query);
        }
        return null;
    }
    
    public function tampil_transaksi_aktif_paginated($search = '', $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                       a.nama_area, tr.tarif_per_jam
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif
                WHERE t.status = 'masuk'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' 
                        OR k.pemilik LIKE '%$search%'
                        OR a.nama_area LIKE '%$search%')";
        }
        
        $sql .= " ORDER BY t.waktu_masuk DESC
                  LIMIT $limit OFFSET $offset";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                $waktu_masuk = $data->waktu_masuk_unix ?? strtotime($data->waktu_masuk);
                $sekarang = time();
                $selisih = $sekarang - $waktu_masuk;

                $jam = floor($selisih / 3600);
                $menit = floor(($selisih % 3600) / 60);
                $detik = $selisih % 60;
                
                $data->durasi_format = sprintf("%02d:%02d:%02d", $jam, $menit, $detik);

                $data->durasi_text = '';
                if ($jam > 0) $data->durasi_text .= $jam . ' jam ';
                if ($menit > 0) $data->durasi_text .= $menit . ' menit ';
                if ($detik > 0) $data->durasi_text .= $detik . ' detik';
                if ($jam == 0 && $menit == 0 && $detik == 0) {
                    $data->durasi_text = '0 detik';
                }

                $data->estimasi_biaya = $this->hitung_biaya_parkir($selisih, $data->tarif_per_jam);
                
                $hasil[] = $data;
            }
        }
        return $hasil;
    }
    
    public function tampil_riwayat_paginated($search = '', $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                       a.nama_area, tr.tarif_per_jam, u.nama_lengkap as petugas
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif
                LEFT JOIN tb_user u ON t.id_user = u.id_user
                WHERE t.status = 'keluar'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' 
                        OR k.pemilik LIKE '%$search%'
                        OR a.nama_area LIKE '%$search%')";
        }
        
        $sql .= " ORDER BY t.waktu_keluar DESC
                  LIMIT $limit OFFSET $offset";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
                // Format durasi dari detik ke jam:menit:detik
                if ($data->durasi_detik) {
                    $jam = floor($data->durasi_detik / 3600);
                    $menit = floor(($data->durasi_detik % 3600) / 60);
                    $detik = $data->durasi_detik % 60;
                    $data->durasi_format = sprintf("%02d:%02d:%02d", $jam, $menit, $detik);
                } else {
                    $data->durasi_format = $data->durasi_jam . ':00:00';
                }
                $hasil[] = $data;
            }
        }
        return $hasil;
    }
    
    public function hitung_total_aktif($search = '')
    {
        $sql = "SELECT COUNT(*) as total
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                WHERE t.status = 'masuk'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' OR k.pemilik LIKE '%$search%')";
        }
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    public function hitung_total_riwayat($search = '')
    {
        $sql = "SELECT COUNT(*) as total
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                WHERE t.status = 'keluar'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' OR k.pemilik LIKE '%$search%')";
        }
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    public function hitung_transaksi_hari_ini()
    {
        $tanggal = date('Y-m-d');
        $sql = "SELECT COUNT(*) as total 
                FROM tb_transaksi 
                WHERE DATE(waktu_masuk) = '$tanggal'";
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    public function hitung_pendapatan_hari_ini()
    {
        $tanggal = date('Y-m-d');
        $sql = "SELECT SUM(biaya_total) as total 
                FROM tb_transaksi 
                WHERE DATE(waktu_keluar) = '$tanggal'
                AND status = 'keluar'";
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    public function hitung_kendaraan_aktif()
    {
        $sql = "SELECT COUNT(*) as total 
                FROM tb_transaksi 
                WHERE status = 'masuk'";
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    public function tampil_data_byid($id_parkir)
    {
        $id_parkir = mysqli_real_escape_string($this->conn, $id_parkir);
        
        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                       a.nama_area, tr.tarif_per_jam, u.nama_lengkap as petugas
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                JOIN tb_tarif tr ON t.id_tarif = tr.id_tarif
                LEFT JOIN tb_user u ON t.id_user = u.id_user
                WHERE t.id_parkir = '$id_parkir'";
        
        $query = mysqli_query($this->conn, $sql);
        
        if ($query && mysqli_num_rows($query) > 0) {
            return mysqli_fetch_object($query);
        }
        return null;
    }
    
    public function laporan_pendapatan($tanggal_mulai, $tanggal_selesai)
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT DATE(t.waktu_keluar) as tanggal,
                       COUNT(*) as jumlah_transaksi,
                       SUM(t.biaya_total) as total_pendapatan,
                       AVG(t.biaya_total) as rata_rata,
                       MAX(t.biaya_total) as tertinggi,
                       MIN(t.biaya_total) as terendah
                FROM tb_transaksi t
                WHERE t.status = 'keluar'
                AND DATE(t.waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                GROUP BY DATE(t.waktu_keluar)
                ORDER BY tanggal DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        return $hasil;
    }
    
    public function hitung_biaya_parkir($durasi_detik, $tarif_per_jam)
    {
        if ($durasi_detik <= 0) return 0;
        $tarif_per_menit = $tarif_per_jam / 60;
        $menit = ceil($durasi_detik / 60);
        $biaya_real = $menit * $tarif_per_menit;
        return $this->bulatkan_rupiah($biaya_real);
    }
    
    private function bulatkan_rupiah($nilai)
    {
        if ($nilai < 250) return 0;
        return floor(($nilai + 250) / 500) * 500;
    }
}
?>