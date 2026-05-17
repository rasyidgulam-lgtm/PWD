<?php
include 'session.php';
include 'koneksi.php';

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'aslab') {
    header("Location: index.php");
    exit;
}

$id = mysqli_real_escape_string($konek, $_GET['id']);

// Ambil data absen
$res = mysqli_query($konek,
    "SELECT absen.*, data_mhs.nama_mhs, data_mhs.nim_mhs,
            matkul.nama_matkul, pertemuan.pertemuan_ke, pertemuan.tanggal
     FROM absen
     JOIN data_mhs  ON absen.id_mhs = data_mhs.id_mhs
     JOIN matkul    ON absen.id_matkul = matkul.id_matkul
     JOIN pertemuan ON absen.id_pertemuan = pertemuan.id_pertemuan
     WHERE absen.id_absen='$id'");
$absen = mysqli_fetch_assoc($res);

if (!$absen) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='users.php?tab=absen';</script>";
    exit;
}

// Proses simpan
if (isset($_POST['simpan'])) {
    $status = mysqli_real_escape_string($konek, $_POST['status']);
    $ket    = mysqli_real_escape_string($konek, $_POST['keterangan']);

    mysqli_query($konek,
        "UPDATE absen SET status='$status', keterangan='$ket' WHERE id_absen='$id'");

    header("Location: users.php?tab=absen&pesan=absen_ok");
    exit;
}

$arr_status = ['Hadir', 'Izin', 'Sakit', 'Alpha'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="mb-3">
        <a href="users.php?tab=absen" class="btn btn-secondary btn-sm"> <-Kembali</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">Edit Data Kehadiran</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="130">Mahasiswa</td>
                            <td>: <b><?php echo $absen['nim_mhs'] . ' - ' . $absen['nama_mhs']; ?></b></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Mata Kuliah</td>
                            <td>: <?php echo $absen['nama_matkul']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pertemuan</td>
                            <td>: Ke-<?php echo $absen['pertemuan_ke']; ?>
                                (<?php echo $absen['tanggal']; ?>)</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jam Masuk</td>
                            <td>: <?php echo $absen['jam_masuk']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status Kehadiran</label>
                    <select name="status" class="form-select">
                        <?php foreach ($arr_status as $s) { ?>
                            <option value="<?php echo $s; ?>"
                                <?php if ($absen['status'] == $s) echo 'selected'; ?>>
                                <?php echo $s; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional"><?php echo $absen['keterangan']; ?></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="users.php?tab=absen" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>