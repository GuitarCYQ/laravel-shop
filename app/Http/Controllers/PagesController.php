<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    //
    public function root()
    {
        // resources/views/pages/root.blade.php
        return view('pages.root');
    }
}
