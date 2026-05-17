<?php
include 'session.php';
include 'koneksi.php';

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$stats = [
    ['label' => 'Total Mahasiswa', 'value' => mysqli_num_rows(mysqli_query($konek, "SELECT * FROM data_mhs")),  'warna' => 'primary'],
    ['label' => 'Mata Kuliah',     'value' => mysqli_num_rows(mysqli_query($konek, "SELECT * FROM matkul")),    'warna' => 'success'],
    ['label' => 'Total Kelas',     'value' => mysqli_num_rows(mysqli_query($konek, "SELECT * FROM kelas")),     'warna' => 'warning'],
    ['label' => 'Total Absensi',   'value' => mysqli_num_rows(mysqli_query($konek, "SELECT * FROM absen")),     'warna' => 'danger'],
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="LogoUPN.png" alt="logo" width="35" height="30" class="d-inline-block align-text-top">
                Web Absensi
            </a>
            <div class="ms-auto text-white">
                Login sebagai: <b><?php echo $username; ?></b>
                (<?php echo $role; ?>)
                <a href="logout.php" class="btn btn-sm btn-light ms-3">Logout</a>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="container mt-5">
        <h3 class="mb-4">Dashboard</h3>

        <div class="row">
            <?php foreach ($stats as $s) : ?>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 text-center stat-card">
                    <div class="card-body">
                        <!-- Perbaikan: gunakan class stat-{warna} dari style.css -->
                        <h1 class="stat-<?php echo $s['warna']; ?>">
                            <?php echo $s['value']; ?>
                        </h1>
                        <p class="mb-0 text-muted"><?php echo $s['label']; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- MENU -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header card-header-custom">
                <h5 class="mb-0">Menu</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="absen.php"    class="btn btn-primary">Absen</a>
                    <a href="riwayat.php"  class="btn btn-primary">Riwayat Absen</a>
                    <a href="data_mhs.php" class="btn btn-primary">Data Mahasiswa</a>
                    <?php if ($role == 'admin' || $role == 'aslab') : ?>
                        <a href="users.php" class="btn btn-primary">Kelola User</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>