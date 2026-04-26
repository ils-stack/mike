<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourceFileImportTables extends Migration
{
    public function up()
    {
        Schema::create('source_file_import_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_file_metadata_id');
            $table->string('status', 50)->default('imported');
            $table->timestamp('imported_at')->nullable();
            $table->integer('row_count')->default(0);
            $table->integer('column_count')->default(0);
            $table->timestamps();
        });

        Schema::create('source_file_import_columns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->integer('column_index');
            $table->string('original_name', 255)->nullable();
            $table->string('display_name', 255)->nullable();
            $table->boolean('ignored')->default(false);
            $table->string('mapped_field', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('source_file_import_rows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->integer('csv_row_number');
            $table->json('row_data');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('source_file_import_rows');
        Schema::dropIfExists('source_file_import_columns');
        Schema::dropIfExists('source_file_import_batches');
    }
}
