<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ss_incomes', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('incomeid');
            $table->string('incomename', 255)->default('');
            $table->unsignedSmallInteger('incomestatus')->default(0);
            $table->unsignedSmallInteger('incomeorder')->default(0);
            $table->unsignedSmallInteger('parentid')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ss_incomes');
    }
};

