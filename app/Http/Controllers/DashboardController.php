<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            return view('dashboard');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Dashboard Error: ' . $e->getMessage());
            
            // Return a safe fallback view
            return view('dashboard')
                ->with('error', 'There was an issue loading your dashboard. Please try again.');
        }
    }
}
