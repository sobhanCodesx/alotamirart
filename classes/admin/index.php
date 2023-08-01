<?php

class Dashboard
{
    function __construct()
    {
        $Admin = new Auth();
        $Admin->checkAdmin();
    }
    public function index()
    {
    
        require_once BASE_PATH.'/them/admin/index.php';
    }
    
}