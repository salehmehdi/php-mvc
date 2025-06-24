<?php

namespace App\Http\Controllers\Member;

use App\Core\System\View;

class Login 
{
    public function index()
    {
        $name = 'Jhon';
        $surname = 'Doe';
        return View::make('member.login', ['name' => $name, 'surname' => $surname]);
    }
}