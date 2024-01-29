<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/admins",
     *     tags={"Admin - management"},
     *     summary="auth",
     *
     *     @OA\Response(
     *          response=200,
     *          description="indexing the available admins.",
     *          @OA\JsonContent (
     *              type="array",
     *              @OA\Items (
     *                  type="object",
     *                  @OA\Property (
     *                      property="id",
     *                      type="integer",
     *                      example="7",
     *                  ),
     *                  @OA\Property (
     *                      property="name",
     *                      type="string",
     *                      example="Example Admin Name",
     *                  ),
     *                  @OA\Property (
     *                      property="email",
     *                      type="string",
     *                      example="example@email.com",
     *                  ),
     *                  @OA\Property (
     *                      property="status",
     *                      type="string",
     *                      example="active",
     *                  ),
     *                  @OA\Property (
     *                      property="createdAt",
     *                      type="string",
     *                      example="2023-10-01T08:20:37.000000Z",
     *                  ),
     *                  @OA\Property (
     *                      property="updatedAt",
     *                      type="string",
     *                      example="2023-10-01T08:20:37.000000Z",
     *                  ),
     *              )
     *          )
     *      )
     * )
     */
    public function index()
    {
        $admins = Admin::paginate(10);

        return $this->dataResponse($admins, null, true);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/admins",
     *     tags={"Admin - management"},
     *     summary="auth",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "password", "superAdmin", "status"},
     *                 @OA\Property(property="name", type="string", example="Full Admin Name"),
     *                 @OA\Property(property="email", type="string", example="example@email.com"),
     *                 @OA\Property(property="password", type="string", example="blah-blah-blah"),
     *                 @OA\Property(property="superAdmin", type="string", example="0"),
     *                 @OA\Property(property="status", type="string", example="inactive"),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="integer")),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new admin has been stored successfully."),
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
    public function store(Request $request)
    {
        $fields = checkApiValidationRules($request, Admin::validationRules(), $this);
        $admin = null;

        try {
            // creating
            $admin = Admin::create($fields);

            // // syncing roles
            // $admin->roles()->sync($request->input('roles', []));

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $admin->id,
                'entity' => "admins",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $admin->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new admin has been stored successfully.", ['admin' => $admin]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/admins/{id}",
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
     *     @OA\Response(
     *          response=200,
     *          description="the admin profile details.",
     *          @OA\JsonContent (
     *              type="object",
     *              @OA\Property (
     *                  property="id",
     *                  type="integer",
     *                  example="7",
     *              ),
     *              @OA\Property (
     *                  property="name",
     *                  type="string",
     *                  example="Example Admin Name",
     *              ),
     *              @OA\Property (
     *                  property="email",
     *                  type="string",
     *                  example="example@email.com",
     *              ),
     *              @OA\Property (
     *                  property="status",
     *                  type="string",
     *                  example="active",
     *              ),
     *              @OA\Property (
     *                  property="createdAt",
     *                  type="string",
     *                  example="2023-10-01T08:20:37.000000Z",
     *              ),
     *              @OA\Property (
     *                  property="updatedAt",
     *                  type="string",
     *                  example="2023-10-01T08:20:37.000000Z",
     *              ),
     *              @OA\Property (
     *                  property="roles",
     *                  type="object",
     *                  @OA\Property (property="id", type="integer", example=2),
     *                  @OA\Property (property="title", type="string", example="Admin Manager"),
     *                  @OA\Property (property="permissions", type="array", @OA\Items (type="object")),
     *              ),
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show($admin)
    {
        $admin = Admin::with('roles:id,title', 'roles.permissions')->findOrFail($admin);

        return $this->dataResponse(['admin' => $admin], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/admins/{id}",
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
     *                 @OA\Property(property="superAdmin", type="string", example="0"),
     *                 @OA\Property(property="status", type="string", example="inactive"),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="integer")),
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
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Admin $admin)
    {
        $fields = checkApiValidationRules($request, Admin::validationRules($admin->id), $this);

        $old_values = $admin->fieldValues();

        try {
            // creating
            $admin->update($fields);

            // // syncing roles
            // $admin->roles()->sync($request->input('roles', []));

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $admin->id,
                'entity' => "admins",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $admin->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The admin has been updated successfully.", ['admin' => $admin]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/admin/admins/password/{admin}",
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
     *                 required={"newPassword"},
     *                 @OA\Property(property="newPassword", type="string", example="test-test-test"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="the password has been changed successfully."),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function updatePassword(Request $request, Admin $admin)
    {
        $fields = checkApiValidationRules($request, [
            'newPassword' => "required|string|min:8"
        ], $this);

        // prevent password from updating
        unset($fields['password']);

        try {
            // creating
            $admin->update($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $admin->id,
                'entity' => "admins",
                'action' => "reset password",
                'oldValue' => null,
                'newValue' => null,
            ]);

            // responsing
            return $this->successResponse("The password has been changed successfully.");
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/admins/{id}",
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
     *     @OA\Response(
     *          response=200,
     *          description="the admin has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Admin $admin)
    {
        try {
            // deleting
            $admin->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $admin->id,
                'entity' => "admins",
                'action' => "delete",
                'oldValue' => null,
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the admin has been deleted successfully.");
    }
}
