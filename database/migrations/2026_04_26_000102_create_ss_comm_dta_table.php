<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSsCommDtaTable extends Migration
{
    public function up()
    {
        Schema::create('ss_comm_dta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_id');
            $table->double('amt', 12, 2);
            $table->date('comm_dt');
            $table->string('broker_name', 150);
            $table->string('brokerage', 150);
            $table->string('csv_name', 150);
            $table->integer('dumper_id');
            $table->integer('import_type');
            $table->string('percent', 150);
            $table->boolean('active')->default(true);

            $table->index('broker_name');
            $table->index('csv_name');
            $table->index('import_type');
            $table->index('comm_dt');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ss_comm_dta');
    }
}
