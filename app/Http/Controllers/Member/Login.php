<?php

namespace App\Http\Controllers\Member;

use App\Core\System\View;
use App\Core\System\Controller;

class Login extends Controller
{
    public function index()
    {
        $name = 'Jhon';
        $surname = 'Doe';
        return View::make('member.login', ['name' => $name, 'surname' => $surname]);
    }
}