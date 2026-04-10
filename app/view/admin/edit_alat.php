<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "peminjaman_alat";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil data lama termasuk nama file gambarnya
$sql = "SELECT * FROM alat WHERE alat_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $nama_alat = $_POST['nama_alat'];
    $stok      = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    
    // Logika Ganti Gambar
    $foto_nama = $_FILES['gambar']['name'];
    $foto_tmp  = $_FILES['gambar']['tmp_name'];

    if (!empty($foto_nama)) {
        // Jika user mengunggah foto baru
        $ekstensi_boleh = array('png', 'jpg', 'jpeg');
        $x = explode('.', $foto_nama);
        $ekstensi = strtolower(end($x));
        $nama_file_baru = time() . '-' . $foto_nama;

        if (in_array($ekstensi, $ekstensi_boleh)) {
            // Hapus foto lama dari folder img agar tidak menumpuk (jika ada)
            if ($data['gambar'] && file_exists("img/" . $data['gambar'])) {
                unlink("img/" . $data['gambar']);
            }

            // Pindahkan file baru
            move_uploaded_file($foto_tmp, 'img/' . $nama_file_baru);

            // Update database dengan gambar baru
            $sql_update = "UPDATE alat SET nama_alat=?, deskripsi=?, stok=?, gambar=? WHERE alat_id=?";
            $stmt_edit = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt_edit, "ssisi", $nama_alat, $deskripsi, $stok, $nama_file_baru, $id);
        } else {
            echo "<script>alert('Format gambar harus JPG atau PNG!');</script>";
        }
    } else {
        // Jika user tidak ganti gambar, update data teks saja
        $sql_update = "UPDATE alat SET nama_alat=?, deskripsi=?, stok=? WHERE alat_id=?";
        $stmt_edit = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_edit, "ssii", $nama_alat, $deskripsi, $stok, $id);
    }

    if (isset($stmt_edit) && mysqli_stmt_execute($stmt_edit)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='dashboard.php';</script>";
        exit;
    } else {
        echo "Gagal update: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Alat | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --text-main: #1e293b;
            --text-label: #64748b;
            --white: #ffffff;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #e2e8f0 0%, #f8fafc 100%);
            margin: 0; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .edit-card {
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }

        .header { text-align: center; margin-bottom: 25px; }
        .header h2 { margin: 0; color: var(--text-main); font-size: 22px; }

        .form-group { margin-bottom: 18px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-label); font-size: 13px; text-transform: uppercase; }

        input, textarea { 
            width: 100%; padding: 12px; border: 2px solid #f1f5f9; border-radius: 12px; 
            box-sizing: border-box; font-size: 14px; background: #f8fafc;
        }

        /* Styling khusus Preview Gambar */
        .preview-container {
            background: #f1f5f9;
            padding: 10px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 10px;
            border: 2px dashed #cbd5e1;
        }

        .img-display {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            object-fit: cover;
            display: block;
            margin: 0 auto 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .file-input {
            font-size: 12px;
            background: none;
            border: none;
            padding: 0;
        }

        .btn-save { 
            width: 100%; padding: 14px; background: var(--primary); color: white; 
            border: none; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 16px;
        }

        .btn-back { 
            display: block; text-align: center; margin-top: 15px; color: var(--text-label);
            text-decoration: none; font-size: 14px; font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="edit-card">
        <div class="header">
            <h2>Edit Detail Alat</h2>
            <p style="color: #64748b; font-size: 13px;">Perbarui spesifikasi dan foto unit</p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label>Foto Unit Saat Ini</label>
                <div class="preview-container">
                    <?php if (!empty($data['gambar']) && file_exists("img/" . $data['gambar'])): ?>
                        <img src="img/<?php echo $data['gambar']; ?>" class="img-display" alt="Foto Alat">
                    <?php else: ?>
                        <div style="padding: 20px; color: #94a3b8;">Tidak ada foto</div>
                    <?php endif; ?>
                    <input type="file" name="gambar" class="file-input">
                </div>
                <small style="color: #94a3b8; font-size: 11px;">*Kosongkan jika tidak ingin mengubah foto</small>
            </div>

            <div class="form-group">
                <label>Nama Alat</label>
                <input type="text" name="nama_alat" value="<?php echo htmlspecialchars($data['nama_alat']); ?>" required>
            </div>

            <div class="form-group">
                <label>Jumlah Unit (Stok)</label>
                <input type="number" name="stok" value="<?php echo (int)$data['stok']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Deskripsi Alat</label>
                <textarea name="deskripsi" rows="3" required><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
            </div>

            <button type="submit" name="update" class="btn-save">Simpan Perubahan</button>
            <a href="dashboard.php" class="btn-back">Batal & Kembali</a>
        </form>
    </div>

</body>
</html>