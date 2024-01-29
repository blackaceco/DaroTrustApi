<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('guest')->only(['login']);
        // $this->middleware('auth')->except(['login']);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     tags={"Admin - authentication"},
     *     summary="guest",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="string", example="example@email.com"),
     *                 @OA\Property(property="password", type="string", example="blah-blah-blah")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="The admin has been authenticated."),
     *     @OA\Response(response="404", description="The admin has been authenticated before !"),
     *     @OA\Response(response="401", description="Invalid the credentials."),
     *     @OA\Response(response="422", description="Missed credentials.")
     * )
     */
    public function login(Request $request)
    {
        // credential's validation
        $credentials = checkApiValidationRules($request, [
            'email' => "required|email|exists:admins,email",
            'password' => "required|string",
        ], $this);

        // append extra field for the credentials for checking
        $credentials['status'] = 'active';

        // attempt login
        $login = Auth::attempt($credentials);

        // check is authenticated or not
        if ($login) {
            return $this->successResponse("You have been authenticated successfully.", Auth::user()->toArray());
        } else
            return $this->errorResponse("Invalid email or password.", [], 401);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/me",
     *     tags={"Admin - authentication"},
     *     summary="auth",
     *
     *     @OA\Response(response="200", description="The authenticated admin profile."),
     *     @OA\Response(response="401", description="Unauthenticated.")
     * )
     */
    public function me()
    {
        $admin = Admin::with('roles:id,title', 'roles.permissions')->findOrFail( auth()->id() );
        return $this->dataResponse([
            'admin' => $admin
        ], null, false, true);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/change-password",
     *     tags={"Admin - authentication"},
     *     summary="auth",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(property="current_password", type="string", example="***********"),
     *                 @OA\Property(property="password", type="string", example="***********"),
     *                 @OA\Property(property="password_confirmation", type="string", example="***********")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="The admin has been authenticated."),
     *     @OA\Response(response="401", description="Invalid the credentials."),
     *     @OA\Response(response="422", description="Missed credentials.")
     * )
     */
    public function changePassword(Request $request)
    {
        // validate & getting fields
        $fields = checkApiValidationRules($request, [
            'oldPassword' => "required|string",
            'newPassword' => "required|string|min:8"
        ], $this);

        // check current password
        if (!Hash::check($fields['oldPassword'], auth()->user()->password))
            return $this->errorResponse("Invalid current password !", [], 401);

        // change password
        auth()->user()->update([
            'password' => $fields['newPassword']
        ]);

        // return success response
        return $this->successResponse("Your password has been changed successfully.");
    }

    /**
     * @OA\Post(
     *     path="/api/admin/logout",
     *     tags={"Admin - authentication"},
     *     summary="auth",
     *
     *     @OA\Response(response="200", description="The admin has been logged out."),
     *     @OA\Response(response="401", description="Unauthenticated."),
     * )
     */
    public function logout()
    {
        Auth::logout();

        return $this->successResponse("You have been logged out successfully.");
    }
}
