<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number', 20)->nullable(); 
            $table->string('customer_name', 64)->nullable(); // ★最大64文字

            $table->string('phone_number', 20)->nullable();

            $table->string('location_from_1', 255)->nullable();
            $table->decimal('lat_1', 10, 7)->nullable(); 
            $table->decimal('lng_1', 10, 7)->nullable(); 
            
            $table->string('location_from_2', 255)->nullable();
            $table->decimal('lat_2', 10, 7)->nullable();
            $table->decimal('lng_2', 10, 7)->nullable();
            
            $table->string('location_from_3', 255)->nullable();
            $table->decimal('lat_3', 10, 7)->nullable();
            $table->decimal('lng_3', 10, 7)->nullable();

            $table->string('location_to', 128)->nullable();  // ★最大128文字 (配車先)
            $table->string('call_area', 128)->nullable();    // ★最大128文字 (呼び出し)
            $table->string('guidance', 512)->nullable();     // ★最大512文字 (誘導先)
            $table->string('primary_info', 256)->nullable(); // ★最大256文字 (Primary)

            $table->dateTime('dispatch_time')->nullable();
            $table->dateTime('completion_time')->nullable();
            $table->string('status')->default('未手配');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dispatches');
    }
};