<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnlineStoreController extends Controller
{
        public function index()
    {
        return view('onlinestore.index');
    }
}
