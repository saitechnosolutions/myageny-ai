<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashborad');
});

Route::get('/dashboard', function () {
    return view('pages.dashborad');
});

Route::get('/lead', function () {
    return view('pages.lead');
});
