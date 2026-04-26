<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDumperTable extends Migration
{
    public function up()
    {
        Schema::create('dumper', function (Blueprint $table) {
            for ($index = 1; $index <= 45; $index++) {
                $table->string('row'.$index, 150);
            }

            $table->id();
            $table->string('csv_name', 150);
            $table->unsignedBigInteger('brokerage_id');
            $table->string('month', 55);
            $table->string('year', 55);
            $table->integer('import_type');
            $table->string('alt_name', 150);
            $table->date('updated_at');

            $table->index('csv_name');
            $table->index('row2');
            $table->index('row1');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dumper');
    }
}
