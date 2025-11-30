<?php
include "koneksi.php";

if(isset($_POST['simpan'])){
    $tanggal = $_POST['tanggal'];
    $jenis = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    mysqli_query($koneksi, "INSERT INTO pengeluaran (tanggal, jenis, jumlah, keterangan)
                            VALUES ('$tanggal', '$jenis', '$jumlah', '$keterangan')");

    header("location:index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengeluaran</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffe6f2;
            padding: 20px;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #ff4da6;
            margin-bottom: 20px;
        }

        form {
            width: 350px;
            margin: auto;
            background: #fff0f7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 105, 180, 0.3);
        }

        label {
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ffb3d9;
            margin-top: 5px;
            margin-bottom: 15px;
            outline: none;
        }

        input:focus, textarea:focus {
            border-color: #ff66b3;
            box-shadow: 0 0 5px rgba(255, 102, 179, 0.5);
        }

        button {
            width: 100%;
            padding: 10px;
            background: #ff66b3;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #ff4da6;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #ff4da6;
        }
    </style>

</head>
<body>

<h2>Tambah Data Pengeluaran</h2>

<form method="post">
    <label>Tanggal</label>
    <input type="date" name="tanggal" required>

    <label>Jenis</label>
    <input type="text" name="jenis" required>

    <label>Jumlah</label>
    <input type="number" name="jumlah" required>

    <label>Keterangan</label>
    <textarea name="keterangan"></textarea>

    <button type="submit" name="simpan">Simpan</button>

    <a href="index.php">Kembali</a>
</form>

</body>
</html>
