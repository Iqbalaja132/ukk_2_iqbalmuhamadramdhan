<?php

include_once __DIR__ . '/../models/m_logaktivitas.php';

$log_obj = new logaktivitas();

$data_log = [];
$total_data = 0;
$total_halaman = 0;
$jumlah_hari_ini = 0;
$current_page = 1;
$current_search = '';
$current_date_filter = '';
$current_user_filter = '';
$limit = 15;

try {
    $current_search = $_GET['search'] ?? '';
    $current_date_filter = $_GET['date_filter'] ?? '';
    $current_user_filter = $_GET['user_filter'] ?? '';
    $current_page = $_GET['page'] ?? 1;

    $current_page = max(1, intval($current_page));

    $data_log = $log_obj->tampil_data_paginated(
        $current_search, 
        $current_date_filter, 
        $current_user_filter, 
        $current_page, 
        $limit
    );

    $total_data = $log_obj->hitung_total_data(
        $current_search, 
        $current_date_filter, 
        $current_user_filter
    );

    $total_halaman = ceil($total_data / $limit);

    $jumlah_hari_ini = $log_obj->hitung_hari_ini();

    $daftar_user = $log_obj->get_daftar_user();

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}