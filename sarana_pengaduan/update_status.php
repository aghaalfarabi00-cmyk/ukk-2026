<?php
session_start();
include "db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: list_aspirasi.php");
    exit;
}

$id = $_GET['id'];

$data = mysqli_query($conn,"SELECT ia.*, k.ket_kategori FROM input_aspirasi ia JOIN kategori k ON ia.id_kategori = k.id_kategori WHERE ia.id_pelaporan='$id'");
$row = mysqli_fetch_assoc($data);

if(isset($_POST['status'])){
    $status = $_POST['status'];
    $feedback = $_POST['feedback'];

    $cek = mysqli_query($conn,"SELECT progress FROM aspirasi WHERE id_pelaporan='$id'");
    $progress = 0;
    if(mysqli_num_rows($cek) > 0){
        $old = mysqli_fetch_assoc($cek);
        if(isset($old['progress'])){
            $progress = $old['progress'];
        } else {
            $progress = 0;
        }
    }

    if($status == "Menunggu"){
        $progress = 0;
    }elseif($status == "Proses"){
        $progress = min($progress + 20, 100);
    }else{
        $progress = 100;
    }

    mysqli_query($conn,"INSERT INTO aspirasi (id_pelaporan,status,id_kategori,feedback,progress,updated_at) VALUES ('$id','$status','".$row['id_kategori']."','$feedback','$progress',NOW()) ON DUPLICATE KEY UPDATE status='$status', feedback='$feedback', progress='$progress', updated_at=NOW()");

    mysqli_query($conn,"INSERT INTO histori_status (id_pelaporan,status,feedback,updated_at) VALUES ('$id','$status','$feedback',NOW())");

    header("Location: list_aspirasi.php");
    exit;
}
?>

<link rel="stylesheet" href="assets/style.css">
<div class="container">
    <h2>Update Status</h2>
    <div class="detail-box">
        <p><strong>NIS:</strong> <?php echo $row['nis']; ?></p>
        <p><strong>Kategori:</strong> <?php echo $row['ket_kategori']; ?></p>
        <p><strong>Lokasi:</strong> <?php echo $row['lokasi']; ?></p>
        <p><strong>Keterangan:</strong> <?php echo $row['ket']; ?></p>
    </div>

    <form method="POST">
        <label>Status</label>
        <select name="status" required>
            <option value="Menunggu">Menunggu</option>
            <option value="Proses">Proses</option>
            <option value="Selesai">Selesai</option>
        </select>

        <label>Feedback</label>
        <textarea name="feedback" placeholder="Tulis respon..." required></textarea>

        <button type="submit">Simpan</button>
    </form>

    <br>
    <a href="list_aspirasi.php" class="btn-kembali">← Kembali</a>
</div>