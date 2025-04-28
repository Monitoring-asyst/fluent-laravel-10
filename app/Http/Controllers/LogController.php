<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function receive(Request $request)
    {
        $rawText = $request->getContent();

        // Coba decode sebagai JSON array
        $jsonData = json_decode($rawText, true);

        if (is_array($jsonData)) {
            // Jika array, loop per item
            foreach ($jsonData as $item) {
                // Ambil field log (atau sesuaikan dengan struktur data kamu)
                $logLine = $item['log'] ?? json_encode($item);

                if (preg_match('/^(INFO|ERROR|WARN|DEBUG):\s+(.*)$/', $logLine, $matches)) {
                    $level = $matches[1];
                    $message = $matches[2];
                } else {
                    $level = 'INFO';
                    $message = $logLine;
                }

                Log::create([
                    'level' => $level,
                    'message' => $message,
                    'raw_data' => $item
                ]);
            }
        } else {
            // Jika bukan array, fallback ke mode per baris (misal: text biasa)
            $lines = explode("\n", trim($rawText));
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                if (preg_match('/^(INFO|ERROR|WARN|DEBUG):\s+(.*)$/', $line, $matches)) {
                    $level = $matches[1];
                    $message = $matches[2];
                } else {
                    $level = 'INFO';
                    $message = $line;
                }

                Log::create([
                    'level' => $level,
                    'message' => $message,
                    'raw_data' => ['original' => $line]
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function show($id)
    {
        $log = Log::findOrFail($id);
        return response()->json($log);
    }
}
