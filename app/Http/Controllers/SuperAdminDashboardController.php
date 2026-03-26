<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
     public function index(Request $request)
    {


        $user = $request->user();


        $user->tokens()->where('name', 'dashboard-session')->delete();
        $token = $user->createToken('dashboard-session')->plainTextToken;

        return view('pages.dashboard.leads.admin-dashboard', [
            'apiToken'  => $token,
            'apiBase'   => url('/api'),
            'leadBase'  => url('/leads'),
            'userName'  => $user->name,
            'userRole'  => $user->roles->first()?->display_name ?? 'Admin',
            'today'     => now()->format('D, d M Y'),
        ]);
    }
}
