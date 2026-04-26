<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDumperRowAssignTable extends Migration
{
    public function up()
    {
        Schema::create('dumper_row_assign', function (Blueprint $table) {
            $table->id();
            $table->integer('col_type');
            $table->integer('sel_col');
            $table->string('csv_name', 150);
        });
    }

    public function down()
    {
        Schema::dropIfExists('dumper_row_assign');
    }
}
