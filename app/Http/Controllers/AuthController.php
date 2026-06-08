<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\TraitApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use TraitApiResponse;

     public function register(Request $request){

        $request->validate([
            'name'=> ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'alpha_num', 'min:8']

        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = auth('api')->attempt(['email' => $request->email, 'password' => $request->password]);

        return $this->successResponse(['user' => $user, 'token' => $token], 'usario creado correctamente', 201);
    }

    public function login(Request $request){

            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'alpha_num', 'min:8']

            ]);

            $token = auth('api')->attempt(['email' => $request->email, 'password' => $request->password]);

            

            if($token){
                return $this->successResponse(['user' => auth('api')->user(), 'token' => $token], 'usario logeado correctamente', 200);
            }else{
                return $this->errorResponse('Credenciales inválidas', 401);
            }
        }

        public function logout(){

            auth('api')->logout();;

            return $this->successResponse(null, 'logut exitoso', 200);

        }

        public function me(){
            return $this->successResponse(auth('api')->user(), 'Usuario autenticado', 200);
        }
}