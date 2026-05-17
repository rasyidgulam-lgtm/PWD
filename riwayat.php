<?php
include 'session.php';
include 'koneksi.php';

$role     = $_SESSION['role'];
$id_login = $_SESSION['id_mhs'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Absensi</title>
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
            <h5 class="mb-0">Riwayat Absensi</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Matkul</th>
                        <th>Pertemuan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $badge_status = [
                    'Hadir' => 'success',
                    'Izin'  => 'info',
                    'Sakit' => 'warning',
                    'Alpha' => 'danger',
                ];

                $no = 1;

                if ($role == 'mahasiswa') {
                    $query = mysqli_query($konek,
                        "SELECT absen.*, data_mhs.nama_mhs, matkul.nama_matkul, pertemuan.pertemuan_ke, pertemuan.tanggal FROM absen
                         JOIN data_mhs  ON absen.id_mhs = data_mhs.id_mhs
                         JOIN matkul    ON absen.id_matkul = matkul.id_matkul
                         JOIN pertemuan ON absen.id_pertemuan = pertemuan.id_pertemuan
                         WHERE absen.id_mhs = '$id_login'
                         ORDER BY absen.id_absen DESC");
                } else {
                    // Admin dan aslab melihat semua riwayat
                    $query = mysqli_query($konek,
                        "SELECT absen.*, data_mhs.nama_mhs, matkul.nama_matkul, pertemuan.pertemuan_ke, pertemuan.tanggal FROM absen
                         JOIN data_mhs  ON absen.id_mhs = data_mhs.id_mhs
                         JOIN matkul    ON absen.id_matkul = matkul.id_matkul
                         JOIN pertemuan ON absen.id_pertemuan = pertemuan.id_pertemuan
                         ORDER BY absen.id_absen DESC");
                }

                while ($d = mysqli_fetch_assoc($query)) {
                    $warna = isset($badge_status[$d['status']]) ? $badge_status[$d['status']] : 'secondary';
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $d['nama_mhs']; ?></td>
                        <td><?php echo $d['nama_matkul']; ?></td>
                        <td>Pertemuan <?php echo $d['pertemuan_ke']; ?></td>
                        <td><?php echo $d['tanggal']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $warna; ?>">
                                <?php echo $d['status']; ?>
                            </span>
                        </td>
                        <td><?php echo $d['keterangan'] ? $d['keterangan'] : '-'; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>