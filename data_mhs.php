<?php
include 'session.php';
include 'koneksi.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4">
    <!-- Tombol Kembali -->
    <div class="mb-3">
        <a href="index.php" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">Data Mahasiswa</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Asal Kota</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $query = mysqli_query($konek,
                    "SELECT data_mhs.*, kelas.nama_kelas FROM data_mhs
                     JOIN kelas ON data_mhs.id_kelas = kelas.id_kelas
                     ORDER BY nim_mhs ASC");

                while ($d = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $d['nim_mhs']; ?></td>
                        <td><?php echo $d['nama_mhs']; ?></td>
                        <td><?php echo $d['nama_kelas']; ?></td>
                        <td><?php echo $d['askot_mhs']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>