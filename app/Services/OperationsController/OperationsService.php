<?php
namespace App\Services\OperationsController;

use App\Models\Accounts;
use App\Models\Operations;
use Illuminate\Support\Facades\DB;

class OperationsService{

    public function accounts()
    {
        $user = auth()->user();
        $data = Accounts::where('user_id', $user->id)->get();

        return $data;
    }

    public function operations()
    {
        $user = auth()->user();
        $data = Operations::where('user_id', $user->id)->get();

        return $data;
    }

    public function add_account(array $data)
    {
        $data['user_id'] = Auth()->id();
        $account = Accounts::create($data);

        return response()->json([
            "message" => "200",
            "account" => $account
        ]);
    }

    public function add_operation(array $data)
    {
        $data['user_id'] = Auth()->id();
        $data['article_id'] = 1;
        $account = Operations::create($data);

        return response()->json([
            "message" => "200",
            "account" => $account
        ]);
    }
}