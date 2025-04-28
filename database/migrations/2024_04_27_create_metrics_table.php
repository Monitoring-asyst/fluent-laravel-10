<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->integer('pid')->nullable();
            $table->string('name')->nullable();
            $table->float('cpu_usage')->nullable();
            $table->float('memory_usage')->nullable();
            $table->string('host')->nullable();
            $table->string('type')->nullable();
            $table->float('value')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('metrics');
    }
}; 