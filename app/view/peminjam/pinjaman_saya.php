<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/peminjaman/app/config/koneksi.php";

// Proteksi akses: Hanya peminjam yang bisa akses
$session_level = isset($_SESSION['level']) ? strtolower(trim($_SESSION['level'])) : '';
if ($session_level !== 'peminjam') {
    header("Location: ../dashboard.php");
    exit();
}

$username = $_SESSION['username'] ?? "User";
$nama_session = $_SESSION['nama'] ?? "Peminjam";

// Query join tabel sesuai struktur database (Pastikan nama tabel 'peminjaman' atau 'peminjam' sesuai DB Anda)
// Di sini saya asumsikan tabel utamanya adalah 'peminjaman'
$query = "SELECT p.*, a.nama_alat 
          FROM peminjaman p 
          JOIN alat a ON p.alat_id = a.alat_id 
          JOIN users u ON p.user_id = u.user_id 
          WHERE u.username = '$username' 
          ORDER BY p.tanggal_pinjam DESC";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjaman Saya - SISTEM ALAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: #2d394b; 
            --active-blue: #3b9df2; 
            --bg-main: #f0f2f5;
            --text-gray: #bdc3c7;
            --logout-red: #ff7675;
            --success: #2ecc71;
            --warning: #f1c40f;
            --danger: #e74c3c;
            --info: #3498db;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', 'Nunito', sans-serif; background-color: var(--bg-main); display: flex; color: #333; min-height: 100vh; }

        /* --- SIDEBAR --- */
        .sidebar { 
            width: 280px; background-color: var(--sidebar-bg); height: 100vh; color: white; 
            position: fixed; display: flex; flex-direction: column; z-index: 1000;
        }
        .sidebar-header { 
            padding: 30px 25px; font-size: 18px; font-weight: bold; display: flex; 
            align-items: center; gap: 12px; background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .sidebar-menu { padding: 20px 15px; flex-grow: 1; }
        .sidebar-menu a { 
            display: flex; align-items: center; color: var(--text-gray); padding: 14px 20px; 
            text-decoration: none; border-radius: 12px; margin-bottom: 8px; 
            font-size: 15px; transition: 0.3s; font-weight: 500;
        }
        .sidebar-menu a .icon { margin-right: 15px; font-size: 18px; width: 25px; text-align: center; }
        .sidebar-menu a:hover { color: white; background: rgba(255,255,255,0.05); transform: translateX(5px); }
        .sidebar-menu a.active { background: var(--active-blue); color: white; box-shadow: 0 4px 15px rgba(59, 157, 242, 0.3); }

        .logout-section { padding: 20px 15px 30px; border-top: 1px solid rgba(255,255,255,0.05); }
        .logout-link { 
            display: flex; align-items: center; justify-content: center; padding: 14px; 
            color: var(--logout-red); text-decoration: none; font-weight: 600; font-size: 15px;
            border: 1.5px dashed rgba(255, 118, 117, 0.3); border-radius: 12px; 
            transition: 0.3s; background: rgba(255, 118, 117, 0.03);
        }
        .logout-link:hover { background: var(--logout-red); color: white; border-style: solid; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 280px; flex: 1; padding: 40px; width: calc(100% - 280px); }
        .top-navbar {
            background: white; padding: 15px 25px; border-radius: 15px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        /* --- TABLE CARD --- */
        .card { background: white; padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: none; }
        .card h2 { margin-bottom: 5px; color: #1e293b; font-weight: 800; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th { 
            text-align: left; padding: 15px; color: #94a3b8; 
            border-bottom: 2px solid #f8fafc; font-size: 12px; 
            text-transform: uppercase; letter-spacing: 1px;
        }
        td { padding: 18px 15px; border-bottom: 1px solid #f8fafc; font-size: 14px; }
        tr:hover td { background-color: #fbfcfe; }

        /* --- STATUS BADGES --- */
        .badge-status { 
            padding: 6px 14px; border-radius: 30px; font-weight: 800; font-size: 11px; 
            text-transform: uppercase; display: inline-block; text-align: center; min-width: 100px;
        }
        .bg-menunggu { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }
        .bg-dipinjam { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
        .bg-kembali { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
        .bg-ditolak { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }

        .denda-text { font-weight: 700; }
        .unit-badge { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><span>🛠️</span> SISTEM ALAT</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
            <a href="daftar_alat_peminjam.php"><span class="icon">🔍</span> Lihat Alat</a>
            <a href="pinjaman_saya.php" class="active"><span class="icon">📦</span> Pinjaman Saya</a>
            <a href="pinjam_alat.php"><span class="icon">🔧</span> Pinjam Alat</a>
            <a href="pengembalian_peminjam.php"><span class="icon">📥</span> Kembalikan Alat</a>
        </div>
        <div class="logout-section">
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
                <span class="icon">🚪</span> Keluar Sistem
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div style="color: #888; font-size: 14px;">Aktivitas / <strong>Riwayat Pinjaman</strong></div>
            <div style="font-size: 14px;">Login sebagai: <strong><?= htmlspecialchars($nama_session); ?></strong></div>
        </div>

        <div class="card">
            <h2>Riwayat Peminjaman Anda 📦</h2>
            <p style="color: #64748b; font-size: 14px;">Pantau status persetujuan dan riwayat pengembalian alat Anda di sini.</p>

            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)): 
                            // Gunakan trim dan strtolower untuk keamanan perbandingan string
                            $status = trim($row['status']);
                            $status_display = ucfirst(strtolower($status));
                            
                            $badge_class = 'bg-menunggu';
                            if (strtolower($status) == 'dipinjam') $badge_class = 'bg-dipinjam';
                            elseif (strtolower($status) == 'kembali') $badge_class = 'bg-kembali';
                            elseif (strtolower($status) == 'ditolak') $badge_class = 'bg-ditolak';
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong style="color: #1e293b;"><?= htmlspecialchars($row['nama_alat']); ?></strong></td>
                        <td style="color: #64748b;"><?= date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                        <td><span class="unit-badge"><?= $row['jumlah']; ?> Unit</span></td>
                       <td>
                            <span class="badge-status <?= $badge_class; ?>">
                                <?php 
                                    // Jika status di DB kosong, paksa tampilkan MENUNGGU
                                    echo (!empty($status)) ? strtoupper($status) : 'MENUNGGU'; 
                                ?>
                            </span>
                        </td>
                        <td>
                            <span class="denda-text <?= ($row['denda'] > 0) ? 'text-danger' : 'text-muted'; ?>">
                                <?= ($row['denda'] > 0) ? 'Rp ' . number_format($row['denda'], 0, ',', '.') : '-'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; } else { ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 60px 0;">
                            <i class="fas fa-box-open" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px;"></i><br>
                            <span style="color: #94a3b8;">Belum ada riwayat peminjaman alat.</span>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>