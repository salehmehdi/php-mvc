<?php

use App\Core\System\Route;

Route::get('/', ['as'   => 'Home','uses' => 'Home@index']);
Route::get('/login', ['as'   => 'Login','uses' => 'Member\Login@index']);