<?php
include_once 'm_koneksi.php';

class rekap
{
    private $conn;
    
    public function __construct() {
        $koneksi = new koneksi();
        $this->conn = $koneksi->koneksi;
    }

    public function getConnection() {
        return $this->conn;
    }
    
    // Rekap Harian
    public function rekap_harian($tanggal)
    {
        $tanggal = mysqli_real_escape_string($this->conn, $tanggal);
        
        $sql = "SELECT 
                    COUNT(*) as jumlah_transaksi,
                    SUM(biaya_total) as total_pendapatan,
                    AVG(biaya_total) as rata_rata,
                    MIN(biaya_total) as minimal,
                    MAX(biaya_total) as maksimal,
                    COUNT(DISTINCT id_kendaraan) as kendaraan_unik
                FROM tb_transaksi 
                WHERE DATE(waktu_keluar) = '$tanggal'
                AND status = 'keluar'";
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_object($query);
        
        // Per transaksi per jam
        $sql_jam = "SELECT 
                        HOUR(waktu_keluar) as jam,
                        COUNT(*) as jumlah,
                        SUM(biaya_total) as pendapatan
                    FROM tb_transaksi 
                    WHERE DATE(waktu_keluar) = '$tanggal'
                    AND status = 'keluar'
                    GROUP BY HOUR(waktu_keluar)
                    ORDER BY jam ASC";
        
        $query_jam = mysqli_query($this->conn, $sql_jam);
        $per_jam = [];
        while ($data = mysqli_fetch_object($query_jam)) {
            $per_jam[] = $data;
        }
        
        $result->per_jam = $per_jam;
        
        return $result;
    }
    
    // Rekap untuk periode tertentu (RENTANG TANGGAL)
    public function rekap_periode($tanggal_mulai, $tanggal_selesai)
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT 
                    COUNT(*) as jumlah_transaksi,
                    SUM(biaya_total) as total_pendapatan,
                    AVG(biaya_total) as rata_rata,
                    MIN(biaya_total) as minimal,
                    MAX(biaya_total) as maksimal,
                    COUNT(DISTINCT id_kendaraan) as kendaraan_unik
                FROM tb_transaksi 
                WHERE DATE(waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                AND status = 'keluar'";
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_object($query);
        
        // Per hari dalam rentang
        $sql_harian = "SELECT 
                            DATE(waktu_keluar) as tanggal,
                            COUNT(*) as jumlah,
                            SUM(biaya_total) as pendapatan
                        FROM tb_transaksi 
                        WHERE DATE(waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                        AND status = 'keluar'
                        GROUP BY DATE(waktu_keluar)
                        ORDER BY tanggal ASC";
        
        $query_harian = mysqli_query($this->conn, $sql_harian);
        $per_hari = [];
        while ($data = mysqli_fetch_object($query_harian)) {
            $per_hari[] = $data;
        }
        
        $result->per_hari = $per_hari;
        
        return $result;
    }
    
    // Rekap jenis kendaraan untuk periode tertentu (RENTANG TANGGAL)
    public function rekap_jenis_kendaraan_periode($tanggal_mulai, $tanggal_selesai)
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT 
                    k.jenis_kendaraan,
                    COUNT(*) as jumlah,
                    SUM(t.biaya_total) as total_pendapatan
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                WHERE DATE(t.waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                AND t.status = 'keluar'
                GROUP BY k.jenis_kendaraan
                ORDER BY jumlah DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        
        return $hasil;
    }
    
    // Rekap area untuk periode tertentu (RENTANG TANGGAL)
    public function rekap_area_periode($tanggal_mulai, $tanggal_selesai)
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT 
                    a.nama_area,
                    COUNT(*) as jumlah,
                    SUM(t.biaya_total) as total_pendapatan
                FROM tb_transaksi t
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                WHERE DATE(t.waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                AND t.status = 'keluar'
                GROUP BY a.id_area
                ORDER BY jumlah DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        
        return $hasil;
    }
    
    // Rekap Bulanan
    public function rekap_bulanan($bulan)
    {
        $bulan = mysqli_real_escape_string($this->conn, $bulan);
        $tahun_bulan = date('Y-m', strtotime($bulan . '-01'));
        
        $sql = "SELECT 
                    DATE(waktu_keluar) as tanggal,
                    COUNT(*) as jumlah_transaksi,
                    SUM(biaya_total) as total_pendapatan
                FROM tb_transaksi 
                WHERE DATE_FORMAT(waktu_keluar, '%Y-%m') = '$tahun_bulan'
                AND status = 'keluar'
                GROUP BY DATE(waktu_keluar)
                ORDER BY tanggal ASC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        
        return $hasil;
    }
    
    // Rekap Tahunan
    public function rekap_tahunan($tahun)
    {
        $tahun = mysqli_real_escape_string($this->conn, $tahun);
        
        $sql = "SELECT 
                    MONTH(waktu_keluar) as bulan,
                    COUNT(*) as jumlah_transaksi,
                    SUM(biaya_total) as total_pendapatan
                FROM tb_transaksi 
                WHERE YEAR(waktu_keluar) = '$tahun'
                AND status = 'keluar'
                GROUP BY MONTH(waktu_keluar)
                ORDER BY bulan ASC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        
        return $hasil;
    }
    
    // Rekap berdasarkan jenis kendaraan (Bulanan)
    public function rekap_jenis_kendaraan_bulanan($bulan)
    {
        $bulan = mysqli_real_escape_string($this->conn, $bulan);
        $tahun_bulan = date('Y-m', strtotime($bulan . '-01'));
        
        $sql = "SELECT 
                    k.jenis_kendaraan,
                    COUNT(*) as jumlah,
                    SUM(t.biaya_total) as total_pendapatan
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                WHERE DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$tahun_bulan'
                AND t.status = 'keluar'
                GROUP BY k.jenis_kendaraan
                ORDER BY jumlah DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        
        return $hasil;
    }
    
    // Rekap berdasarkan jenis kendaraan (Tahunan)
    public function rekap_jenis_kendaraan_tahunan($tahun)
    {
        $tahun = mysqli_real_escape_string($this->conn, $tahun);
        
        $sql = "SELECT 
                    k.jenis_kendaraan,
                    COUNT(*) as jumlah,
                    SUM(t.biaya_total) as total_pendapatan
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                WHERE YEAR(t.waktu_keluar) = '$tahun'
                AND t.status = 'keluar'
                GROUP BY k.jenis_kendaraan
                ORDER BY jumlah DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        
        return $hasil;
    }
    
    // Rekap berdasarkan area (Bulanan)
    public function rekap_area_bulanan($bulan)
    {
        $bulan = mysqli_real_escape_string($this->conn, $bulan);
        $tahun_bulan = date('Y-m', strtotime($bulan . '-01'));
        
        $sql = "SELECT 
                    a.nama_area,
                    COUNT(*) as jumlah,
                    SUM(t.biaya_total) as total_pendapatan
                FROM tb_transaksi t
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                WHERE DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$tahun_bulan'
                AND t.status = 'keluar'
                GROUP BY a.id_area
                ORDER BY jumlah DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        
        return $hasil;
    }
    
    // Rekap berdasarkan area (Tahunan)
    public function rekap_area_tahunan($tahun)
    {
        $tahun = mysqli_real_escape_string($this->conn, $tahun);
        
        $sql = "SELECT 
                    a.nama_area,
                    COUNT(*) as jumlah,
                    SUM(t.biaya_total) as total_pendapatan
                FROM tb_transaksi t
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                WHERE YEAR(t.waktu_keluar) = '$tahun'
                AND t.status = 'keluar'
                GROUP BY a.id_area
                ORDER BY jumlah DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        
        return $hasil;
    }
    
    // Rekap jam sibuk
    public function rekap_jam_sibuk($tanggal_mulai, $tanggal_selesai)
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT 
                    HOUR(waktu_keluar) as jam,
                    COUNT(*) as jumlah_transaksi
                FROM tb_transaksi 
                WHERE DATE(waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                AND status = 'keluar'
                GROUP BY HOUR(waktu_keluar)
                ORDER BY jumlah_transaksi DESC
                LIMIT 5";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        while ($data = mysqli_fetch_object($query)) {
            $hasil[] = $data;
        }
        
        return $hasil;
    }
    
    // Total pendapatan periode
    public function total_pendapatan_periode($tanggal_mulai, $tanggal_selesai)
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT SUM(biaya_total) as total 
                FROM tb_transaksi 
                WHERE DATE(waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                AND status = 'keluar'";
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    // Total transaksi periode
    public function total_transaksi_periode($tanggal_mulai, $tanggal_selesai)
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT COUNT(*) as total 
                FROM tb_transaksi 
                WHERE DATE(waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                AND status = 'keluar'";
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    // Total kendaraan unik periode
    public function total_kendaraan_unik_periode($tanggal_mulai, $tanggal_selesai)
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT COUNT(DISTINCT id_kendaraan) as total 
                FROM tb_transaksi 
                WHERE DATE(waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                AND status = 'keluar'";
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    // Laporan detail per periode (dengan pagination)
    public function laporan_detail_periode_paginated($tanggal_mulai, $tanggal_selesai, $search = '', $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT t.*, 
                       k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                       a.nama_area,
                       u.nama_lengkap as petugas,
                       DATE(t.waktu_keluar) as tanggal
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                LEFT JOIN tb_user u ON t.id_user = u.id_user
                WHERE t.status = 'keluar'
                AND DATE(t.waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' 
                        OR k.pemilik LIKE '%$search%'
                        OR k.jenis_kendaraan LIKE '%$search%')";
        }
        
        $sql .= " ORDER BY t.waktu_keluar DESC
                  LIMIT $limit OFFSET $offset";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
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
    
    public function hitung_total_detail_periode($tanggal_mulai, $tanggal_selesai, $search = '')
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT COUNT(*) as total
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                WHERE t.status = 'keluar'
                AND DATE(t.waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' OR k.pemilik LIKE '%$search%')";
        }
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    // Laporan detail bulanan (dengan pagination)
    public function laporan_detail_bulanan_paginated($bulan, $search = '', $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $bulan = mysqli_real_escape_string($this->conn, $bulan);
        $tahun_bulan = date('Y-m', strtotime($bulan . '-01'));
        
        $sql = "SELECT t.*, 
                       k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                       a.nama_area,
                       u.nama_lengkap as petugas,
                       DATE(t.waktu_keluar) as tanggal
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                LEFT JOIN tb_user u ON t.id_user = u.id_user
                WHERE t.status = 'keluar'
                AND DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$tahun_bulan'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' 
                        OR k.pemilik LIKE '%$search%'
                        OR k.jenis_kendaraan LIKE '%$search%')";
        }
        
        $sql .= " ORDER BY t.waktu_keluar DESC
                  LIMIT $limit OFFSET $offset";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
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
    
    public function hitung_total_detail_bulanan($bulan, $search = '')
    {
        $bulan = mysqli_real_escape_string($this->conn, $bulan);
        $tahun_bulan = date('Y-m', strtotime($bulan . '-01'));
        
        $sql = "SELECT COUNT(*) as total
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                WHERE t.status = 'keluar'
                AND DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$tahun_bulan'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' OR k.pemilik LIKE '%$search%')";
        }
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    // Laporan detail tahunan (dengan pagination)
    public function laporan_detail_tahunan_paginated($tahun, $search = '', $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $tahun = mysqli_real_escape_string($this->conn, $tahun);
        
        $sql = "SELECT t.*, 
                       k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                       a.nama_area,
                       u.nama_lengkap as petugas,
                       DATE(t.waktu_keluar) as tanggal
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                LEFT JOIN tb_user u ON t.id_user = u.id_user
                WHERE t.status = 'keluar'
                AND YEAR(t.waktu_keluar) = '$tahun'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' 
                        OR k.pemilik LIKE '%$search%'
                        OR k.jenis_kendaraan LIKE '%$search%')";
        }
        
        $sql .= " ORDER BY t.waktu_keluar DESC
                  LIMIT $limit OFFSET $offset";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
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
    
    public function hitung_total_detail_tahunan($tahun, $search = '')
    {
        $tahun = mysqli_real_escape_string($this->conn, $tahun);
        
        $sql = "SELECT COUNT(*) as total
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                WHERE t.status = 'keluar'
                AND YEAR(t.waktu_keluar) = '$tahun'";
        
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $sql .= " AND (k.plat_nomor LIKE '%$search%' OR k.pemilik LIKE '%$search%')";
        }
        
        $query = mysqli_query($this->conn, $sql);
        $result = mysqli_fetch_assoc($query);
        return $result['total'] ?? 0;
    }
    
    // Export full data
    public function laporan_detail_periode($tanggal_mulai, $tanggal_selesai)
    {
        $tanggal_mulai = mysqli_real_escape_string($this->conn, $tanggal_mulai);
        $tanggal_selesai = mysqli_real_escape_string($this->conn, $tanggal_selesai);
        
        $sql = "SELECT t.*, 
                       k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                       a.nama_area,
                       u.nama_lengkap as petugas,
                       DATE(t.waktu_keluar) as tanggal
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                LEFT JOIN tb_user u ON t.id_user = u.id_user
                WHERE t.status = 'keluar'
                AND DATE(t.waktu_keluar) BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                ORDER BY t.waktu_keluar DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
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
    
    public function laporan_detail_bulanan($bulan)
    {
        $bulan = mysqli_real_escape_string($this->conn, $bulan);
        $tahun_bulan = date('Y-m', strtotime($bulan . '-01'));
        
        $sql = "SELECT t.*, 
                       k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                       a.nama_area,
                       u.nama_lengkap as petugas,
                       DATE(t.waktu_keluar) as tanggal
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                LEFT JOIN tb_user u ON t.id_user = u.id_user
                WHERE t.status = 'keluar'
                AND DATE_FORMAT(t.waktu_keluar, '%Y-%m') = '$tahun_bulan'
                ORDER BY t.waktu_keluar DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
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
    
    public function laporan_detail_tahunan($tahun)
    {
        $tahun = mysqli_real_escape_string($this->conn, $tahun);
        
        $sql = "SELECT t.*, 
                       k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                       a.nama_area,
                       u.nama_lengkap as petugas,
                       DATE(t.waktu_keluar) as tanggal
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                JOIN tb_area_parkir a ON t.id_area = a.id_area
                LEFT JOIN tb_user u ON t.id_user = u.id_user
                WHERE t.status = 'keluar'
                AND YEAR(t.waktu_keluar) = '$tahun'
                ORDER BY t.waktu_keluar DESC";
        
        $query = mysqli_query($this->conn, $sql);
        
        $hasil = [];
        if ($query) {
            while ($data = mysqli_fetch_object($query)) {
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
}
?>