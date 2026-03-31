<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Stok</title>
</head>
<body>
    <form action="proses_tambah_stok.php" method="POST">
    <label>Pilih Alat:</label>
    <select name="id_alat" required>
        <?php
        include 'koneksi.php';
        $query = mysqli_query($conn, "SELECT id_alat, nama_alat, stok FROM alat");
        while($d = mysqli_fetch_array($query)){
            echo "<option value='$d[id_alat]'>$d[nama_alat] (Sisa: $d[stok])</option>";
        }
        ?>
    </select>
    
    <label>Jumlah Tambah:</label>
    <input type="number" name="jumlah" min="1" required>
    
    <button type="submit">Update Stok</button>
</form>
</body>
</html>