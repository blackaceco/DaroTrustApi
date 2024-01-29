<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\MetaResource;
use App\Models\Meta;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/meta/{website_slug}/{page?}",
     *     tags={"Front - meta"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="slud of an existing website.",
     *         required=true,
     *     ),
     * 
     *     @OA\Parameter(
     *         name="page",
     *         in="path",
     *         description="an existing page value in the metas",
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
    public function index(Request $request, $website_slug, $page = null)
    {
        $metas = Meta::where('websiteId', $request->websiteId)->with(['details', 'website']);

        if ($page)
            $metas->where('page', $page);

        $metas = $metas->get();

        return $this->dataResponse([
            'meta' => MetaResource::collection($metas),
        ], null, false);
    }
}
