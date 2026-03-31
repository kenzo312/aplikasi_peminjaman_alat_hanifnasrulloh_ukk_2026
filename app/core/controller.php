<?php

class Controller {
    /**
     * Method untuk memanggil View
     * @param string $view Nama file view (contoh: 'admin/index')
     * @param array $data Data yang akan dikirim ke view
     */
    public function view($view, $data = []) {
        // Mengecek apakah file view ada
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die("View $view tidak ditemukan!");
        }
    }

    /**
     * Method untuk memanggil Model
     * @param string $model Nama class model (contoh: 'Alat')
     * @return object Instance dari model tersebut
     */
    public function model($model) {
        // Mengecek apakah file model ada
        if (file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
            return new $model;
        } else {
            die("Model $model tidak ditemukan!");
        }
    }
}