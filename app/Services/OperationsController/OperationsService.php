<?php
namespace App\Services\OperationsController;

use App\Models\Accounts;
use Illuminate\Support\Facades\DB;

class OperationsService{

    public function accounts()
    {
        $user = auth()->user();
        $data = Accounts::where('user_id', $user->id)->get();

        return $data;
    }

    public function add_account()
    {
        $user = auth()->user();
        $data = Accounts::where('user_id', $user->id)->get();

        return $data;
    }
}