<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $logs = Log::latest()->paginate(50);
        return view('dashboard', compact('logs'));
    }
}
