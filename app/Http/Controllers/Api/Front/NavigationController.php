<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\NavigationResource;
use App\Models\NavigationGroup;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/navigation/{website}",
     *     tags={"Front - navigations"},
     *     summary="auth",
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
    public function index(Request $request, $website_slug)
    {
        // get the navigations
        $navigations = NavigationGroup::where('websiteId', $request->websiteId)->get();

        return $this->dataResponse([
            'navigations' => NavigationResource::collection($navigations)
        ], null, false);
    }
}
