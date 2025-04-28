<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    protected $fillable = [
        'pid',
        'name',
        'cpu_usage',
        'memory_usage',
        'host',
        'type',
        'value',
        'timestamp',
        'raw_data'
    ];    

    protected $casts = [
        'raw_data' => 'array',
        'cpu_usage' => 'float',
        'memory_usage' => 'float'
    ];
} 