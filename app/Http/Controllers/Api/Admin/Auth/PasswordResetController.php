<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\Admin;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/admin/password/reset",
     *     tags={"Admin - authentication"},
     *     summary="guest",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email",},
     *                 @OA\Property(property="email", type="string", example="example@email.com"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="An email has been sent to your email address."),
     *         )
     *     ),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function sendResetLinkEmail (Request $request)
    {
        // validation
        checkApiValidationRules($request, [
            'email' => "required|email|exists:admins,email",
        ], $this);

        $admin = Admin::where('email', $request->email)->firstOrFail();
        
        $token = json_encode([
            'id' => $admin->id,
            'expire_at' => now()->addMinutes(10),
        ]);

        $token = Crypt::encryptString($token);
        
        $resetLink = config('custom.front_url') . "/reset/password/$token";

        Mail::to($admin->email)->send(new PasswordResetMail($resetLink));

        return $this->successResponse("An email has been sent to your email address.");
    }

    /**
     * @OA\Post(
     *     path="/api/admin/password/reset/verify",
     *     tags={"Admin - authentication"},
     *     summary="guest",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"token", "password", "password_confirmation"},
     *                 @OA\Property(property="token", type="string", example="eyJpdiI6ImdTTW44ZHFqTdFQ3c9PSIsIn..............................."),
     *                 @OA\Property(property="password", type="string", example="blah-blah-blah"),
     *                 @OA\Property(property="password_confirmation", type="string", example="blah-blah-blah"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your password has been reseted successfully."),
     *         )
     *     ),
     *     @OA\Response(response="410", description="the link were expired !"),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function reset(Request $request)
    {
        // validation
        checkApiValidationRules($request, [
            'token' => "required|string",
            'password' => "required|string|min:8|confirmed"
        ], $this);

        // decrypting the token
        try {
            $token = json_decode( Crypt::decryptString($request->input('token')) );

            if (now()->parse($token->expire_at) < now())
                return $this->errorResponse("the link were expired !", [], 410);
    
            // find the admin or fail
            $admin = Admin::findOrFail($token->id);
    
            // update & change the admin's password
            $admin->update([
                'password' => $request->input('password'),
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("Your password has been reseted successfully.");
    }
}
