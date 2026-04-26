<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes_task_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::table('notes_task_lists')->insert([
            [
                'id' => 1,
                'name' => 'To Do',
                'slug' => 'todo',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Progress',
                'slug' => 'progress',
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Done',
                'slug' => 'done',
                'created_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes_task_lists');
    }
};
