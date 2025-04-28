<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Metric;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    public function receive(Request $request)
    {
        $data = $request->all();

        // Extract metrics data
        $pid = $data['pid'] ?? null;
        $name = $data['name'] ?? 'Unknown';
        $cpuUsage = $data['cpu_usage'] ?? 0;
        $memoryUsage = $data['memory_usage'] ?? 0;

        // Store the metric
        Metric::create([
            'pid' => $pid,
            'name' => $name,
            'cpu_usage' => $cpuUsage,
            'memory_usage' => $memoryUsage,
            'raw_data' => $data
        ]);

        return response()->json(['status' => 'success']);
    }

    public function show($id)
    {
        $metric = Metric::findOrFail($id);
        return response()->json($metric);
    }

    public function cpu(Request $request)
    {
        $payloads = $request->all();
        foreach ($payloads as $data) {
            $cpuUsage = $data['cpu_p'] ?? $data['cpu_usage'] ?? $data['cpu_p_idle'] ?? 0;
            $timestamp = $data['timestamp'] ?? now();
            Log::info('CPU Dashboard', [
                'cpu_usage' => $cpuUsage,
                'timestamp' => $timestamp
            ]);
            $metric = Metric::updateOrCreate(
                ['type' => 'cpu'],
                [
                    'pid' => $data['pid'] ?? null,
                    'name' => $data['name'] ?? 'CPU',
                    'cpu_usage' => $cpuUsage,
                    'memory_usage' => null,
                    'host' => $data['host'] ?? 'unknown',
                    'type' => 'cpu',
                    'value' => $cpuUsage,
                    'timestamp' => $timestamp,
                    'raw_data' => $data
                ]
            );
        }
        return response()->json(['status' => 'success'], 201);
    }

    public function mem(Request $request)
    {
        $payloads = $request->all();
        foreach ($payloads as $data) {
            $memUsed = $data['Mem.used'] ?? null;
            $memTotal = $data['Mem.total'] ?? null;
            $memPercent = isset($data['mem_p']) ? $data['mem_p'] : (isset($memUsed, $memTotal) && $memTotal > 0 ? ($memUsed / $memTotal) * 100 : 0);
            $timestamp = $data['timestamp'] ?? now();
            Log::info('MEM Dashboard', [
                'memory_usage' => $memPercent,
                'timestamp' => $timestamp
            ]);
            $metric = Metric::updateOrCreate(
                ['type' => 'memory'],
                [
                    'pid' => $data['pid'] ?? null,
                    'name' => $data['name'] ?? 'Memory',
                    'cpu_usage' => null,
                    'memory_usage' => $memPercent,
                    'host' => $data['host'] ?? 'unknown',
                    'type' => 'memory',
                    'value' => $memPercent,
                    'timestamp' => $timestamp,
                    'raw_data' => $data
                ]
            );
        }
        return response()->json(['status' => 'success'], 201);
    }
}
