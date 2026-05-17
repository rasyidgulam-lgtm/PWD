<?php
include 'session.php';
include 'koneksi.php';

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'aslab') {
    header("Location: index.php");
    exit;
}

// TAMBAH MAHASISWA
if (isset($_POST['tambah_mhs'])) {
    $nim   = mysqli_real_escape_string($konek, $_POST['nim_mhs']);
    $nama  = mysqli_real_escape_string($konek, $_POST['nama_mhs']);
    $pw    = mysqli_real_escape_string($konek, $_POST['pw_mhs']);
    $askot = mysqli_real_escape_string($konek, $_POST['askot_mhs']);
    $kelas = mysqli_real_escape_string($konek, $_POST['id_kelas']);

    $cek = mysqli_query($konek, "SELECT id_mhs FROM data_mhs WHERE nim_mhs='$nim'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "NIM $nim sudah terdaftar!";
        $pesan_type = "danger";
    } else {
        mysqli_query($konek,
            "INSERT INTO data_mhs(nim_mhs, nama_mhs, pw_mhs, askot_mhs, id_kelas) VALUES('$nim','$nama','$pw','$askot','$kelas')");
        $id_baru = mysqli_insert_id($konek);
        mysqli_query($konek,
            "INSERT INTO users(nama, username, password, role, id_ref) VALUES('$nama','$nim','$pw','mahasiswa','$id_baru')");
        $pesan = "Mahasiswa $nama berhasil ditambahkan!";
        $pesan_type = "success";
    }
}

// Pesan dari redirect
if (isset($_GET['pesan'])) {
    $arr_pesan = [
        'hapus_ok'  => ['Mahasiswa berhasil dihapus.', 'success'],
        'edit_ok'   => ['Data mahasiswa berhasil diubah.', 'success'],
        'absen_ok'  => ['Data kehadiran berhasil diubah.', 'success'],
    ];
    if (isset($arr_pesan[$_GET['pesan']])) {
        $pesan      = $arr_pesan[$_GET['pesan']][0];
        $pesan_type = $arr_pesan[$_GET['pesan']][1];
    }
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'user';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="mb-3">
        <a href="index.php" class="btn btn-secondary btn-sm"> <- Kembali</a>
    </div>

    <?php if (isset($pesan)) : ?>
    <div class="alert alert-<?php echo $pesan_type; ?> alert-dismissible fade show">
        <?php echo $pesan; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link <?php echo $tab == 'user'  ? 'active' : ''; ?>" href="users.php?tab=user">Data User</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $tab == 'mhs'   ? 'active' : ''; ?>" href="users.php?tab=mhs">Kelola Mahasiswa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $tab == 'absen' ? 'active' : ''; ?>" href="users.php?tab=absen">Edit Kehadiran</a>
        </li>
    </ul>

    <!-- TAB: DATA USER -->
    <?php if ($tab == 'user') : ?>
    <div class="card shadow-sm">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">Data User</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr><th>No</th><th>NIM / Username</th><th>Nama</th><th>Role</th></tr>
                </thead>
                <tbody>
                <?php
                $badge = ['admin' => 'danger', 'aslab' => 'warning text-dark', 'mahasiswa' => 'primary'];
                $query = mysqli_query($konek,
                    "SELECT * FROM users ORDER BY FIELD(role,'admin','aslab','mahasiswa'), username ASC");
                $no = 1;
                while ($d = mysqli_fetch_assoc($query)) {
                    $warna = $badge[$d['role']] ?? 'secondary';
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $d['username']; ?></td>
                        <td><?php echo $d['nama']; ?></td>
                        <td><span class="badge bg-<?php echo $warna; ?>"><?php echo $d['role']; ?></span></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB: KELOLA MAHASISWA -->
    <?php elseif ($tab == 'mhs') : ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">Tambah Mahasiswa</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">NIM</label>
                        <input type="text" name="nim_mhs" class="form-control" required placeholder="cth: 124250099">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_mhs" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Password</label>
                        <input type="text" name="pw_mhs" class="form-control" required placeholder="cth: nama099">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Asal Kota</label>
                        <input type="text" name="askot_mhs" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kelas</label>
                        <select name="id_kelas" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <?php
                            $kelas = mysqli_query($konek, "SELECT * FROM kelas");
                            while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                <option value="<?php echo $k['id_kelas']; ?>"><?php echo $k['nama_kelas']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="tambah_mhs" class="btn btn-primary mt-3">Tambah</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">Daftar Mahasiswa</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr><th>No</th><th>NIM</th><th>Nama</th><th>Kelas</th><th>Asal Kota</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $query = mysqli_query($konek,
                    "SELECT data_mhs.*, kelas.nama_kelas
                     FROM data_mhs
                     JOIN kelas ON data_mhs.id_kelas = kelas.id_kelas
                     ORDER BY nim_mhs ASC");
                while ($d = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $d['nim_mhs']; ?></td>
                        <td><?php echo $d['nama_mhs']; ?></td>
                        <td><?php echo $d['nama_kelas']; ?></td>
                        <td><?php echo $d['askot_mhs']; ?></td>
                        <td class="d-flex gap-1">
                            <a href="edit.php?id=<?php echo $d['id_mhs']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="hapus.php?id=<?php echo $d['id_mhs']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus <?php echo $d['nama_mhs']; ?>? Absensinya ikut terhapus!')"> Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB: EDIT KEHADIRAN -->
    <?php elseif ($tab == 'absen') : ?>
    <div class="card shadow-sm">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">Data Kehadiran</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th><th>Nama</th><th>Mata Kuliah</th>
                        <th>Pertemuan</th><th>Jam</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $badge_absen = ['Hadir'=>'success','Izin'=>'info','Sakit'=>'warning','Alpha'=>'danger'];
                $query = mysqli_query($konek,
                    "SELECT absen.*, data_mhs.nama_mhs, matkul.nama_matkul, pertemuan.pertemuan_ke FROM absen
                     JOIN data_mhs  ON absen.id_mhs = data_mhs.id_mhs
                     JOIN matkul    ON absen.id_matkul = matkul.id_matkul
                     JOIN pertemuan ON absen.id_pertemuan = pertemuan.id_pertemuan
                     ORDER BY absen.id_absen DESC");
                while ($d = mysqli_fetch_assoc($query)) {
                    $warna = $badge_absen[$d['status']] ?? 'secondary';
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $d['nama_mhs']; ?></td>
                        <td><?php echo $d['nama_matkul']; ?></td>
                        <td>Pertemuan <?php echo $d['pertemuan_ke']; ?></td>
                        <td><?php echo $d['jam_masuk']; ?></td>
                        <td><span class="badge bg-<?php echo $warna; ?>"><?php echo $d['status']; ?></span></td>
                        <td>
                            <a href="edit_absen.php?id=<?php echo $d['id_absen']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>