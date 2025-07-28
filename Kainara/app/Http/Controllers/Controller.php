<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // <--- Crucial line

class Controller extends BaseController // <--- Crucial line
{
    use AuthorizesRequests, ValidatesRequests;
}