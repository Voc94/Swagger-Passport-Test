<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "c_password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="c_password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="object",
     *                 @OA\Property(property="token", type="string", example="token"),
     *                 @OA\Property(property="name", type="string", example="John Doe")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        Log::info('Register method called');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
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

            // Process the invitation if it exists
            if ($request->has('invitation')) {
                $invitationCode = $request->input('invitation');
                $invitationEmail = base64_decode($invitationCode);
                Log::info('Processing invitation', ['invitationCode' => $invitationCode, 'invitationEmail' => $invitationEmail]);

                $invitation = Invitation::where('email', $invitationCode)->where('status', 0)->first();
                if ($invitation) {
                    Log::info('Invitation found and valid', ['invitation' => $invitation]);

                    $invitingUser = User::find($invitation->user_id);
                    if ($invitingUser) {
                        if ($invitingUser->id !== $user->id) {
                            Log::info('Inviting user found', ['invitingUser' => $invitingUser]);

                            $invitingUser->friends()->attach($user->id);
                            $user->friends()->attach($invitingUser->id);

                            // Update the invitation status to 1 (used)
                            $invitation->status = 1;
                            $invitation->save();

                            Log::info('Friends relationship created and invitation status updated', ['invitingUser' => $invitingUser->id, 'newUser' => $user->id]);
                        } else {
                            Log::warning('Inviting user and new user are the same', ['user' => $user->id]);
                        }
                    } else {
                        Log::error('Inviting user not found', ['invitation' => $invitation]);
                    }
                } else {
                    Log::error('Invitation not found or already used', ['invitationEmail' => $invitationEmail]);
                }
            }

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
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User login successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="object",
     *                 @OA\Property(property="token", type="string", example="token"),
     *                 @OA\Property(property="name", type="string", example="John Doe")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        Log::info('Login method called');

        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();

                // Process the invitation if it exists
                if ($request->has('invitation')) {
                    $invitationCode = $request->input('invitation');
                    $invitationEmail = base64_decode($invitationCode);
                    Log::info('Processing invitation', ['invitationCode' => $invitationCode, 'invitationEmail' => $invitationEmail]);

                    $invitation = Invitation::where('email', $invitationCode)->where('status', 0)->first();
                    if ($invitation) {
                        Log::info('Invitation found and valid', ['invitation' => $invitation]);

                        $invitingUser = User::find($invitation->user_id);
                        if ($invitingUser) {
                            if ($invitingUser->id !== $user->id) {
                                Log::info('Inviting user found', ['invitingUser' => $invitingUser]);

                                $invitingUser->friends()->attach($user->id);
                                $user->friends()->attach($invitingUser->id);

                                // Update the invitation status to 1 (used)
                                $invitation->status = 1;
                                $invitation->save();

                                Log::info('Friends relationship created and invitation status updated', ['invitingUser' => $invitingUser->id, 'newUser' => $user->id]);
                            } else {
                                Log::warning('Inviting user and logged-in user are the same', ['user' => $user->id]);
                            }
                        } else {
                            Log::error('Inviting user not found', ['invitation' => $invitation]);
                        }
                    } else {
                        Log::error('Invitation not found or already used', ['invitationEmail' => $invitationEmail]);
                    }
                }

                $success['token'] = $user->createToken('MyApp')->accessToken;
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

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get authenticated user details",
     *     tags={"Auth"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-08-05T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-08-05T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */
    public function userDetails(): JsonResponse
    {
        Log::info('User details method called');

        try {
            if (Auth::check()) {
                $user = Auth::user();
                Log::info('Authenticated user details retrieved', ['user' => $user]);

                return $this->sendResponse($user, 'User details retrieved successfully.');
            } else {
                Log::info('Unauthorized access attempt');
                return $this->sendError('Unauthorized.', ['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            Log::error('Error in userDetails method: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
