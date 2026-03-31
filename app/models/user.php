<?php

class User {
    private $table = 'users';
    private $db;

    public function __construct() {
        // Database.php otomatis dipanggil melalui core/App.php atau require
        $this->db = new Database(); 
    }

    public function getAllUser() {
        $this->db->query("SELECT * FROM " . $this->table);
        return $this->db->resultSet();
    }

    public function getUserById($id) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE id_user=:id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function getUserByUsername($username) {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind('username', $username);
        return $this->db->single();
    }
}