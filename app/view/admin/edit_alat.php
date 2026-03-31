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

    $sql_update = "UPDATE alat SET nama_alat=?, deskripsi=?, stok=? WHERE alat_id=?";
    $stmt_edit = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_edit, "ssii", $nama_alat, $deskripsi, $stok, $id);

    if (mysqli_stmt_execute($stmt_edit)) {
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
            --bg: #f8fafc;
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
        }

        .edit-card {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s ease;
        }

        .edit-card:hover {
            transform: translateY(-5px);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 { 
            margin: 0; 
            color: var(--text-main); 
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header p {
            color: var(--text-label);
            font-size: 14px;
            margin-top: 8px;
        }

        .form-group { 
            margin-bottom: 20px; 
        }

        label { 
            display: block; 
            font-weight: 600; 
            margin-bottom: 8px; 
            color: var(--text-label);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input, textarea { 
            padding: 12px 16px; 
            width: 100%; 
            box-sizing: border-box;
            border: 2px solid #f1f5f9; 
            border-radius: 12px; 
            font-size: 15px;
            background-color: #f8fafc;
            color: var(--text-main);
            transition: all 0.2s ease;
        }

        input:focus, textarea:focus {
            border-color: var(--primary);
            background-color: var(--white);
            outline: none;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 30px;
        }

        .btn-save { 
            width: 100%;
            padding: 14px; 
            background: var(--primary); 
            color: white; 
            border: none; 
            border-radius: 12px; 
            cursor: pointer; 
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .btn-save:hover { 
            background: var(--primary-hover);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .btn-back { 
            text-align: center;
            padding: 12px;
            background: transparent;
            color: var(--text-label);
            text-decoration: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            border: 2px solid #f1f5f9;
        }

        .btn-back:hover { 
            background: #f1f5f9;
            color: var(--text-main);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .edit-card {
                padding: 25px;
                margin: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="edit-card">
        <div class="header">
            <h2>Update Data Alat</h2>
            <p>Kelola informasi stok dan deskripsi alat</p>
        </div>

        <form action="" method="POST">
            <div class="form-group">
                <label>Nama Alat</label>
                <input type="text" name="nama_alat" value="<?php echo htmlspecialchars($data['nama_alat']); ?>" placeholder="Contoh: PC Gaming" required>
            </div>

            <div class="form-group">
                <label>Jumlah Unit (Stok)</label>
                <input type="number" name="stok" value="<?php echo (int)$data['stok']; ?>" placeholder="0" required>
            </div>
            
            <div class="form-group">
                <label>Deskripsi Detail</label>
                <textarea name="deskripsi" rows="4" placeholder="Masukkan spesifikasi alat..." required><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
            </div>

            <div class="btn-container">
                <button type="submit" name="update" class="btn-save">Simpan Perubahan</button>
                <a href="dashboard.php" class="btn-back">Batal & Kembali</a>
            </div>
        </form>
    </div>

</body>
</html>