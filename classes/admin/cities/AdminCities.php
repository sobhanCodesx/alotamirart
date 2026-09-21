<?php

class AdminCities extends Admin
{
    private function unavailable()
    {
        flash('error', 'ماژول قدیمی مدیریت شهر در این نسخه پنل فعال نیست؛ مسیر حفظ شده تا لینک‌های قدیمی خطا ندهند.');
        $this->redirect('admin/dashboard');
    }

    public function index($id, $page)
    {
        $this->unavailable();
    }

    public function create($req, $id)
    {
        $this->unavailable();
    }

    public function updated($req, $id)
    {
        $this->unavailable();
    }

    public function deletd($id, $provi)
    {
        $this->unavailable();
    }
}
