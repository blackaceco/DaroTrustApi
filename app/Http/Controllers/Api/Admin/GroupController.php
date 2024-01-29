<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/groups",
     *     tags={"Admin - groups"},
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
     *         name="groupTypeId",
     *         in="path",
     *         description="id of an existing groupTypeId.",
     *         required=true,
     *     ),
     * 
     *     @OA\Parameter(
     *         name="featureTitle",
     *         in="path",
     *         description="a featureTitle of groups.",
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
    public function index(Request $request)
    {
        $groups = Group::with(['details', 'groupType.details']);

        if ($request->filled('featureTitle'))
            $groups->where('featureTitle', $request->input('featureTitle'));

        if ($request->filled('groupTypeId'))
            $groups->where('groupTypeId', $request->input('groupTypeId'));

        return $this->dataResponse([
            'groups' => $groups->get()
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/groups",
     *     tags={"Admin - groups"},
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
        $fields = checkApiValidationRules($request, Group::validationRules(), $this);
        $group = null;

        try {
            $fields['websiteId'] = $website->id;

            // creating
            $group = Group::create($fields);

            // attaching details
            $group->details()->createMany($request->input('details'));

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
     * @OA\Put(
     *     path="/api/admin/{website}/groups/{group}/translate",
     *     tags={"Admin - groups"},
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
     *                 required={"details"},
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
     *             @OA\Property(property="message", type="string", example="The group has been translated successfully."),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function translate(Request $request, Website $website, Group $group)
    {
        $rules = Group::validationRules();
        unset($rules['featureTitle']);
        checkApiValidationRules($request, $rules, $this);
        $old_values = $group->fieldValues();

        $detail = $request->input('details')[0];

        // check for dupilcation
        if ($group->details->contains($detail['languageId']))
            return $this->errorResponse("Duplicate !", [], 423);

        try {
            // attaching details
            $group->details()->create($detail);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $group->id,
                'entity' => "groups",
                'action' => "group translate",
                'oldValue' => $old_values,
                'newValue' => $group->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The group has been translated successfully.", []);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/groups/item",
     *     tags={"Admin - groups"},
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
     *                 required={"groupId", "itemId"},
     *                 @OA\Property(property="groupId", type="integer", example=9),
     *                 @OA\Property(property="itemId", type="integer", example=2),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new item has been added successfully."),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function addItem(Request $request, Website $website)
    {
        $fields = checkApiValidationRules($request, [
            'groupId' => "required|exists:groups,id",
            'itemId' => "required|exists:items,id",
        ], $this);
        
        $group = Group::where('websiteId', $website->id)->findOrFail($fields['groupId']);

        $old_values = $group->items;

        try {
            // we dont need it
            unset($fields['groupId']);
            
            // if duplicate then return with error
            if ($group->items->contains($fields['itemId']))
                return $this->errorResponse("Duplicate !");
            
            // adding
            $group->items()->attach($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $group->id,
                'entity' => "groups",
                'action' => "add-item",
                'oldValue' => $old_values,
                'newValue' => $group->items,
            ]);

            // responsing
            return $this->successResponse("The new item has been added successfully.");
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/groups/{group}",
     *     tags={"Admin - groups"},
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
    public function show($website, $group)
    {
        $group = Group::where('websiteId', $website)->with(['details', 'items', 'groupType'])->findOrFail($group);

        return $this->dataResponse([
            'group' => $group
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/groups/{group}",
     *     tags={"Admin - groups"},
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
    public function update(Request $request, Website $website, Group $group)
    {
        $fields = checkApiValidationRules($request, Group::validationRules($group->id), $this);
        $old_values = $group->fieldValues();

        try {
            // creating
            $group->update($fields);

            // syncing details
            $details = $group->details;
            $newDetails = $request->input('details');
            // $remainDetails = [];

            foreach ($newDetails as $newDetail) {
                // $remainDetails[] = $newDetail['languageId'];
                $is_update = false;
                // checking if available then update
                foreach ($details as $detail) 
                {
                    if ($detail->languageId == $newDetail['languageId'] && $detail->key == $newDetail['key'])
                    {
                        $detail->update($newDetail);
                        $is_update = true;
                        break;
                    }
                }
                
                // if no update available it is mean that is new and should be created
                if (!$is_update)
                    $group->details()->create($newDetail);
            }

            // removing missed language ids
            // $group->details()->whereNotIn('languageId', $remainDetails)->delete();

            // storing activity log
            ActivityLog::create([
                'groupId' => $group->id,
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
     *     path="/api/admin/{website}/groups/{group}",
     *     tags={"Admin - groups"},
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
    public function destroy(Website $website, Group $group)
    {
        if ($group->websiteId != $website->id)
            return $this->errorResponse("Bad request", [], 400);

        try {
            // deleting
            $group->delete();

            // storing activity log
            ActivityLog::create([
                'groupId' => null,
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
