<?php

class Alat {
    private $table = 'alat';
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllAlat() {
        // Query JOIN untuk mendapatkan nama kategori
        $query = "SELECT alat.*, kategori.nama_kategori 
                  FROM alat 
                  JOIN kategori ON alat.id_kategori = kategori.id_kategori";
        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function updateStok($id, $jumlah) {
        $this->db->query("UPDATE alat SET stok = stok + :jml WHERE id_alat = :id");
        $this->db->bind('jml', $jumlah);
        $this->db->bind('id', $id);
        $this->db->execute();
    }
}