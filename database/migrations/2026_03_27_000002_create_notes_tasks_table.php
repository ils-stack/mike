<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('advisor_id')->nullable()->index();
            $table->unsignedBigInteger('investor_id')->nullable()->index();
            $table->foreignId('list_id')->default(1)->constrained('notes_task_lists');
            $table->boolean('is_deleted')->default(false)->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes_tasks');
    }
};
