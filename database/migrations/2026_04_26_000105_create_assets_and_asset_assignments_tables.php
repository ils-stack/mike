<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('folder', 100)->index();
                $table->string('file_name');
                $table->string('file_type', 150)->nullable();
                $table->string('file_path');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('asset_assignments')) {
            Schema::create('asset_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('asset_id')->index();
                $table->string('module_type', 100)->index();
                $table->unsignedBigInteger('module_id')->index();
                $table->unsignedInteger('sort_order')->nullable();

                $table->unique(['asset_id', 'module_type', 'module_id'], 'asset_assignments_asset_module_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
    }
};
