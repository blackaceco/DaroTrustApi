<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\NavigationGroup;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/navigation-group",
     *     tags={"Admin - navigation-group"},
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
    public function index(Website $website)
    {
        $navigation_groups = NavigationGroup::where('websiteId', $website->id)->with(['schemas.details', 'items.details'])->get();

        return $this->dataResponse([
            'groups' => $navigation_groups
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/navigation-group",
     *     tags={"Admin - navigation-group"},
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
     *                 required={"navigation", "languageId"},
     *                 @OA\Property(property="navigation", type="string", example="Blah blah blah"),
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
     *             @OA\Property(property="message", type="string", example="The new navigation group has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function store(Request $request, Website $website)
    {
        $fields = checkApiValidationRules($request, NavigationGroup::validationRules(), $this);
        $navigation_group = null;

        try {
            // getting the website
            $fields['websiteId'] = $website->id;

            // creating
            $navigation_group = NavigationGroup::create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $navigation_group->id,
                'entity' => "navigation_groups",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $navigation_group->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new navigation group has been stored successfully.", ['navigation_group' => $navigation_group]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/navigation-group/{id}",
     *     tags={"Admin - navigation-group"},
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
     *         name="id",
     *         in="path",
     *         description="id of an existing navigation group.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show($website, $navigation_group)
    {
        $navigation_group = NavigationGroup::with(['schemas.details', 'items.details'])->findOrFail($navigation_group);

        return $this->dataResponse([
            'group' => $navigation_group
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/navigation-group/{id}",
     *     tags={"Admin - navigation-group"},
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
     *         name="id",
     *         in="path",
     *         description="id of an existing navigation group.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"navigation", "languageId"},
     *                 @OA\Property(property="navigation", type="string", example="Blah blah blah"),
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
     *             @OA\Property(property="message", type="string", example="The new localization has been stored successfully."),
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
    public function update(Request $request, Website $website, NavigationGroup $navigation_group)
    {
        $fields = checkApiValidationRules($request, NavigationGroup::validationRules($navigation_group->id), $this);
        $old_values = $navigation_group->fieldValues();

        try {
            $fields['websiteId'] = $website->id;

            // updating
            $navigation_group->update($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $navigation_group->id,
                'entity' => "navigation_groups",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $navigation_group->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The navigation group has been updated successfully.", ['navigation_group' => $navigation_group]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/navigation-group/{id}",
     *     tags={"Admin - navigation-group"},
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
     *         name="id",
     *         in="path",
     *         description="id of an existing navigation-group.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the navigation group has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy($website, $navigation_group)
    {
        try {
            // deleting
            $navigation_group = NavigationGroup::where('websiteId', $website)->findOrFail($navigation_group);
            $navigation_group->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website,
                'entityId' => $navigation_group->id,
                'entity' => "navigation_groups",
                'action' => "delete",
                'oldValue' => $navigation_group->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the navigation group has been deleted successfully.");
    }
}
