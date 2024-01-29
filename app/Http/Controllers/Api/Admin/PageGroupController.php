<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PageGroup;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class PageGroupController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/page-groups",
     *     tags={"Admin - page-groups"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function index(Request $request, Website $website)
    {
        $page = $request->query('page');

        $groups = PageGroup::with('features')->where('websiteId', $website->id);

        if($page) {
            $groups = $groups->where('page', $page);
        }

        $groups = $groups->get();

        return $this->dataResponse([
            'groups' => $groups
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/page-groups",
     *     tags={"Admin - page-groups"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"featureTitle", "details"},
     *                 @OA\Property(property="featureTitle", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="details", type="array", @OA\Items(
     *                     @OA\Property(property="languageId", type="integer", example="2"),
     *                     @OA\Property(property="title", type="string", example="Example Title"),
     *                 )),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new group has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="group",
     *                     type="object"
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function store(Request $request,Website $website)
    {
        $fields = checkApiValidationRules($request, PageGroup::validationRules(), $this);
        $group = null;

        try {
            $fields['websiteId'] = $website->id;

            // creating
            $group = PageGroup::create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $group->id,
                'entity' => "groups",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $group->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new group has been stored successfully.", ['group' => $group]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/page-groups/{group}",
     *     tags={"Admin - page-groups"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="group",
     *         in="path",
     *         description="id of an existing group.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent (
     *              type="object",
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show(Website $website, $group)
    {
        $group = PageGroup::where('websiteId', $website->id)->findOrFail($group);

        return $this->dataResponse([
            'group' => $group
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/page-groups/{group}",
     *     tags={"Admin - page-groups"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="group",
     *         in="path",
     *         description="id of an existing group.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"featureTitle", "details"},
     *                 @OA\Property(property="featureTitle", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="details", type="array", @OA\Items(
     *                     @OA\Property(property="languageId", type="integer", example="2"),
     *                     @OA\Property(property="title", type="string", example="Example Title"),
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
     *             @OA\Property(property="message", type="string", example="The group has been updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Website $website, $group)
    {
        $group = PageGroup::where('websiteId', $website->id)->findOrFail($group);

        $fields = checkApiValidationRules($request, PageGroup::validationRules($group->id), $this);
        $old_values = $group->fieldValues();

        try {
            // creating
            $group->update($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $group->id,
                'entity' => "groups",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $group->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The group has been updated successfully.", ['group' => $group]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/page-groups/{group}",
     *     tags={"Admin - page-groups"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="group",
     *         in="path",
     *         description="id of an existing group.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the group has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, $group)
    {
        $group = PageGroup::where('websiteId', $website->id)->findOrFail($group);

        if ($group->websiteId != $website->id)
            return $this->errorResponse("Bad request", [], 400);

        try {
            // deleting
            $group->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $group->id,
                'entity' => "groups",
                'action' => "delete",
                'oldValue' => $group->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the group has been deleted successfully.");
    }
}
