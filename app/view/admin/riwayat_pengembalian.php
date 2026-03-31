<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "peminjaman_alat");

$query = "SELECT p.*, u.nama_lengkap, a.nama_alat 
          FROM peminjaman p
          JOIN users u ON p.user_id = u.user_id 
          JOIN alat a ON p.alat_id = a.alat_id 
          WHERE p.status = 'Kembali' 
          ORDER BY p.tanggal_kembali_asli DESC";
$result = mysqli_query($conn, $query);
?>

<table>
    <thead>
        <tr>
            <th>Peminjam</th>
            <th>Alat</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?= $row['nama_lengkap']; ?></td>
            <td><?= $row['nama_alat']; ?></td>
            <td><?= $row['tanggal_pinjam']; ?></td>
            <td><strong><?= $row['tanggal_kembali_asli']; ?></strong></td>
            <td><span style="color:green; font-weight:bold;">Selesai</span></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>