<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ss_expenses', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('expenseid');
            $table->string('expensename', 255)->default('');
            $table->unsignedSmallInteger('expensestatus')->default(0);
            $table->unsignedSmallInteger('expenseorder')->default(0);
            $table->unsignedSmallInteger('parentid')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ss_expenses');
    }
};

