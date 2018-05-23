<?php

namespace App\Models;

use App\Api\V2\User\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreUser extends User
{
    use SoftDeletes;
}
