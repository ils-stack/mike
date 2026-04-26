<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourceAltNamesTable extends Migration
{
    public function up()
    {
        Schema::create('source_alt_names', function (Blueprint $table) {
            $table->id();
            $table->string('alt_name', 255)->unique();
            $table->string('broker_name', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('source_alt_names');
    }
}
