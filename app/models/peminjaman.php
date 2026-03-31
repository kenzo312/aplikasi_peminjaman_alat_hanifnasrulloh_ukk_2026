<?php

class Peminjaman {
    private $table = 'peminjaman';
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function tambahDataPeminjaman($data) {
        $query = "INSERT INTO peminjaman (id_user, id_alat, tgl_pinjam, tgl_kembali, status) 
                  VALUES (:id_user, :id_alat, :tgl_pinjam, :tgl_kembali, 'dipinjam')";
        
        $this->db->query($query);
        $this->db->bind('id_user', $data['id_user']);
        $this->db->bind('id_alat', $data['id_alat']);
        $this->db->bind('tgl_pinjam', $data['tgl_pinjam']);
        $this->db->bind('tgl_kembali', $data['tgl_kembali']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }
}