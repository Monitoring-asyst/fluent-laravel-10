<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Metric;
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
        $cpuMetric = Metric::where('type', 'cpu')->latest('timestamp')->first();
        $memMetric = Metric::where('type', 'memory')->latest('timestamp')->first();
        $metrics = collect([$cpuMetric, $memMetric])->filter();
        return view('dashboard', compact('logs', 'metrics'));
    }
}
