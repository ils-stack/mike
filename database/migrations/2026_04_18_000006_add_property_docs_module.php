<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('modules')) {
            return;
        }

        $values = ['module_name' => 'Property Docs'];

        if (Schema::hasColumn('modules', 'updated_at')) {
            $values['updated_at'] = now();
        }

        if (Schema::hasColumn('modules', 'created_at')) {
            $values['created_at'] = now();
        }

        DB::table('modules')->updateOrInsert(['module_key' => 'property_docs'], $values);
    }

    public function down(): void
    {
        if (! Schema::hasTable('modules')) {
            return;
        }

        DB::table('modules')->where('module_key', 'property_docs')->delete();
    }
};
