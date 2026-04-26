<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrokerMasterTable extends Migration
{
    public function up()
    {
        Schema::create('broker_master', function (Blueprint $table) {
            $table->id();
            $table->string('broker_name', 150);
            $table->string('email', 150);
            $table->string('broker_split', 75);
            $table->string('broker_code', 75);
            $table->double('broker_factor_1', 4, 2);
            $table->double('broker_factor_2', 4, 2);
            $table->double('broker_factor_3', 4, 2);
            $table->double('broker_factor_4', 4, 2);
            $table->integer('active');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('type');
            $table->integer('consultant_id');
            $table->integer('vetted');
            $table->unsignedBigInteger('assoc_id');
            $table->string('remarks', 70);
            $table->unsignedBigInteger('parent_id');
            $table->string('percent', 150);

            $table->index('broker_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('broker_master');
    }
}
