<?php
session_start();
include "db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

$where = [];

if(!empty($_GET['nis'])){
    $where[] = "ia.nis LIKE '%".$_GET['nis']."%'";
}

if(!empty($_GET['kategori'])){
    $where[] = "ia.id_kategori='".$_GET['kategori']."'";
}

if(!empty($_GET['tanggal'])){
    $tanggal = $_GET['tanggal'];
    $where[] = "ia.created_at >= '$tanggal 00:00:00' AND ia.created_at <= '$tanggal 23:59:59'";
}

$whereSQL = "";
if(count($where) > 0){
    $whereSQL = "WHERE " . implode(" AND ", $where);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pengaduan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <h2>Daftar Pengaduan Siswa</h2>
    <a href="dashboard.php">⬅ Dashboard</a>

    <form method="GET" style="margin:15px 0;">
        <label>NIS:</label>
        <input type="text" name="nis" value="<?php if(isset($_GET['nis'])){ echo $_GET['nis']; } else { echo ''; } ?>" style="width:150px;">

        <label>Kategori:</label>
        <select name="kategori" style="width:150px;">
            <option value="">Semua</option>
            <?php
            $kat = mysqli_query($conn,"SELECT * FROM kategori");
            while($k = mysqli_fetch_assoc($kat)){
                if(isset($_GET['kategori']) && $_GET['kategori']==$k['id_kategori']){
                    $sel = 'selected';
                } else {
                    $sel = '';
                }
                echo "<option value='".$k['id_kategori']."' $sel>".$k['ket_kategori']."</option>";
            }
            ?>
        </select>

        <label>Tanggal:</label>
        <input type="date" name="tanggal" value="<?php if(isset($_GET['tanggal'])){ echo $_GET['tanggal']; } else { echo ''; } ?>" style="width:150px;">

        <button type="submit" style="width:auto; padding:8px 20px;">Filter</button>
    </form>

    <table>
        <tr>
            <th>NIS</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Progress</th>
            <th class="actions">Aksi</th>
        </tr>

        <?php
        $query = mysqli_query($conn,"SELECT ia.*, k.ket_kategori, a.status, a.feedback, a.progress FROM input_aspirasi ia LEFT JOIN kategori k ON ia.id_kategori = k.id_kategori LEFT JOIN aspirasi a ON ia.id_pelaporan = a.id_pelaporan $whereSQL ORDER BY ia.created_at DESC");

        while($row = mysqli_fetch_assoc($query)){
            if(isset($row['status'])){
                $status = $row['status'];
            } else {
                $status = 'Menunggu';
            }
            if($status == 'Menunggu'){
                $statusClass = 'status-menunggu';
            } elseif($status == 'Proses'){
                $statusClass = 'status-proses';
            } else {
                $statusClass = 'status-selesai';
            }
        ?>
        <tr>
            <td><?php echo $row['nis']; ?></td>
            <td><?php echo $row['ket_kategori']; ?></td>
            <td><?php echo $row['lokasi']; ?></td>
            <td><?php echo date('d-m-Y', strtotime($row['created_at'])); ?></td>
            <td class="<?php echo $statusClass; ?>"><?php echo $status; ?></td>
            <td><?php if(isset($row['progress'])){ echo $row['progress']; } else { echo 0; } ?>%</td>
            <td class="actions">
                <a href="update_status.php?id=<?php echo $row['id_pelaporan']; ?>">Update</a>
                <a href="histori.php?id=<?php echo $row['id_pelaporan']; ?>">Histori</a>
                <a href="delete_aspirasi.php?id=<?php echo $row['id_pelaporan']; ?>" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>
</body>
</html>

</body>
</html>