<?php 
session_start();

// 1. Hapus semua variabel session
$_SESSION = array();

// 2. Jika ingin menghapus cookie session juga (Opsional tapi lebih aman)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hancurkan session di server
session_destroy();

// 4. Pastikan browser tidak menyimpan cache halaman ini
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 5. Tendang ke halaman login
header("location:login.php?pesan=logout");
exit();
?>