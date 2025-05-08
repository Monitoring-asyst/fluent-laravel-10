<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Metric;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    public function receive(Request $request)
    {
        \Log::info('MetricController@receive called', ['payload' => $request->all()]);
        $payloads = $request->all();

        // Normalize: jadikan array kalau objek tunggal
        if (!is_array($payloads) || isset($payloads['cpu_p']) || isset($payloads['Mem.used'])) {
            $payloads = [$payloads];
        }

        foreach ($request->all() as $fields) {
            $timestamp = isset($fields['date']) ? date('Y-m-d H:i:s', (float)$fields['date']) : now();
            $hostname = $fields['hostname'] ?? 'unknown';

            if (isset($fields['cpu_p'])) {
                Metric::create([
                    'pid' => null,
                    'name' => 'CPU',
                    'cpu_usage' => $fields['cpu_p'],
                    'memory_usage' => null,
                    'host' => $hostname,
                    'type' => 'cpu',
                    'value' => $fields['cpu_p'],
                    'timestamp' => $timestamp,
                    'raw_data' => $fields
                ]);
            } elseif (isset($fields['Mem.used'], $fields['Mem.total'])) {
                $memUsed = $fields['Mem.used'];
                $memTotal = $fields['Mem.total'];
                $memPercent = ($memTotal > 0) ? ($memUsed / $memTotal) * 100 : 0;

                Metric::create([
                    'pid' => null,
                    'name' => 'Memory',
                    'cpu_usage' => null,
                    'memory_usage' => $memPercent,
                    'host' => $hostname,
                    'type' => 'memory',
                    'value' => $memPercent,
                    'timestamp' => $timestamp,
                    'raw_data' => $fields
                ]);
            }
        }

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

    public function latest()
    {
        try {
            $cpu = Metric::where('type', 'cpu')
                ->orderBy('timestamp', 'desc')
                ->first();

            $memory = Metric::where('type', 'memory')
                ->orderBy('timestamp', 'desc')
                ->first();

            $response = [
                'cpu' => null,
                'memory' => null
            ];

            if ($cpu) {
                $response['cpu'] = [
                    'cpu_usage' => (float) $cpu->cpu_usage,
                    'timestamp' => $cpu->timestamp,
                    'raw_data' => $cpu->raw_data
                ];
            }

            if ($memory) {
                $response['memory'] = [
                    'memory_usage' => (float) $memory->memory_usage,
                    'timestamp' => $memory->timestamp,
                    'raw_data' => $memory->raw_data
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error fetching latest metrics: ' . $e->getMessage());
            return response()->json([
                'cpu' => null,
                'memory' => null
            ], 500);
        }
    }
}
