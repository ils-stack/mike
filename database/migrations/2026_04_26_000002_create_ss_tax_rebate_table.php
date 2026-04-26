<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ss_tax_rebate', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->bigIncrements('id');
            $table->integer('tax_year');
            $table->integer('age_limit');
            $table->integer('age_limit_higher');
            $table->double('rebate', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ss_tax_rebate');
    }
};

