<?php
session_start();

// Handle logout
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: login.php");
    exit;
}

include "db.php";

if(isset($_POST['login'])){
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $cek = mysqli_query($conn,"SELECT * FROM admin WHERE username='$user' AND password='$pass'");
    
    if(mysqli_num_rows($cek) > 0){
        $_SESSION['admin'] = $user;
        header("Location: dashboard.php");
        exit;
    }else{
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistem Aspirasi Sekolah</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">

    <h2>Sistem Aspirasi Sekolah</h2>

    <div class="main-box">

        <!-- ================= ADMIN ================= -->
        <div class="card-box">
            <h3>Login Admin</h3>

            <?php if(isset($error)){ ?>
                <p style="color:red; text-align:center;"><?php echo $error; ?></p>
            <?php } ?>

            <form method="POST">
                <label>Username</label>
                <input type="text" name="username" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <button name="login">Login</button>
            </form>
        </div>

        <!-- ================= SISWA ================= -->
        <div class="card-box">
            <h3>Menu Siswa</h3>

            <p style="text-align:center;">
                Gunakan menu ini untuk mengirim aspirasi atau melihat progres laporan Anda.
            </p>

            <br>

            <a href="form_aspirasi.php">
                <button>Isi Aspirasi</button>
            </a>

            <br><br>

            <a href="dashboard_siswa.php">
                <button>Lihat Progres Aspirasi</button>
            </a>

        </div>

    </div>

</div>

</body>
</html>