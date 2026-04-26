<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderIdToSourceFileMetadataTable extends Migration
{
    public function up()
    {
        Schema::table('source_file_metadata', function (Blueprint $table) {
            $table->bigInteger('provider_id')->nullable()->after('path_hash');
        });
    }

    public function down()
    {
        Schema::table('source_file_metadata', function (Blueprint $table) {
            $table->dropColumn('provider_id');
        });
    }
}
