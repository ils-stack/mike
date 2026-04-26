<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ss_tax_table', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->bigIncrements('id');
            $table->integer('tax_year');
            $table->double('amt_start_range', 10, 2);
            $table->double('amt_end_range', 10, 2);
            $table->double('fixed_tax', 10, 2);
            $table->double('per_tax', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ss_tax_table');
    }
};

