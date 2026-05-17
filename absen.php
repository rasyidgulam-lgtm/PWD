<?php
include 'session.php';
include 'koneksi.php';

$role     = $_SESSION['role'];
$id_login = $_SESSION['id_mhs'];

// Data mahasiswa yang login
$mhs_login = null;
if ($role == 'mahasiswa') {
    $res_mhs   = mysqli_query($konek, "SELECT * FROM data_mhs WHERE id_mhs='$id_login'");
    $mhs_login = mysqli_fetch_assoc($res_mhs);
}

// Proses simpan absen
if (isset($_POST['absen'])) {
    $id_mhs       = ($role == 'mahasiswa') ? $id_login : mysqli_real_escape_string($konek, $_POST['id_mhs']);
    $id_matkul    = mysqli_real_escape_string($konek, $_POST['id_matkul']);
    $id_pertemuan = mysqli_real_escape_string($konek, $_POST['id_pertemuan']);
    $status       = mysqli_real_escape_string($konek, $_POST['status']);
    $keterangan   = mysqli_real_escape_string($konek, $_POST['keterangan']);
    $jam          = date('H:i:s');

    $cek = mysqli_query($konek,
        "SELECT id_absen FROM absen WHERE id_mhs='$id_mhs' AND id_pertemuan='$id_pertemuan'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Sudah absen di pertemuan ini!'); window.location='absen.php';</script>";
    } else {
        mysqli_query($konek,
            "INSERT INTO absen(id_mhs, id_matkul, id_pertemuan, jam_masuk, status, keterangan)
             VALUES('$id_mhs','$id_matkul','$id_pertemuan','$jam','$status','$keterangan')");
        echo "<script>alert('Absensi berhasil disimpan!'); window.location='absen.php';</script>";
    }
}

// Array status kehadiran
$arr_status = ['Hadir', 'Izin', 'Sakit', 'Alpha'];

// Semua matkul
$res_matkul = mysqli_query($konek, "SELECT * FROM matkul");
$arr_matkul = [];
while ($m = mysqli_fetch_assoc($res_matkul)) {
    $arr_matkul[] = $m;
}

// Semua pertemuan
$res_pt = mysqli_query($konek, "SELECT * FROM pertemuan ORDER BY id_matkul, pertemuan_ke");
$arr_pertemuan = [];
while ($p = mysqli_fetch_assoc($res_pt)) {
    $arr_pertemuan[] = $p;
}

// id_kelas mahasiswa login
$id_kelas_login = ($role == 'mahasiswa' && $mhs_login) ? (int)$mhs_login['id_kelas'] : 0;

// Mapping id_mhs => id_kelas untuk admin/aslab
$res_mk = mysqli_query($konek, "SELECT id_mhs, id_kelas FROM data_mhs");
$arr_mhs_kelas = [];
while ($row = mysqli_fetch_assoc($res_mk)) {
    $arr_mhs_kelas[(int)$row['id_mhs']] = (int)$row['id_kelas'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Form Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4">
    <div class="mb-3">
        <a href="index.php" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">Form Absensi</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="absen.php">

                <?php if ($role == 'mahasiswa') : ?>
                <div class="mb-3">
                    <label class="form-label">Mahasiswa</label>
                    <input type="text" class="form-control"
                           value="<?php echo $mhs_login['nim_mhs'] . ' - ' . $mhs_login['nama_mhs']; ?>" disabled>
                </div>
                <?php else : ?>
                <div class="mb-3">
                    <label class="form-label">Mahasiswa</label>
                    <select name="id_mhs" id="select_mhs" class="form-select" required onchange="gantiMahasiswa(this.value)">
                        <option value="">-- Pilih Mahasiswa --</option>
                        <?php
                        $res_mhs_all = mysqli_query($konek, "SELECT * FROM data_mhs ORDER BY nama_mhs");
                        while ($d = mysqli_fetch_assoc($res_mhs_all)) { ?>
                            <option value="<?php echo $d['id_mhs']; ?>">
                                <?php echo $d['nim_mhs'] . ' - ' . $d['nama_mhs']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Mata Kuliah -->
                <div class="mb-3">
                    <label class="form-label">Mata Kuliah</label>
                    <select name="id_matkul" id="select_matkul" class="form-select" required onchange="filterPertemuan()">
                        <option value="">Pilih Mata Kuliah</option>
                        <?php foreach ($arr_matkul as $m) : ?>
                            <option value="<?php echo $m['id_matkul']; ?>">
                                <?php echo $m['nama_matkul']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pertemuan</label>
                    <select name="id_pertemuan" id="select_pertemuan" class="form-select" required>
                        <option value="">Pilih Mata Kuliah dahulu</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label class="form-label">Status Kehadiran</label>
                    <select name="status" class="form-select" required>
                        <?php foreach ($arr_status as $s) : ?>
                            <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional"></textarea>
                </div>

                <button type="submit" name="absen" class="btn btn-primary">Simpan Absensi</button>
            </form>
        </div>
    </div>
</div>

<script>
var dataPertemuan = <?php echo json_encode($arr_pertemuan); ?>;
var dataMhsKelas = <?php echo json_encode($arr_mhs_kelas); ?>;
var idKelasAktif = <?php echo $id_kelas_login; ?>;

function gantiMahasiswa(idMhs) {
    idKelasAktif = idMhs ? (dataMhsKelas[idMhs] || 0) : 0;
    filterPertemuan();
}

function filterPertemuan() {
    var selectPt = document.getElementById('select_pertemuan');
    var idMatkul = document.getElementById('select_matkul').value;

    selectPt.innerHTML = '<option value="">-- Pilih Pertemuan --</option>';

    if (idMatkul === '') {
        selectPt.innerHTML = '<option value="">-- Pilih Mata Kuliah dahulu --</option>';
        return;
    }

    var filtered = dataPertemuan.filter(function(p) {
        var cocokMatkul = (p.id_matkul == idMatkul);
        var cocokKelas  = (idKelasAktif === 0) ? true : (p.id_kelas == idKelasAktif);
        return cocokMatkul && cocokKelas;
    });

    if (filtered.length === 0) {
        selectPt.innerHTML = '<option value="">Belum ada pertemuan untuk mata kuliah ini</option>';
        return;
    }

    for (var i = 0; i < filtered.length; i++) {
        var p     = filtered[i];
        var label = 'Pertemuan ' + p.pertemuan_ke;
        if (p.topik) label += ' - ' + p.topik;
        label += ' (' + p.tanggal + ')';

        var opt   = document.createElement('option');
        opt.value = p.id_pertemuan;
        opt.text  = label;
        selectPt.appendChild(opt);
    }
}
</script>
</body>
</html>