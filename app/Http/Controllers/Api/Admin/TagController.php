<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tag;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class TagController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/tags",
     *     tags={"Admin - tags"},
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
    public function index(Request $request)
    {
        $tags = Tag::with('details')->get();

        if ($request->has('featureTitle'))
            $tags->where('featureTitle', $request->input('featureTitle'));

        return $this->dataResponse([
            'tags' => $tags
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/tags",
     *     tags={"Admin - tags"},
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
     *             @OA\Property(property="message", type="string", example="The new tag has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="tag",
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
        $fields = checkApiValidationRules($request, Tag::validationRules(), $this);
        $tag = null;

        try {
            $fields['websiteId'] = $website->id;

            // creating
            $tag = Tag::create($fields);

            // attaching details
            $tag->details()->createMany($request->input('details'));

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $tag->id,
                'entity' => "tags",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $tag->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new tag has been stored successfully.", ['tag' => $tag]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/tags/{tag}/translate",
     *     tags={"Admin - tags"},
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
     *         name="tag",
     *         in="path",
     *         description="id of an existing tag.",
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
     *             @OA\Property(property="message", type="string", example="The tag has been translated successfully."),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function translate(Request $request, Website $website, Tag $tag)
    {
        $rules = Tag::validationRules();
        unset($rules['featureTitle']);
        checkApiValidationRules($request, $rules, $this);
        $old_values = $tag->fieldValues();

        $detail = $request->input('details')[0];

        // check for dupilcation
        if ($tag->details->contains($detail['languageId']))
            return $this->errorResponse("Duplicate !", [], 423);

        try {
            // attaching details
            $tag->details()->create($detail);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $tag->id,
                'entity' => "tags",
                'action' => "tag translate",
                'oldValue' => $old_values,
                'newValue' => $tag->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The tag has been translated successfully.", []);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/tags/item",
     *     tags={"Admin - tags"},
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
     *                 required={"tagId", "itemId"},
     *                 @OA\Property(property="tagId", type="integer", example=9),
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
            'tagId' => "required|exists:tags,id",
            'itemId' => "required|exists:items,id",
        ], $this);
        
        $tag = Tag::where('websiteId', $website->id)->findOrFail($fields['tagId']);

        $old_values = $tag->items;

        try {
            // we dont need it
            unset($fields['tagId']);
            
            // if duplicate then return with error
            if ($tag->items->contains($fields['itemId']))
                return $this->errorResponse("Duplicate !");
            
            // adding
            $tag->items()->attach($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $tag->id,
                'entity' => "tags",
                'action' => "add-item",
                'oldValue' => $old_values,
                'newValue' => $tag->items,
            ]);

            // responsing
            return $this->successResponse("The new item has been added successfully.");
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/tags/{tag}",
     *     tags={"Admin - tags"},
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
     *         name="tag",
     *         in="path",
     *         description="id of an existing tag.",
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
    public function show($website, $tag)
    {
        $tag = Tag::where('websiteId', $website)->with(['details', 'items'])->findOrFail($tag);

        return $this->dataResponse([
            'tag' => $tag
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/tags/{tag}",
     *     tags={"Admin - tags"},
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
     *         name="tag",
     *         in="path",
     *         description="id of an existing tag.",
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
     *             @OA\Property(property="message", type="string", example="The tag has been updated successfully."),
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
    public function update(Request $request, Website $website, Tag $tag)
    {
        $fields = checkApiValidationRules($request, Tag::validationRules($tag->id), $this);
        $old_values = $tag->fieldValues();

        try {
            // creating
            $tag->update($fields);

            // syncing details
            $details = $tag->details;
            $newDetails = $request->input('details');
            $remainDetails = [];

            foreach ($newDetails as $newDetail) {
                $remainDetails[] = $newDetail['languageId'];
                $is_update = false;
                // checking if available then update
                foreach ($details as $detail) 
                {
                    if ($detail->languageId == $newDetail['languageId'])
                    {
                        $detail->update(['title' => $newDetail['title']]);
                        $is_update = true;
                        break;
                    }
                }

                // if no update available it is mean that is new and should be created
                if (!$is_update)
                    $tag->details()->create($newDetail);
            }

            // removing missed language ids
            $tag->details()->whereNotIn('languageId', $remainDetails)->delete();

            // storing activity log
            ActivityLog::create([
                'tagId' => $tag->id,
                'entityId' => $tag->id,
                'entity' => "tags",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $tag->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The tag has been updated successfully.", ['tag' => $tag]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/tags/{tag}",
     *     tags={"Admin - tags"},
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
     *         name="tag",
     *         in="path",
     *         description="id of an existing tag.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="the tag has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, Tag $tag)
    {
        if ($tag->websiteId != $website->id)
            return $this->errorResponse("Bad request", [], 400);

        try {
            // deleting
            $tag->delete();

            // storing activity log
            ActivityLog::create([
                'tagId' => null,
                'entityId' => $tag->id,
                'entity' => "tags",
                'action' => "delete",
                'oldValue' => $tag->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the tag has been deleted successfully.");
    }
}
