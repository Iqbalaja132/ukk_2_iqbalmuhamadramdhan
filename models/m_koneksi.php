<?php

class koneksi {
    private $server = "localhost";
    private $username = "root";
    private $pass = "";
    private $db = "ukk_2_iqbalmuhamadramdhan";

    public $koneksi;
    function __construct()
    {
        $this->koneksi = mysqli_connect(
            $this->server,
            $this->username,
            $this->pass,
            $this->db
        );

        mysqli_select_db($this->koneksi, $this->db);

        if ($this->koneksi) {
            return $this->koneksi;
        } else {
            echo "Koneksi ke Db Gagal";
        }
        
    }
}

$koneksi = new koneksi();

?>