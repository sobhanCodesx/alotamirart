<?php

class Profile
{
    public function index($iUserId,$iUserName)
    {
        $db = new DataBase();
        require_once BASE_PATH . "/them/app/profile/index.php";
    }

}