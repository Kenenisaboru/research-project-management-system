<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthController
 *
 * Handles Sanctum token issuance, revocation, and current user retrieval for API V1 clients.
 */
class AuthController extends ApiController
{
    /**
     * Authenticate user credentials and issue a Sanctum personal access token.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('The provided credentials do not match our records.', [
                'email' => [__('Invalid institutional email or password.')],
            ], 401);
        }

        if ($user->status !== 'active') {
            return $this->error('Account has been suspended. Please contact the RPMS administrator.', null, 403);
        }

        $deviceName = $request->input('device_name', 'rpms-mobile-api');
        $token = $user->createToken($deviceName)->plainTextToken;

        $user->update(['last_login_at' => now()]);

        AuditService::log('API_LOGIN', 'User', $user->id, "User {$user->name} authenticated via API token ({$deviceName})", $user->id);

        return $this->success([
            'token' => $token,
            'type'  => 'Bearer',
            'user'  => new UserResource($user->load('department')),
        ], 'Authentication successful.');
    }

    /**
     * Return the currently authenticated API user details.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()->load('department')),
            'User profile retrieved successfully.'
        );
    }

    /**
     * Revoke the current access token.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        AuditService::log('API_LOGOUT', 'User', $user->id, "User {$user->name} logged out and revoked current API token", $user->id);

        return $this->success(null, 'API token revoked successfully.');
    }
}
