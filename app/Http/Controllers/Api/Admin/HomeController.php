<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Put(
     *     path="/api/admin/update/self",
     *     tags={"Admin - management"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing admin.",
     *         required=true,
     *     ),
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={},
     *                 @OA\Property(property="name", type="string", example="Full Admin Name"),
     *                 @OA\Property(property="email", type="string", example="example@email.com"),
     *                 @OA\Property(property="password", type="string", example="blah-blah-blah"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The admin has been updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Full Admin Name"),
     *                 @OA\Property(property="email", type="string", example="example@email.com"),
     *                 @OA\Property(property="status", type="string", example="inactive"),
     *                 @OA\Property(property="updatedAt", type="string", example="2023-10-02T12:50:00.000000Z"),
     *                 @OA\Property(property="createdAt", type="string", example="2023-10-02T12:50:00.000000Z"),
     *                 @OA\Property(property="id", type="integer", example=8),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function updateSelfAccount(Request $request)
    {
        $admin = auth()->user();
        $fields = checkApiValidationRules($request, Admin::validationRules($admin->id), $this);

        // prevent fields
        unset($fields['superAdmin']);
        unset($fields['status']);
        unset($fields['roles']);

        try {
            // creating
            $admin->update($fields);

            // responsing
            return $this->successResponse("Your account has been updated successfully.", ['admin' => $admin]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }
}
