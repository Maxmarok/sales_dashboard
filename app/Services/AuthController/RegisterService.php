<?php
namespace App\Services\AuthController;

use App\Http\Resources\RegisterService\LoginResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    /**
     * @param array $data
     * @return JsonResponse
     */
    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json(['user' => new LoginResource($user), 'token' => $token], 200);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function login(array $data)
    {
        if (Auth::attempt($data)) {
            $user = User::where('email', $data['email'])->first();
            $token = $user->createToken('api_token')->plainTextToken;
            return response()->json(['user' => new LoginResource($user), 'token' => $token], 200);
        }

        return response()->json(['errors' => ['email' => 'Введен неправильный email или пароль'], ], 422);
        
    }
}
