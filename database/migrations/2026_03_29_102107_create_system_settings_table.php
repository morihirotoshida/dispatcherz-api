<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ★DBファサードを追加

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'admin_pin' などのキー名
            $table->string('value');         // '9999' などの値
            $table->timestamps();
        });

        // ★テーブルを作ると同時に、初期の暗証番号「9999」を登録しておく魔法！
        DB::table('system_settings')->insert([
            'key' => 'admin_pin',
            'value' => '9999',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};