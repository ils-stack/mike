<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ss_als', function (Blueprint $table) {
            $table->increments('alid');
            $table->string('alname', 255)->default('');
            $table->unsignedTinyInteger('alstatus')->default(1);
            $table->unsignedInteger('alorder')->default(0);
            $table->unsignedInteger('parentid')->default(0);
            $table->unsignedTinyInteger('shortterm')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ss_als');
    }
};
