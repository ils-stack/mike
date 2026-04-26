<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeSourceFileMetadataProviderNullable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE source_file_metadata MODIFY provider_id BIGINT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE source_file_metadata MODIFY provider_id BIGINT NOT NULL');
    }
}
