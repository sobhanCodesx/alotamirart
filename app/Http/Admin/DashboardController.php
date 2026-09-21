<?php
namespace App\Http\Admin;

class DashboardController extends AdminController
{
    public function index()
    {
        return $this->render('them/admin/index.php');
    }
}
