<?php

// konfigurasi koneksi
$host       =  "10.2.255.22";
$dbuser     =  "fmss";
$dbpass     =  "S3su4tu_R4h4s14";
$port       =  "5432";
 $dbname    =  "fmss_data";

// script koneksi php postgree
$link = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 
 
if($link)
{
    echo "Koneksi Berhasil";
}else
{
    echo "Gagal melakukan Koneksi";
}
?>