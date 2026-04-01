<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // ★追加：テーブルの存在確認に必要

class SettingController extends Controller
{
    public function verifyPin(Request $request)
    {
        // ① 万が一テーブル自体が存在しない場合の超安全対策（強制突破）
        if (!Schema::hasTable('system_settings')) {
            if ($request->pin === '9999') return response()->json(['success' => true]);
            return response()->json(['success' => false, 'message' => '暗証番号が違います'], 401);
        }

        $pin = DB::table('system_settings')->where('key', 'admin_pin')->value('value');
        
        // ② 万が一データが空っぽなら、ここで「9999」を自動生成してあげる！
        if (empty($pin)) {
            DB::table('system_settings')->insert([
                'key' => 'admin_pin',
                'value' => '9999',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $pin = '9999';
        }

        // ③ 判定
        if ($pin == $request->pin) {
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => '暗証番号が違います'], 401);
    }

    public function updatePin(Request $request)
    {
        if (!Schema::hasTable('system_settings')) {
            return response()->json(['success' => false, 'message' => 'テーブルがありません'], 500);
        }

        $currentPin = DB::table('system_settings')->where('key', 'admin_pin')->value('value');
        if (empty($currentPin)) {
            $currentPin = '9999';
        }
        
        if ($currentPin == $request->current_pin) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'admin_pin'],
                ['value' => $request->new_pin, 'updated_at' => now()]
            );
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => '現在の暗証番号が違います'], 401);
    }
}