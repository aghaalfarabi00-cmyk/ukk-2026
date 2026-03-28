<?php
include "db.php";

$filter = "";
if(isset($_GET['filter'])){
    $filter = $_GET['filter'];
}

if(!isset($_GET['nis'])){
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cek Progres</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h2>Cek Progres Pengaduan</h2>
    <form method="GET">
        <label>NIS</label>
        <input type="text" name="nis" required>
        <button type="submit">Lihat</button>
    </form>
    <br>
    <a href="form_aspirasi.php">← Kembali</a>
</div>
</body>
</html>
<?php
exit;
}

$nis = $_GET['nis'];
$where = "WHERE ia.nis='$nis'";
if($filter != ""){
    $where .= " AND a.status='$filter'";
}

$query = "SELECT ia.*, k.ket_kategori, a.status, a.feedback, a.progress FROM input_aspirasi ia JOIN kategori k ON ia.id_kategori = k.id_kategori LEFT JOIN aspirasi a ON ia.id_pelaporan = a.id_pelaporan $where ORDER BY ia.id_pelaporan DESC";

$data = mysqli_query($conn,$query);

$statQuery = mysqli_query($conn,"SELECT a.status FROM input_aspirasi ia LEFT JOIN aspirasi a ON ia.id_pelaporan = a.id_pelaporan WHERE ia.nis='$nis'");

$menunggu = $proses = $selesai = 0;
while($rowStat = mysqli_fetch_assoc($statQuery)){
    if($rowStat['status']==NULL || $rowStat['status']=='Menunggu'){
        $menunggu++;
    }elseif($rowStat['status']=='Proses'){
        $proses++;
    }else{
        $selesai++;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
<h2>Dashboard Siswa</h2>
<p><strong>NIS: </strong><?php echo $nis; ?></p>
<a href="form_aspirasi.php" class="btn-kembali">← Kembali</a>

<h3>Statistik</h3>
<div class="stat-box">
    <div class="card red">Menunggu<br><b><?php echo $menunggu; ?></b></div>
    <div class="card orange">Proses<br><b><?php echo $proses; ?></b></div>
    <div class="card green">Selesai<br><b><?php echo $selesai; ?></b></div>
</div>

<form method="GET" style="margin-bottom:15px;">
    <input type="hidden" name="nis" value="<?php echo $nis; ?>">
    <label>Filter:</label>
    <select name="filter">
        <option value="">Semua</option>
        <option value="Menunggu" <?= ($filter=="Menunggu")?'selected':'' ?>>Menunggu</option>
        <option value="Proses" <?= ($filter=="Proses")?'selected':'' ?>>Proses</option>
        <option value="Selesai" <?= ($filter=="Selesai")?'selected':'' ?>>Selesai</option>
    </select>
    <button type="submit">Filter</button>
</form>

<table>
<tr>
    <th>Kategori</th>
    <th>Lokasi</th>
    <th>Status</th>
    <th>Progress</th>
    <th>Aksi</th>
</tr>

<?php while($d = mysqli_fetch_assoc($data)){ 
    if(isset($d['status'])){
        $status = $d['status'];
    } else {
        $status = 'Menunggu';
    }
    if($status=='Menunggu'){
        $statusClass = 'status-menunggu';
    } elseif($status=='Proses'){
        $statusClass = 'status-proses';
    } else {
        $statusClass = 'status-selesai';
    }
?>
<tr>
    <td><?php echo $d['ket_kategori']; ?></td>
    <td><?php echo $d['lokasi']; ?></td>
    <td class="<?php echo $statusClass; ?>"><?php echo $status; ?></td>
    <td><?php if(isset($d['progress'])){ echo $d['progress']; } else { echo 0; } ?>%</td>
    <td>
        <a href="histori_siswa.php?id=<?php echo $d['id_pelaporan']; ?>&nis=<?php echo $nis; ?>">Histori</a> |
        <a href="delete_aspirasi.php?id=<?php echo $d['id_pelaporan']; ?>&nis=<?php echo $nis; ?>" style="color:red;" onclick="return confirm('Hapus?')">Hapus</a>
    </td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>

<hr>

<h3>Filter Status</h3>

<form method="GET">
    <input type="hidden" name="nis" value="<?= $nis ?>">
    <select name="filter">
        <option value="">Semua</option>
        <option value="Menunggu" <?= ($filter=="Menunggu")?'selected':'' ?>>Menunggu</option>
        <option value="Proses" <?= ($filter=="Proses")?'selected':'' ?>>Proses</option>
        <option value="Selesai" <?= ($filter=="Selesai")?'selected':'' ?>>Selesai</option>
    </select>
    <button type="submit">Tampilkan</button>
</form>

<hr>

<h3>Riwayat Aspirasi</h3>

<table>
<tr>
    <th>Tanggal</th>
    <th>Kategori</th>
    <th>Lokasi</th>
    <th>Keterangan</th>
    <th>Status</th>
    <th>Progress</th>
    <th>Feedback</th>
    <th>Histori</th>
</tr>

<?php while($d = mysqli_fetch_assoc($data)){ ?>
<tr>
    <td><?= date('d-m-Y H:i', strtotime($d['created_at'])) ?></td>
    <td><?= $d['ket_kategori'] ?></td>
    <td><?= $d['lokasi'] ?></td>
    <td><?= $d['ket'] ?></td>

    <td>
        <?php
        $status = $d['status'] ?? 'Menunggu';

        if($status=='Menunggu'){
            echo "<span style='color:red;font-weight:bold;'>Menunggu</span>";
        }elseif($status=='Proses'){
            echo "<span style='color:orange;font-weight:bold;'>Proses</span>";
        }else{
            echo "<span style='color:lightgreen;font-weight:bold;'>Selesai</span>";
        }
        ?>
    </td>

    <td><?= $d['progress'] ?? 0 ?>%</td>

    <td><?= $d['feedback'] ? $d['feedback'] : '-' ?></td>

    <td>
        <a href="histori_siswa.php?id=<?= $d['id_pelaporan'] ?>&nis=<?= $nis ?>">Lihat</a><br>
        <a href="delete_aspirasi.php?id=<?= $d['id_pelaporan'] ?>&nis=<?= $nis ?>" 
           onclick="return confirm('Yakin ingin menghapus aspirasi ini?');" 
           style="color:red;">Hapus</a>
    </td>
</tr>
<?php } ?>

</table>

<br>
<a href="form_aspirasi.php">← Kirim Aspirasi Lagi</a>

</div>

</body>
</html>