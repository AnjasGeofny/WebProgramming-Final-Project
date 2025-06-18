<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Field;

class HomeController extends Controller
{
    public function index()
    {
        return view('user.home');
    }
}
