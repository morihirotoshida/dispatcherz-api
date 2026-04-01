<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DispatchController;
// 上のほうの use 宣言が並んでいるところに以下を追加
use App\Http\Controllers\SettingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// ★これを追記します（CSVインポート用の窓口）
Route::post('/dispatches/import', [DispatchController::class, 'importCsv']);
Route::get('/dispatches', [DispatchController::class, 'index']);

Route::post('/dispatches', [DispatchController::class, 'store']);

Route::put('/dispatches/{id}', [DispatchController::class, 'update']);

// 下のほうの Route::... が並んでいるところに以下の3行を追加
Route::delete('/dispatches/{id}', [DispatchController::class, 'destroy']); // 完全削除用
Route::post('/verify-pin', [SettingController::class, 'verifyPin']);       // PIN確認用
Route::post('/update-pin', [SettingController::class, 'updatePin']);       // PIN変更用

// ★これを追記します
Route::get('/customers/search', [DispatchController::class, 'searchCustomer']);