<?php

class HomeController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $view = '../app/Views/home.php';
        require '../app/Views/layouts/layout.php';
    }
}
