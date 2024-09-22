<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Core\RequestController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Log;
use Auth;

class AuthController extends RequestController
{
    public function register(Request $request)
    {
        try{
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $user = User::create([
                'name' => $inputs['name'],
                'email' => $inputs['email'],
                'password' => Hash::make($inputs['password']),
            ]);

            $role = 'student';
            $roles = Role::where('name', $role)->get();
            $user->roles()->attach($roles);

            $response = $this->sendData($request, $this->success,[]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }
        return $response;
    }

    public function login(Request $request)
    {
        try{
            $inputs = $request->all();
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($request, $validator->messages());
            }

            $user = User::where('email', $inputs['email'])->first();

            if (!$user || !Hash::check($inputs['password'], $user->password)) {
                return $this->sendJsonError($request, $this->badRequest,"Invalid credentials");
            }

            $token = $user->createToken('authToken')->plainTextToken;

            $data=[
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
            $response = $this->sendData($request, $this->success, $data);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }
        return $response;
    }

    public function logout(Request $request)
    {
        try{
            $request->user()->currentAccessToken()->delete();

            $response = $this->sendData($request, $this->success,[]);
        } catch (\Exception $e) {
            report($e);
            $response = $this->sendError($request);
        }
        return $response;
    }
}

