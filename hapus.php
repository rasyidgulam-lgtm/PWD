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
    header("Location: users.php?tab=mhs");
    exit;
}

$nim = $mhs['nim_mhs'];

mysqli_query($konek, "DELETE FROM absen WHERE id_mhs='$id'");
mysqli_query($konek, "DELETE FROM users WHERE username='$nim'");
mysqli_query($konek, "DELETE FROM data_mhs WHERE id_mhs='$id'");

header("Location: users.php?tab=mhs&pesan=hapus_ok");
exit;
?>