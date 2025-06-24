<?php

namespace App\Http\Controllers\Member;

use App\Core\System\View;
use App\Core\System\Controller;
use App\Models\Member;

class Login extends Controller
{
    public function index()
    {

        $email = 'joendoe@gmail.com';
        $member = Member::where('email', $email)->where('channel', 2)->first();
        $member = $member ? $member : [];

        return View::make('member.login', ['member' => $member]);
    }
}