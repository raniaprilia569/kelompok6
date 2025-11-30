<?php
include "koneksi.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengeluaran</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffe6f2; /* pink muda */
            padding: 20px;
            color: #333;
        }

        h2 {
            color: #ff4da6;
            text-align: center;
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
            background: #ff66b3;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 14px;
        }

        a:hover {
            background: #ff4da6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff0f7;
            box-shadow: 0 0 10px rgba(255, 105, 180, 0.2);
            border-radius: 8px;
            overflow: hidden;
        }

        table th {
            background: #ff99cc;
            color: white;
            padding: 12px;
            font-size: 16px;
        }

        table td {
            border: 1px solid #ffcce6;
            padding: 10px;
            text-align: center;
        }

        table tr:nth-child(even) {
            background: #ffe6f2;
        }

        .btn-delete {
            background: #ff3366;
            padding: 6px 10px;
            border-radius: 5px;
        }

        .btn-delete:hover {
            background: #cc0052;
        }

        .btn-edit {
            background: #ff66cc;
            padding: 6px 10px;
            border-radius: 5px;
        }

        .btn-edit:hover {
            background: #ff33bb;
        }

        .top-btn {
            display: inline-block;
            margin-bottom: 15px;
        }
    </style>

</head>
<body>

<h2>Data Pengeluaran</h2>

<a class="top-btn" href="add.php">Tambah</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Tanggal</th>
        <th>Jenis</th>
        <th>Jumlah</th>
        <th>Keterangan</th>
        <th>Aksi</th>
    </tr>

    <?php
    $query = mysqli_query($koneksi, "SELECT * FROM pengeluaran ORDER BY id ASC");

    while($data = mysqli_fetch_array($query)){
    ?>
        <tr>
            <td><?php echo $data['id']; ?></td>
            <td><?php echo $data['tanggal']; ?></td>
            <td><?php echo $data['jenis']; ?></td>
            <td><?php echo $data['jumlah']; ?></td>
            <td><?php echo $data['keterangan']; ?></td>
            <td>
                <a class="btn-edit" href="edit.php?id=<?php echo $data['id']; ?>">Edit</a>
                |
                <a class="btn-delete" onclick="return confirm('Yakin hapus?')" href="delete.php?id=<?php echo $data['id']; ?>">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
