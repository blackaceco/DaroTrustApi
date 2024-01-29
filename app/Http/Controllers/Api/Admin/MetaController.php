<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MetaResource;
use App\Models\ActivityLog;
use App\Models\Meta;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/meta",
     *     tags={"Admin - meta"},
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
    public function index()
    {
        $metas = Meta::with(['details'])->get();

        return $this->dataResponse([
            'meta' => $metas
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/meta",
     *     tags={"Admin - meta"},
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
     *                 required={"page", "details"},
     *                 @OA\Property(property="page", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="details", type="array", @OA\Items(
     *                     @OA\Property(property="description", type="string", example=""),
     *                     @OA\Property(property="image", type="string", example=""),
     *                     @OA\Property(property="keywords", type="string", example=""),
     *                     @OA\Property(property="languageId", type="string", example=""),
     *                     @OA\Property(property="title", type="string", example=""),
     *                 )),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function store(Request $request,Website $website)
    {
        $fields = checkApiValidationRules($request, Meta::validationRules(), $this);
        $meta = null;

        try {
            // unset($fields['details']);

            $fields['websiteId'] = $website->id;

            // creating
            $meta = Meta::create($fields);
            // $meta->details()->createMany($request->input('details'));

            // UPDATE
            $meta->details()->create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $meta->id,
                'entity' => "metas",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $meta->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new meta has been stored successfully.", ['meta' => $meta]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/meta/{meta}",
     *     tags={"Admin - meta"},
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
     *         name="meta",
     *         in="path",
     *         description="id of an existing meta.",
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
    public function show(Website $website, $meta)
    {
        $meta = Meta::with(['details'])->findOrFail($meta);

        return $this->dataResponse([
            'meta' => $meta
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/meta/{meta}",
     *     tags={"Admin - meta"},
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
     *         name="meta",
     *         in="path",
     *         description="id of an existing meta.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"page", "details"},
     *                 @OA\Property(property="page", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="details", type="array", @OA\Items(
     *                     @OA\Property(property="description", type="string", example=""),
     *                     @OA\Property(property="image", type="string", example=""),
     *                     @OA\Property(property="keywords", type="string", example=""),
     *                     @OA\Property(property="languageId", type="string", example=""),
     *                     @OA\Property(property="title", type="string", example=""),
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
     *         )
     *     ),
     *
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Website $website, $meta)
    {
        $fields = checkApiValidationRules($request, Meta::validationRules($meta), $this);

        $meta = Meta::with('details')->findOrFail($meta);

        $old_values = $meta->fieldValues();

        try {
            unset($fields['details']);

            // creating
            // $meta->update($fields);

            // syncing details
            // $details = $meta->details;
            // $newDetails = $request->input('details');
            // $remainDetails = [];

            // foreach ($newDetails as $newDetail) {
            //     $remainDetails[] = $newDetail['languageId'];
            //     $is_update = false;
            //     // checking if available then update
            //     foreach ($details as $detail)
            //     {
            //         if ($detail->languageId == $newDetail['languageId'])
            //         {
            //             $detail->update([
            //                 'description' => $newDetail['description'],
            //                 'image' => $newDetail['image'],
            //                 'keywords' => $newDetail['keywords'],
            //                 'languageId' => $newDetail['languageId'],
            //                 'title' => $newDetail['title'],
            //             ]);
            //             $is_update = true;
            //             break;
            //         }
            //     }

            //     // if no update available it is mean that is new and should be created
            //     if (!$is_update)
            //         $meta->details()->create($newDetail);
            // }

            // removing missed language ids
            // $meta->details()->whereNotIn('languageId', $remainDetails)->delete();

            // UPDATE
            $detail = $meta->details()->where('languageId', $fields['languageId'])->first();

            if($detail) {
                $detail->update($fields);
            } else {
                $meta->details()->create($fields);
            }

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $meta->id,
                'entity' => "metas",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $meta->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The meta has been updated successfully.", ['meta' => $meta]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/meta/{meta}",
     *     tags={"Admin - meta"},
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
     *         name="meta",
     *         in="path",
     *         description="id of an existing meta.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the meta has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, $meta)
    {
        $meta = Meta::findOrFail($meta);

        if ($meta->websiteId != $website->id)
            return $this->errorResponse("Bad request", [], 400);

        try {

            // deleting
            $meta->delete();

            // storing activity log
            ActivityLog::create([
                'metaId' => $website->id,
                'entityId' => $meta->id,
                'entity' => "metas",
                'action' => "delete",
                'oldValue' => $meta->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the meta has been deleted successfully.");
    }
}
