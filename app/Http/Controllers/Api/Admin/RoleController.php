<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/roles",
     *     tags={"Admin - roles"},
     *     summary="auth",
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items (
     *                      type="object",
     *                      @OA\Property (property="id", type="integer", example="7"),
     *                      @OA\Property (property="title", type="string", example="Example Website Title"),
     *                      @OA\Property (property="createdAt", type="string", example="2023-10-01T08:20:37.000000Z"),
     *                      @OA\Property (property="updatedAt", type="string", example="2023-10-01T08:20:37.000000Z"),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="meta",
     *                  type="object",
     *                  @OA\Property (property="current_page", type="integer", example=1),
     *                  @OA\Property (property="last_page", type="integer", example=1),
     *                  @OA\Property (property="has_next_page", type="boolean", example=false),
     *                  @OA\Property (property="has_previous_page", type="boolean", example=false),
     *                  @OA\Property (property="next_page_url", type="string", example=null),
     *                  @OA\Property (property="prev_page_url", type="string", example=null),
     *                  @OA\Property (property="on_first_page", type="boolean", example=true),
     *                  @OA\Property (property="per_page", type="integer", example=10),
     *                  @OA\Property (property="total", type="integer", example=20),
     *              )
     *          )
     *      )
     * )
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();

        return $this->dataResponse([
            'roles' => $roles
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/roles",
     *     tags={"Admin - roles"},
     *     summary="auth",
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title"},
     *                 @OA\Property(property="title", type="string", example="Example Role Title"),
     *                 @OA\Property(property="permissions", type="array", @OA\items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="create", type="boolean", example=1),
     *                     @OA\Property(property="read", type="boolean", example=0),
     *                     @OA\Property(property="update", type="boolean", example=1),
     *                     @OA\Property(property="delete", type="boolean", example=0),
     *                 )),
     *             )
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new role has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="role",
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Example Role Title"),
     *                     @OA\Property(property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="permissions", type="array", @OA\Items(type="object")),
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function store(Request $request)
    {
        $fields = checkApiValidationRules($request, Role::validationRules(), $this);
        $role = null;

        try {
            // creating
            $role = Role::create($fields);

            // syncing permissions
            $permissions = [];
            foreach ($request->input('permissions') as $permission) {
                $permissions[$permission['id']] = [
                    'create' => $permission['create'],
                    'read' => $permission['read'],
                    'update' => $permission['update'],
                    'delete' => $permission['delete'],
                ];
            }
            
            $role->permissions()->sync($permissions);

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $role->id,
                'entity' => "roles",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $role->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new role has been stored successfully.", ['role' => $role]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/roles/{id}",
     *     tags={"Admin - roles"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing role.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent (
     *              type="object",
     *              @OA\Property (property="id", type="integer", example="7"),
     *              @OA\Property (property="title", type="string", example="Example Role Title"),
     *              @OA\Property (property="permissions", type="array", @OA\Items(type="object")),
     *              @OA\Property (property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *              @OA\Property (property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show($role)
    {
        $role = Role::with('permissions')->findOrFail($role);

        return $this->dataResponse([
            'role' => $role
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/roles/{id}",
     *     tags={"Admin - roles"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing role.",
     *         required=true,
     *     ),
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title"},
     *                 @OA\Property(property="title", type="string", example="Example Role Title"),
     *                 @OA\Property(property="permissions", type="array", @OA\items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="create", type="boolean", example=1),
     *                     @OA\Property(property="read", type="boolean", example=0),
     *                     @OA\Property(property="update", type="boolean", example=1),
     *                     @OA\Property(property="delete", type="boolean", example=0),
     *                 )),
     *             )
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new role has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="role",
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Example Role Title"),
     *                     @OA\Property(property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="permissions", type="array", @OA\Items(type="object")),
     *                 ),
     *             ),
     *         )
     *     ),
     * 
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Role $role)
    {
        $fields = checkApiValidationRules($request, Role::validationRules($role->id), $this);
        $old_values = $role->fieldValues();

        try {
            // creating
            $role->update($fields);

            // syncing permissions
            $permissions = [];
            foreach ($request->input('permissions') as $permission) {
                $permissions[$permission['id']] = [
                    'create' => $permission['create'],
                    'read' => $permission['read'],
                    'update' => $permission['update'],
                    'delete' => $permission['delete'],
                ];
            }
            
            $role->permissions()->sync($permissions);

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $role->id,
                'entity' => "roles",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $role->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The role has been updated successfully.", ['role' => $role]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/roles/{id}",
     *     tags={"Admin - roles"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing role.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="the role has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Role $role)
    {
        try {
            // deleting
            $role->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $role->id,
                'entity' => "roles",
                'action' => "delete",
                'oldValue' => $role->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the role has been deleted successfully.");
    }
}
