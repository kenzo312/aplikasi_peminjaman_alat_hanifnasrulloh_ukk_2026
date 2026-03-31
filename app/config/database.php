<?php

class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db_name = "peminjaman_alat";

    protected $dbh;
    protected $stmt;

    public function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;

        $option = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $option);
        } catch (PDOException $e) {
            die("Koneksi Gagal: " . $e->getMessage());
        }
    }
}