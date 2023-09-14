<?php
namespace App\Services\ProfileController;

use App\Services\WB\KeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ProfileService{
    /**
     * Обновление профиля
     * @param array $data
     * @return JsonResponse
     */
    public function update(array $data)
    {
        $user = Auth()->user();

        if($data['password'] != null){
            $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            "message" => "200",
            "user" => $user
        ]);
    }

    public function addApiKey(array $data)
    {
        //marketplace + key
        //проверяем ключ, затем берем логин и пишем в БД
        switch ($data['marketplace']){
            case 'OZ':
                break;
            case 'WB':
                return (new KeyService())->addKey($data);
                break;
            case 'YA':
                break;
        }
    }
}
