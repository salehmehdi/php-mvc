<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Member extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'members';

    protected $guarded  = [];
}