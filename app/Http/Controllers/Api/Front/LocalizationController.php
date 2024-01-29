<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Localization;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/pages/{website_slug}/localization/key/{key}",
     *     tags={"Front - localization"},
     *     summary="guest",
     * 
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="slug of an existing website.",
     *         required=true,
     *     ),
     * 
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="key which existing in the localizations table",
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
    public function localizationKey(Request $request, $website_slug, $key)
    {
        $key = Localization::where('websiteId', $request->websiteId)->where('key', $key)->with('locale_details')->firstOrFail();

        return $this->dataResponse([$key->key => ($key->locale_details[0]->value ?? null)]);
    }

    /**
     * @OA\Get(
     *     path="/api/localization/website/{website_slug}",
     *     tags={"Front - localization"},
     *     summary="guest",
     * 
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="slug of an existing website.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(type="object")
     *          )
     *      )
     * )
     */
    public function localizationWebsite(Request $request, $website_slug)
    {
        $keys = Localization::where('websiteId', $request->websiteId)->with('locale_details')->get();
        $data = [];

        // looping the keys
        foreach ($keys as $key)
            $data[$key->key] = $key->locale_details[0]->value ?? null;

        return $this->dataResponse($data);
    }
}
