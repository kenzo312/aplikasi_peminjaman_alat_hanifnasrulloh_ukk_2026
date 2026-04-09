<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Peminjaman Alat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #5541e1; --bg: #f8fafc; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        
        .login-card {
            background: #fff; width: 100%; max-width: 420px; padding: 50px;
            border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }

        .header { text-align: center; margin-bottom: 35px; }
        .header h2 { color: #1e293b; font-size: 1.8rem; font-weight: 700; margin-bottom: 8px; }
        .header p { color: #64748b; font-size: 1rem; }

        .alert {
            background: #fff1f2; color: #e11d48; padding: 12px;
            border-radius: 12px; margin-bottom: 25px; font-size: 0.9rem; 
            text-align: center; border: 1px solid #fecdd3;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }

        .input-group { margin-bottom: 20px; position: relative; }
        .input-group i { position: absolute; left: 18px; top: 16px; color: #94a3b8; font-size: 1.1rem; }
        .input-group input {
            width: 100%; padding: 14px 15px 14px 50px;
            border: 1px solid #e2e8f0; border-radius: 12px; outline: none;
            transition: 0.3s; font-size: 1rem; color: #334155;
        }
        .input-group input:focus { border-color: var(--primary); }

        .btn-login {
            width: 100%; padding: 15px; background: var(--primary);
            color: white; border: none; border-radius: 12px;
            font-weight: 600; font-size: 1rem; cursor: pointer; 
            transition: 0.3s; margin-top: 10px;
        }
        .btn-login:hover { opacity: 0.9; }

        .footer { text-align: center; margin-top: 25px; font-size: 0.9rem; color: #64748b; }
        .footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="header">
        <h2>Welcome Back</h2>
        <p>Login ke akun Anda</p>
    </div>

    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == "gagal"): ?>
        <div class="alert">
            <i class="fas fa-exclamation-circle"></i> Username atau Password salah!
        </div>
    <?php endif; ?>

    <form action="proses_login.php" method="POST">
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required autocomplete="off" autofocus>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="login" class="btn-login">Masuk</button>
    </form>

    <div class="footer">
        Belum punya akun? <a href="register.php">Daftar sekarang</a>
    </div>
</div>

</body>
</html>