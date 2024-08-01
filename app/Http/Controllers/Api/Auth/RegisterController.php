<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        Log::info('Register method called');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            //'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed', $validator->errors()->toArray());
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);

            Log::info('User created', ['user' => $user]);

            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['name'] = $user->name;

            Log::info('Token created', ['token' => $success['token']]);

            return $this->sendResponse($success, 'User registered successfully.')
                ->header('Accept', 'application/json')
                ->header('Authorization', 'Bearer ' . $success['token']);
        } catch (\Exception $e) {
            Log::error('Error in register method: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        Log::info('Login method called');

        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $success['token'] = $user->createToken('Mphp artisan passport:client --personalyApp')->accessToken;
                $success['name'] = $user->name;

                Log::info('User logged in', ['user' => $user]);

                return $this->sendResponse($success, 'User login successfully.')
                    ->header('Accept', 'application/json')
                    ->header('Authorization', 'Bearer ' . $success['token']);
            } else {
                Log::info('Unauthorized login attempt', ['email' => $request->email]);
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'])
                    ->header('Accept', 'application/json');
            }
        } catch (\Exception $e) {
            Log::error('Error in login method: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
