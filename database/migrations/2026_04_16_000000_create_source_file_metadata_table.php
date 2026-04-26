<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourceFileMetadataTable extends Migration
{
    public function up()
    {
        Schema::create('source_file_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('path', 1000);
            $table->char('path_hash', 64)->unique();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->integer('processed_status')->default(0);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('source_file_metadata');
    }
}
