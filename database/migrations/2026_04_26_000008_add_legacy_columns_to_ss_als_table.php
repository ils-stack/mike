<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ss_als', function (Blueprint $table) {
            if (! Schema::hasColumn('ss_als', 'alstatus')) {
                $table->unsignedTinyInteger('alstatus')->default(1)->after('alname');
            }

            if (! Schema::hasColumn('ss_als', 'parentid')) {
                $table->unsignedInteger('parentid')->default(0)->after('alorder');
            }

            if (! Schema::hasColumn('ss_als', 'shortterm')) {
                $table->unsignedTinyInteger('shortterm')->default(0)->after('parentid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ss_als', function (Blueprint $table) {
            foreach (['shortterm', 'parentid', 'alstatus'] as $column) {
                if (Schema::hasColumn('ss_als', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
