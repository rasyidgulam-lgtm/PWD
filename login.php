<?php
session_start();
include 'koneksi.php';
$error = '';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($konek,
    "SELECT * FROM users
    WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);
    if($data){
        if($password == $data['password']){
            $_SESSION['login'] = true;
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['id_mhs'] = $data['id_ref'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Password salah";
        }
    } else {
        $error = "Username tidak ditemukan";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-4">
                <div class="card shadow">
                    <img src="LogoUPN.png" class="card-img-top" style="height: 400px; object-fit: cover;">
                    <div class="card-body">
                        <h3 class="text-center mb-4">
                            <strong>Login Absensi Kelas Praktikum B</strong>
                        </h3>
                        <?php if($error != '') : ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text"
                                    name="username"
                                    class="form-control"
                                    required
                                    >
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password"
                                    name="password"
                                    class="form-control"
                                    required>
                            </div>
                            <button type="submit"
                                    name="login"
                                    class="btn btn-primary w-100">
                                Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>