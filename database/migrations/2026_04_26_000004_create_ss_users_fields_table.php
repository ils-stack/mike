<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ss_users_fields', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->increments('ufid');
            $table->unsignedInteger('userid')->default(0);
            $table->unsignedInteger('typeid')->default(0);
            $table->string('field', 512)->default('');
            $table->text('value')->nullable();

            $table->index('userid');
            $table->index('typeid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ss_users_fields');
    }
};

