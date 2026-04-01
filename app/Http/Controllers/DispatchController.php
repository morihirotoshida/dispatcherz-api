<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    // ★変更後： 送られてきた日付（start と end）でデータを絞り込む！
    public function index(Request $request)
    {
        $query = Dispatch::query();

        // もしFlutterから「期間」が指定されていれば、配車日時（dispatch_time）で絞り込む
        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('dispatch_time', [
                $request->start . ' 00:00:00', 
                $request->end . ' 23:59:59'
            ]);
        }

        // 常に配車日時の「古い順（asc）」または「新しい順（desc）」で返す
        return response()->json($query->orderBy('dispatch_time', 'asc')->get());
    }

    public function store(Request $request)
    {
        $dispatch = new Dispatch();
        // ★新仕様のデータをすべて受け取る
        $dispatch->customer_number = $request->customer_number;
        $dispatch->customer_name = $request->customer_name;
        $dispatch->phone_number = $request->phone_number;
        
        $dispatch->location_from_1 = $request->location_from_1;
        $dispatch->lat_1 = $request->lat_1;
        $dispatch->lng_1 = $request->lng_1;
        
        $dispatch->location_from_2 = $request->location_from_2;
        $dispatch->lat_2 = $request->lat_2;
        $dispatch->lng_2 = $request->lng_2;
        
        $dispatch->location_from_3 = $request->location_from_3;
        $dispatch->lat_3 = $request->lat_3;
        $dispatch->lng_3 = $request->lng_3;
        
        $dispatch->location_to = $request->location_to;
        $dispatch->call_area = $request->call_area;
        $dispatch->guidance = $request->guidance;
        $dispatch->primary_info = $request->primary_info;
        
        $dispatch->dispatch_time = $request->dispatch_time;
        $dispatch->completion_time = $request->completion_time;
        $dispatch->status = $request->status ?? '未手配';
        
        $dispatch->save();

        return response()->json($dispatch, 201);
    }

    public function update(Request $request, $id)
    {
        $dispatch = Dispatch::find($id);
        if (!$dispatch) {
            return response()->json(['message' => '見つかりません'], 404);
        }

        // 存在するデータのみ上書き（キャンセルの時などは一部だけ送られてくるため）
        if ($request->has('customer_number')) $dispatch->customer_number = $request->customer_number;
        if ($request->has('customer_name')) $dispatch->customer_name = $request->customer_name;
        if ($request->has('phone_number')) $dispatch->phone_number = $request->phone_number;
        
        if ($request->has('location_from_1')) $dispatch->location_from_1 = $request->location_from_1;
        if ($request->has('lat_1')) $dispatch->lat_1 = $request->lat_1;
        if ($request->has('lng_1')) $dispatch->lng_1 = $request->lng_1;
        
        if ($request->has('location_from_2')) $dispatch->location_from_2 = $request->location_from_2;
        if ($request->has('lat_2')) $dispatch->lat_2 = $request->lat_2;
        if ($request->has('lng_2')) $dispatch->lng_2 = $request->lng_2;
        
        if ($request->has('location_from_3')) $dispatch->location_from_3 = $request->location_from_3;
        if ($request->has('lat_3')) $dispatch->lat_3 = $request->lat_3;
        if ($request->has('lng_3')) $dispatch->lng_3 = $request->lng_3;
        
        if ($request->has('location_to')) $dispatch->location_to = $request->location_to;
        if ($request->has('call_area')) $dispatch->call_area = $request->call_area;
        if ($request->has('guidance')) $dispatch->guidance = $request->guidance;
        if ($request->has('primary_info')) $dispatch->primary_info = $request->primary_info;
        
        if ($request->has('dispatch_time')) $dispatch->dispatch_time = $request->dispatch_time;
        if ($request->has('completion_time')) $dispatch->completion_time = $request->completion_time;
        if ($request->has('status')) $dispatch->status = $request->status;

        $dispatch->save();

        return response()->json($dispatch);
    }

    public function destroy($id)
    {
        $dispatch = Dispatch::find($id);
        if ($dispatch) {
            $dispatch->delete();
            return response()->json(['message' => '削除成功']);
        }
        return response()->json(['message' => '見つかりません'], 404);
    }

    // ★追加：電話番号から一番新しい顧客情報を検索するメソッド
    public function searchCustomer(Request $request)
    {
        $phone = $request->query('phone');
        if (!$phone) {
            return response()->json(['message' => '電話番号が指定されていません'], 400);
        }

        // 入力された電話番号と一致するデータのうち、一番新しいもの（desc）を1件取得
        $customer = Dispatch::where('phone_number', $phone)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($customer) {
            return response()->json($customer);
        } else {
            return response()->json(['message' => '見つかりませんでした'], 404);
        }
    }

    // ★追加：CSVファイルを読み込んでデータベースに保存する処理
    public function importCsv(Request $request)
    {
        // 1. ファイルが送られてきているかチェック
        if (!$request->hasFile('csv_file')) {
            return response()->json(['message' => 'ファイルが選択されていません'], 400);
        }

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        // 2. ファイルの中身を読み込み（Excel特有のBOM文字化け対策も含む）
        $content = file_get_contents($path);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = explode("\n", trim($content));

        // 3. 1行目（ヘッダー）を読み飛ばす
        array_shift($lines);

        // 4. 2行目以降を順番にデータベースに保存していく
        foreach ($lines as $line) {
            if (empty(trim($line))) continue; // 空行はスキップ
            $row = str_getcsv($line);

            // ★変更：CSVの5番目の列（インデックス4）に顧客番号があればそれを使い、空欄なら自動生成する！
            $csvCustomerNum = trim($row[4] ?? '');
            $customerNumber = $csvCustomerNum !== '' ? $csvCustomerNum : (string) rand(10000000, 99999999);

            // エクスポートしたCSVの並び順（0:伝票ID, 1:配車日時, 2:配車完了日時...）
            Dispatch::create([
                'dispatch_time'   => !empty($row[1]) ? $row[1] : now(),
                'completion_time' => !empty($row[2]) ? $row[2] : now(),
                'status'          => !empty($row[3]) ? $row[3] : '未手配',
                'customer_number' => $customerNumber, // ★自動生成した番号をセット！
                'customer_name'   => mb_substr($row[5] ?? '', 0, 64),
                'phone_number'    => mb_substr($row[6] ?? '', 0, 20),
                'location_from_1' => mb_substr($row[7] ?? '', 0, 255),
                'lat_1'           => is_numeric($row[8] ?? null) ? $row[8] : null,
                'lng_1'           => is_numeric($row[9] ?? null) ? $row[9] : null,
                'location_from_2' => mb_substr($row[10] ?? '', 0, 255),
                'lat_2'           => is_numeric($row[11] ?? null) ? $row[11] : null,
                'lng_2'           => is_numeric($row[12] ?? null) ? $row[12] : null,
                'location_from_3' => mb_substr($row[13] ?? '', 0, 255),
                'lat_3'           => is_numeric($row[14] ?? null) ? $row[14] : null,
                'lng_3'           => is_numeric($row[15] ?? null) ? $row[15] : null,
                'location_to'     => mb_substr($row[16] ?? '', 0, 128),
                'call_area'       => mb_substr($row[17] ?? '', 0, 128),
                'guidance'        => mb_substr($row[18] ?? '', 0, 512),
                'primary_info'    => mb_substr($row[19] ?? '', 0, 256),
            ]);
        }

        return response()->json(['message' => 'インポートが完了しました']);
    }
}