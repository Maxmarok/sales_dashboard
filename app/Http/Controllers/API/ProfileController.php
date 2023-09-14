<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeftoversController\LeftoversRequest;
use App\Http\Requests\ProfileController\AddApiKeyRequest;
use App\Http\Requests\ProfileController\AddLkRequest;
use App\Http\Requests\ProfileController\DeleteApiKeyRequest;
use App\Http\Requests\ProfileController\UpdateRequest;
use App\Models\ApiKey;
use App\Models\Lk;
use App\Services\ProfileController\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(UpdateRequest $request)
    {
        return (new ProfileService())->update($request->validated());
    }

    public function addApiKey(AddApiKeyRequest $request)
    {
        return (new ProfileService())->addApiKey($request->validated());
    }

    public function userInfo()
    {
        return response()->json([
            'user' => Auth()->User()
        ]);
    }

    public function addLk(AddLkRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth()->id();
        return Response()->json([
            "data" => Lk::create($data)
        ], 200);
    }

    public function index(Request $id)
    {
        $lk = Auth()->user()->getLk($id);

        sleep(3);
        return Response()->json([
            "code" => 201,
            "data" => $lk
        ], 200);
    }

    public function listLk()
    {
        $lk = Auth()->user()->lk;
        return $lk;
    }

    public function apiKeyList()
    {
        return Auth()->user()->apiKeys();
    }

    public function deleteApiKey(DeleteApiKeyRequest $request)
    {
        ApiKey::findOrFail($request->key_id)->delete();
        return Response()->json([
            "message" => true
        ]);
    }

    public function lkDelete(LeftoversRequest $request)
    {
        Lk::findOrFail($request->lk_id)->delete();
        return Response()->json([
            "message" => true
        ]);
    }

    public function lkUpdate(AddLkRequest $request, $id)
    {
        if(Auth()->User()->lk->where('id', $id)->isEmpty()){
            //$fail('ЛК вам не принадлежит');
            return Response()->json([
                "message" => 'ЛК Вам не принадлежит'
            ], 403);
        }

        Lk::findOrFail($id)->update($request->validated());
        return Response()->json([
            "message" => true
        ]);
    }
}
