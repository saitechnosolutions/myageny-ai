<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MastersController extends Controller
{
    /**
     * Display the masters index page
     */
    public function index()
    {
        return view('pages.masters.index');
    }
}
