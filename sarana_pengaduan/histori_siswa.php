<?php
include "db.php";

// pastikan id dan nis tersedia
if(!isset($_GET['id']) || $_GET['id']=='' || !isset($_GET['nis']) || $_GET['nis']==''){
    header("Location: dashboard_siswa.php");
    exit;
}

$id = $_GET['id'];
$nis = $_GET['nis'];

// ambil histori untuk laporan tertentu dan pastikan nis cocok
$data = mysqli_query($conn,"
    SELECT hs.*, ia.nis, k.ket_kategori
    FROM histori_status hs
    LEFT JOIN input_aspirasi ia ON hs.id_pelaporan = ia.id_pelaporan
    LEFT JOIN kategori k ON ia.id_kategori = k.id_kategori
    WHERE hs.id_pelaporan='$id' AND ia.nis='$nis'
    ORDER BY hs.updated_at DESC
");

$cek = mysqli_num_rows($data);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Histori Aspirasi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="history-container">

    <h2>Histori Aspirasi</h2>

    <a href="dashboard_siswa.php?nis=<?php echo $nis; ?>" class="btn-back">⬅ Kembali ke Dashboard</a>

    <?php if($cek > 0){ ?>
    <table class="history-table">
        <tr>
            <th>NIS</th>
            <th>Kategori</th>
            <th>Status</th>
            <th>Feedback</th>
            <th>Waktu Update</th>
        </tr>

        <?php while($h = mysqli_fetch_assoc($data)){
            $status = $h['status'];
            if($status == "Menunggu"){
                $class = "status-menunggu";
            } elseif($status == "Proses"){
                $class = "status-proses";
            } else {
                $class = "status-selesai";
            }
        ?>
        <tr>
            <td><?php echo $h['nis']; ?></td>
            <td><?php echo $h['ket_kategori']; ?></td>
            <td class="<?php echo $class; ?>">
                <?php echo $status; ?>
            </td>
            <td><?php if($h['feedback']){ echo $h['feedback']; } else { echo '-'; } ?></td>
            <td><?php echo date('d-m-Y H:i', strtotime($h['updated_at'])); ?></td>
        </tr>
        <?php } ?>
    </table>

    <?php } else { ?>

        <p style="text-align:center; margin-top:20px;">
            Belum ada histori perubahan untuk aspirasi ini.
        </p>

    <?php } ?>

</div>

</body>
</html>