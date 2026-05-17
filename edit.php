<?php
include 'session.php';
include 'koneksi.php';

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'aslab') {
    header("Location: index.php");
    exit;
}

$id = mysqli_real_escape_string($konek, $_GET['id']);

$res = mysqli_query($konek, "SELECT * FROM data_mhs WHERE id_mhs='$id'");
$mhs = mysqli_fetch_assoc($res);

if (!$mhs) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='users.php?tab=mhs';</script>";
    exit;
}

if (isset($_POST['simpan'])) {
    $nama  = mysqli_real_escape_string($konek, $_POST['nama_mhs']);
    $pw    = mysqli_real_escape_string($konek, $_POST['pw_mhs']);
    $askot = mysqli_real_escape_string($konek, $_POST['askot_mhs']);
    $kelas = mysqli_real_escape_string($konek, $_POST['id_kelas']);
    $nim   = $mhs['nim_mhs'];

    // Update data_mhs
    mysqli_query($konek,
        "UPDATE data_mhs
         SET nama_mhs='$nama', pw_mhs='$pw', askot_mhs='$askot', id_kelas='$kelas'
         WHERE id_mhs='$id'");

    // Update tabel users juga (nama & password)
    mysqli_query($konek,
        "UPDATE users SET nama='$nama', password='$pw'
         WHERE username='$nim'");

    header("Location: users.php?tab=mhs&pesan=edit_ok");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="mb-3">
        <a href="users.php?tab=mhs" class="btn btn-secondary btn-sm">&larr; Kembali</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">Edit Data Mahasiswa</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">NIM</label>
                    <input type="text" class="form-control" value="<?php echo $mhs['nim_mhs']; ?>" disabled>
                    <small class="text-muted">NIM tidak dapat diubah</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_mhs" class="form-control" required value="<?php echo $mhs['nama_mhs']; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="text" name="pw_mhs" class="form-control" required value="<?php echo $mhs['pw_mhs']; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Asal Kota</label>
                    <input type="text" name="askot_mhs" class="form-control" required value="<?php echo $mhs['askot_mhs']; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <?php
                        $kelas = mysqli_query($konek, "SELECT * FROM kelas");
                        while ($k = mysqli_fetch_assoc($kelas)) { ?>
                            <option value="<?php echo $k['id_kelas']; ?>"
                                <?php if ($k['id_kelas'] == $mhs['id_kelas']) echo 'selected'; ?>>
                                <?php echo $k['nama_kelas']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="users.php?tab=mhs" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>