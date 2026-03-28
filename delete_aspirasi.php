<?php
session_start();
include "db.php";

/*
  Script yang dapat dipanggil baik oleh admin maupun siswa.
  - Admin : harus login dan akan diarahkan kembali ke list_aspirasi.php
  - Siswa : tidak perlu login, tetapi harus memberikan nis dan hanya boleh
    menghapus aspirasi yang memang miliknya. Setelah hapus diarahkan kembali ke
    dashboard_siswa.php dengan nis yang sama.
*/

// cek apakah admin
$isAdmin = isset($_SESSION['admin']);

// ambil id
if(!isset($_GET['id']) || $_GET['id']==''){
    if($isAdmin){
        header("Location: list_aspirasi.php");
    } else {
        header("Location: dashboard_siswa.php");
    }
    exit;
}

$id = $_GET['id'];

// jika bukan admin, periksa nis
$nisPemilik = null;
if(!$isAdmin){
    if(!isset($_GET['nis']) || $_GET['nis']==''){
        header("Location: dashboard_siswa.php");
        exit;
    }
    $nisPemilik = $_GET['nis'];

    // pastikan aspirasi memang milik nis tersebut
    $cek = mysqli_query($conn, "SELECT nis FROM input_aspirasi WHERE id_pelaporan='$id'");
    $row = mysqli_fetch_assoc($cek);
    if(!$row || $row['nis'] !== $nisPemilik){
        // tidak berhak
        header("Location: dashboard_siswa.php?nis=".urlencode($nisPemilik));
        exit;
    }
}

// lakukan penghapusan
mysqli_query($conn, "DELETE FROM histori_status WHERE id_pelaporan='$id'");
mysqli_query($conn, "DELETE FROM aspirasi WHERE id_pelaporan='$id'");
mysqli_query($conn, "DELETE FROM input_aspirasi WHERE id_pelaporan='$id'");

if($isAdmin){
    header("Location: list_aspirasi.php");
} else {
    header("Location: dashboard_siswa.php?nis=".urlencode($nisPemilik));
}
exit;
