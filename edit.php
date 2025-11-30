<?php
include "koneksi.php";

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM pengeluaran WHERE id='$id'");
$data = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffe6f2;
            padding: 30px;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #ff4da6;
            margin-bottom: 20px;
        }

        .container {
            width: 400px;
            margin: 0 auto;
            background: #fff0f7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255,105,180,0.2);
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            color: #ff66b3;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ffb3d9;
            border-radius: 5px;
            margin-top: 5px;
            outline: none;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #ff66b3;
            box-shadow: 0 0 5px rgba(255,102,179,0.5);
        }

        button {
            width: 100%;
            background: #ff66b3;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background: #ff4da6;
        }

        a {
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #ff4da6;
        }
    </style>
</head>
<body>

<h2>Edit Data Pengeluaran</h2>

<div class="container">
    <form method="POST" action="update.php">

        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

        <label>Tanggal</label>
        <input type="date" name="tanggal" value="<?php echo $data['tanggal']; ?>" required>

        <label>Jenis</label>
        <input type="text" name="jenis" value="<?php echo $data['jenis']; ?>" required>

        <label>Jumlah</label>
        <input type="number" name="jumlah" value="<?php echo $data['jumlah']; ?>" required>

        <label>Keterangan</label>
        <textarea name="keterangan" rows="3"><?php echo $data['keterangan']; ?></textarea>

        <button type="submit">Update Data</button>
    </form>

    <a href="index.php">← Kembali</a>
</div>

</body>
</html>
