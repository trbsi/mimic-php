<?php

namespace App\Api\V2\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V2\User\Models\User;

class BaseAuthController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->authUser = $this->user->getAuthenticatedUser();
    }
}
