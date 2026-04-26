<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSsProductsTable extends Migration
{
    public function up()
    {
        Schema::create('ss_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 150);
            $table->integer('active');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->integer('nosplit')->default(0)->comment('apply broker split to total');
            $table->integer('comm_summ');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ss_products');
    }
}
