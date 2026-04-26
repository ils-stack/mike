<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_estate_assessment_cash_flow', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->bigIncrements('id');
            $table->unsignedBigInteger('userid');
            $table->unsignedBigInteger('clientid');
            $table->integer('age');
            $table->integer('term');
            $table->string('capital', 150);
            $table->string('income', 150);
            $table->string('req_budget', 150);
            $table->dateTime('update_date');
            $table->integer('ins_term');
            $table->integer('type');
            $table->double('client_cap', 14, 2);
            $table->double('spouse_cap', 14, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_estate_assessment_cash_flow');
    }
};

