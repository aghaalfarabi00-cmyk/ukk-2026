<?php
include "db.php";

$nis = "";
$kelas = "";

if(isset($_GET['nis'])){
    $nis = $_GET['nis'];
    $cekSiswa = mysqli_query($conn,"SELECT * FROM siswa WHERE nis='$nis'");
    if(mysqli_num_rows($cekSiswa) > 0){
        $dataSiswa = mysqli_fetch_assoc($cekSiswa);
        $kelas = $dataSiswa['kelas'];
    }
}

if(isset($_POST['kirim'])){
    $nis = $_POST['nis'];
    $kelas = $_POST['kelas'];
    $kategori = $_POST['kategori'];
    $lokasi = $_POST['lokasi'];
    $ket = $_POST['ket'];

    $cek = mysqli_query($conn,"SELECT * FROM siswa WHERE nis='$nis'");
    if(mysqli_num_rows($cek) == 0){
        mysqli_query($conn,"INSERT INTO siswa (nis,kelas) VALUES ('$nis','$kelas')");
    }

    mysqli_query($conn,"INSERT INTO input_aspirasi (nis,id_kategori,lokasi,ket,created_at) VALUES ('$nis','$kategori','$lokasi','$ket',NOW())");
    $last_id = mysqli_insert_id($conn);
    mysqli_query($conn,"INSERT INTO aspirasi (id_pelaporan,id_kategori,status) VALUES ('$last_id','$kategori','Menunggu')");

    header("Location: dashboard_siswa.php?nis=$nis");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Pengaduan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <h2>Pengaduan Sarana Sekolah</h2>
    <a href="login.php" class="btn-kembali">← Kembali</a>

    <div class="main-box">
        <div class="card-box">
            <h3>Kirim Pengaduan Baru</h3>
            <form method="POST">
                <label>NIS</label>
                <input type="text" name="nis" value="<?php echo $nis; ?>" required>

                <label>Kelas</label>
                <input type="text" name="kelas" value="<?php echo $kelas; ?>" required>

                <label>Kategori</label>
                <select name="kategori" required>
                    <option value="">-- Pilih --</option>
                    <?php
                    $kat = mysqli_query($conn,"SELECT * FROM kategori");
                    while($k = mysqli_fetch_assoc($kat)){
                        echo "<option value='".$k['id_kategori']."'>".$k['ket_kategori']."</option>";
                    }
                    ?>
                </select>

                <label>Lokasi</label>
                <input type="text" name="lokasi" required>

                <label>Keterangan</label>
                <textarea name="ket" required></textarea>

                <button name="kirim">Kirim</button>
            </form>
        </div>

        <div class="card-box">
            <h3>Lihat Progres</h3>
            <p>Masukkan NIS Anda untuk melihat status pengaduan.</p>
            <form method="GET" action="dashboard_siswa.php">
                <label>NIS</label>
                <input type="text" name="nis" required>
                <button type="submit">Lihat</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>