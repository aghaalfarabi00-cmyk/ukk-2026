<?php
session_start();
include "db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

$menunggu = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM aspirasi WHERE status='Menunggu'"))['total'];
$proses = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM aspirasi WHERE status='Proses'"))['total'];
$selesai = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM aspirasi WHERE status='Selesai'"))['total'];

$notif = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM input_aspirasi ia LEFT JOIN aspirasi a ON ia.id_pelaporan = a.id_pelaporan WHERE a.status IS NULL OR a.status='Menunggu'"))['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <h2>Dashboard Admin</h2>

    <div class="top-bar">
        <div class="notif-box">
            🔔 Menunggu: <span class="notif-count"><?php echo $notif; ?></span>
        </div>
        <div class="menu-link">
            <a href="list_aspirasi.php">Data Pengaduan</a>
            <a href="login.php?logout=1">Logout</a>
        </div>
    </div>

    <hr>

    <h3>Statistik</h3>
    <div class="stat-box">
        <div class="card red">Menunggu<br><b><?php echo $menunggu; ?></b></div>
        <div class="card orange">Proses<br><b><?php echo $proses; ?></b></div>
        <div class="card green">Selesai<br><b><?php echo $selesai; ?></b></div>
    </div>

</div>

</body>
</html>