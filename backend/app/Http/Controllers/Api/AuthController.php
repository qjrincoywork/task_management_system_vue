<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * User model instance.
     *
     * @var User
     */
    protected $user;

    /**
     * UserController constructor.
     * @param User $user
     *
     * @return void
     */
    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->user->registerUser($request->validated());

        return response()->json([
            'message' => 'Successfully Registered.',
            'token' => $user->createToken(env('API_AUTH_TOKEN_NAME'))->plainTextToken,
        ], Response::HTTP_OK);
    }

    /**
     * Log in an existing user.
     *
     * @param AuthRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(AuthRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'message' => 'Logged in successfully',
            'user' => auth()->user(),
            'token' => auth()->user()->createToken(env('API_AUTH_TOKEN_NAME'))->plainTextToken
        ]);
    }

    /**
     * Logout a user by deleting the current access token.
     *
     * @return JsonResponse The JSON response with HTTP status code 204 (NO CONTENT).
     */
    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }

    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
