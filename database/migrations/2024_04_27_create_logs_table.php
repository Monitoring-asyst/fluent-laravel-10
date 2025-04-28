<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('level');
            $table->text('message');
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
}; 