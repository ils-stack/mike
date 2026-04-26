<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spouse_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investor_id')->nullable()->index();
            $table->string('client_number')->nullable()->index();
            $table->string('marital_status')->nullable();
            $table->string('entity')->nullable();
            $table->string('surname')->nullable();
            $table->string('first_name')->nullable();
            $table->string('id_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('physical_address')->nullable();
            $table->text('postal_address')->nullable();
            $table->string('cellular')->nullable();
            $table->string('home_tel')->nullable();
            $table->string('work_tel')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spouse_details');
    }
};
